<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class WargaPembayaranController extends Controller
{
    // Data warga milik akun yang sedang login
    private function wargaLogin(): Warga
    {
        $warga = auth()->user()->warga;

        abort_if(!$warga, 403, 'Akun kamu belum terhubung ke data warga. Hubungi pengurus RT.');

        return $warga;
    }

    public function index()
    {
        $warga = $this->wargaLogin();

        $tagihanBelumLunas = Tagihan::with('iuran')
            ->where('warga_id', $warga->id)
            ->whereIn('status', ['belum_bayar', 'menunggu'])
            ->whereHas('iuran', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('created_at', 'desc')
            ->get();

        $riwayatBayar = Pembayaran::with('tagihan.iuran')
            ->where('warga_id', $warga->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('warga.pembayaran', compact('tagihanBelumLunas', 'riwayatBayar'));
    }

    public function checkout(Tagihan $tagihan)
    {
        $warga = $this->wargaLogin();

        // Tagihan harus milik warga yang sedang login
        if ($tagihan->warga_id !== $warga->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($tagihan->status === 'lunas') {
            return response()->json(['error' => 'Tagihan sudah lunas.'], 400);
        }

        if ($tagihan->iuran->status !== 'aktif') {
            return response()->json(['error' => 'Iuran ini sudah ditutup.'], 400);
        }

        try {
            // Hapus pembayaran pending lama agar tidak konflik dengan transaksi baru
            Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status', 'pending')
                ->delete();

            $result = (new MidtransService())->createTransaction($tagihan);

            Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'warga_id'   => $tagihan->warga_id,
                'order_id'   => $result['order_id'],
                'metode'     => 'qris',
                'nominal'    => $tagihan->nominal,
                'status'     => 'pending',
            ]);

            $tagihan->update(['status' => 'menunggu']);

            return response()->json([
                'snap_token' => $result['snap_token'],
                'order_id'   => $result['order_id'],
            ]);

        } catch (\Exception $e) {
            // Detail error hanya ditulis ke log, tidak dikirim ke browser
            Log::error('Midtrans checkout error: ' . $e->getMessage());

            return response()->json(['error' => 'Gagal membuat transaksi. Coba lagi.'], 500);
        }
    }

    /**
     * Webhook dari Midtrans — dipanggil otomatis oleh server Midtrans
     */
    public function webhook(Request $request)
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new Notification();

            $orderId           = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus       = $notification->fraud_status;
            $paymentType       = $notification->payment_type;

            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                return response()->json(['message' => 'Order not found'], 404);
            }

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
                str_contains($paymentType, 'qris')          => 'qris',
                str_contains($paymentType, 'bank_transfer') => 'va',
                default                                     => 'qris',
            };

            DB::transaction(function () use ($pembayaran, $status, $metode, $notification) {

                $pembayaran->update([
                    'status'            => $status,
                    'metode'            => $metode,
                    'tgl_bayar'         => now(),
                    'midtrans_response' => (array) $notification,
                ]);

                $tagihan = $pembayaran->tagihan;

                if ($status === 'lunas') {
                    $tagihan->update(['status' => 'lunas']);

                    $iuran = $tagihan->iuran;
                    $adaYangBelumLunas = $iuran->tagihan()
                        ->whereIn('status', ['belum_bayar', 'menunggu'])
                        ->exists();

                    if (!$adaYangBelumLunas) {
                        $iuran->update(['status' => 'selesai']);
                    }

                } elseif ($status === 'gagal') {
                    $tagihan->update(['status' => 'belum_bayar']);
                }
            });

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}