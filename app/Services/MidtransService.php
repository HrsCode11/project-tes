<?php

namespace App\Services;

use App\Models\Tagihan;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey        = config('midtrans.server_key');
        Config::$isProduction     = config('midtrans.is_production');
        Config::$isSanitized      = config('midtrans.is_sanitized');
        Config::$is3ds            = config('midtrans.is_3ds');
        Config::$curlOptions      = [CURLOPT_HTTPHEADER => []];
        Config::$overrideNotifUrl = rtrim(config('app.url'), '/') . '/midtrans/webhook';
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