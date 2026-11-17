<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey        = config('midtrans.server_key');
        Config::$isProduction     = config('midtrans.is_production');
        Config::$isSanitized      = config('midtrans.is_sanitized');
        Config::$is3ds            = config('midtrans.is_3ds');
        Config::$curlOptions      = [CURLOPT_HTTPHEADER => []];
        Config::$overrideNotifUrl = url('/midtrans/webhook');
    }

    public static function syncAllPending(): void
    {
        $pendingList = Pembayaran::whereNotNull('order_id')
            ->whereHas('tagihan', fn($q) => $q->where('status', '!=', 'lunas'))
            ->where(function ($q) {
                $q->where('status', 'pending')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'gagal')->whereNull('midtrans_response');
                  });
            })
            ->get();

        if ($pendingList->isEmpty()) {
            return;
        }

        $service = new self();
        foreach ($pendingList as $p) {
            $service->syncTransactionStatus($p);
        }
    }

    /**
     * Sinkronisasi status transaksi langsung ke API Midtrans (sebagai cadangan
     * apabila webhook terlambat atau Notification URL di dashboard belum diubah).
     */
    public function syncTransactionStatus(Pembayaran $pembayaran): void
    {
        if (!$pembayaran->order_id || $pembayaran->status === 'lunas') {
            return;
        }

        try {
            $response = Transaction::status($pembayaran->order_id);
            if (!is_object($response)) {
                return;
            }

            $transactionStatus = $response->transaction_status ?? null;
            $fraudStatus       = $response->fraud_status ?? null;
            $paymentType       = $response->payment_type ?? '';

            if ($transactionStatus === 'capture') {
                $status = ($fraudStatus === 'accept') ? 'lunas' : 'gagal';
            } elseif ($transactionStatus === 'settlement') {
                $status = 'lunas';
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $status = 'gagal';
            } else {
                $status = 'pending';
            }

            $metode = match (true) {
                str_contains($paymentType, 'qris') || str_contains($paymentType, 'gopay') => 'qris',
                str_contains($paymentType, 'bank_transfer') || str_contains($paymentType, 'echannel') || str_contains($paymentType, 'permata') => 'va',
                default => $pembayaran->metode,
            };

            if ($status === 'pending' && $pembayaran->midtrans_response !== null) {
                return;
            }

            DB::transaction(function () use ($pembayaran, $status, $metode, $response) {
                $tagihan = $pembayaran->tagihan;

                $pembayaran->update([
                    'status'            => $status,
                    'metode'            => $metode,
                    'tgl_bayar'         => $status === 'lunas' ? ($pembayaran->tgl_bayar ?? now()) : $pembayaran->tgl_bayar,
                    'midtrans_response' => json_decode(json_encode($response), true),
                ]);

                if ($status === 'lunas') {
                    Pembayaran::where('tagihan_id', $tagihan->id)
                        ->where('id', '!=', $pembayaran->id)
                        ->where('status', 'pending')
                        ->update([
                            'status'  => 'gagal',
                            'catatan' => 'Tagihan telah lunas melalui transaksi lain',
                        ]);

                    $tagihan->update(['status' => 'lunas']);

                    $iuran = $tagihan->iuran;
                    $adaYangBelumLunas = $iuran->tagihan()
                        ->whereIn('status', ['belum_bayar', 'menunggu'])
                        ->exists();

                    if (!$adaYangBelumLunas) {
                        $iuran->update(['status' => 'selesai']);
                    }
                } elseif ($status === 'gagal' && $tagihan->status !== 'lunas') {
                    $masihAdaPending = $tagihan->pembayaran()
                        ->where('status', 'pending')
                        ->exists();

                    if (!$masihAdaPending) {
                        $tagihan->update(['status' => 'belum_bayar']);
                    }
                }
            });
        } catch (\Throwable $e) {
            // Jika order_id belum ada di Midtrans (404) karena warga belum memilih metode di Snap, biarkan saja
        }
    }

    public static function hitungBiayaAdmin(float|int $nominal, string $metode = 'qris'): int
    {
        return match ($metode) {
            'va'    => 4000,
            default => (int) ceil($nominal * 0.007), // QRIS 0,7%
        };
    }

    public function createTransaction(Tagihan $tagihan, string $metode = 'qris'): array
    {
        $warga        = $tagihan->warga;
        $orderId      = 'INV-' . $tagihan->id . '-' . time();
        $nominalIuran = (int) $tagihan->nominal;
        $biayaAdmin   = self::hitungBiayaAdmin($nominalIuran, $metode);
        $grossAmount  = $nominalIuran + $biayaAdmin;

        // Nama dari data warga. Email milik akun login, jadi hanya
        // disertakan kalau warga punya akun (email bersifat opsional di Midtrans)
        $customer = ['first_name' => $warga->nama_lengkap];

        if ($warga->user?->email) {
            $customer['email'] = $warga->user->email;
        }

        $enabledPayments = $metode === 'va'
            ? ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va']
            : ['gopay', 'other_qris'];

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $customer,
            'item_details' => [
                [
                    'id'       => 'IURAN-' . $tagihan->iuran_id,
                    'price'    => $nominalIuran,
                    'quantity' => 1,
                    'name'     => $tagihan->iuran->nama_iuran,
                ],
                [
                    'id'       => 'ADMIN-' . strtoupper($metode),
                    'price'    => $biayaAdmin,
                    'quantity' => 1,
                    'name'     => $metode === 'va' ? 'Biaya Admin Virtual Account' : 'Biaya Admin QRIS (0,7%)',
                ],
            ],
            'enabled_payments' => $enabledPayments,
        ];

        $snapToken = Snap::getSnapToken($params);

        return [
            'order_id'    => $orderId,
            'snap_token'  => $snapToken,
            'biaya_admin' => $biayaAdmin,
            'total_bayar' => $grossAmount,
        ];
    }
}