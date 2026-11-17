<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BayarCepatController extends Controller
{
    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['iuran', 'warga']);

        abort_if(!$tagihan->warga->isAktif(), 403, 'Data warga ini sudah tidak aktif.');

        MidtransService::syncAllPending();
        $tagihan->refresh();

        // Sudah lunas: tampilkan info saja
        if ($tagihan->status === 'lunas') {
            return view('bayar.show', ['tagihan' => $tagihan, 'checkoutUrl' => null, 'lunas' => true]);
        }

        // Iuran sudah ditutup: link tidak lagi berlaku
        abort_if($tagihan->iuran->status !== 'aktif', 410, 'Iuran ini sudah ditutup.');

        // URL checkout juga ditandatangani, berlaku 2 jam sejak halaman dibuka
        $checkoutUrl = URL::temporarySignedRoute(
            'bayar.cepat.checkout',
            now()->addHours(2),
            ['tagihan' => $tagihan->id],
            absolute: false
        );

        return view('bayar.show', ['tagihan' => $tagihan, 'checkoutUrl' => $checkoutUrl, 'lunas' => false]);
    }

    public function checkout(Request $request, Tagihan $tagihan)
    {
        if (!$tagihan->warga->isAktif()) {
            return response()->json(['error' => 'Data warga ini sudah tidak aktif.'], 403);
        }

        if ($tagihan->status === 'lunas') {
            return response()->json(['error' => 'Tagihan sudah lunas.'], 400);
        }

        if ($tagihan->iuran->status !== 'aktif') {
            return response()->json(['error' => 'Iuran ini sudah ditutup.'], 410);
        }

        $metode = in_array($request->input('metode'), ['qris', 'va'], true)
            ? $request->input('metode')
            : 'qris';

        try {
            // Batalkan pembayaran pending lama tanpa menghapus barisnya agar
            // jika warga membayar ke nomor VA lama, order_id tetap dikenali webhook
            Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status', 'pending')
                ->update([
                    'status'  => 'gagal',
                    'catatan' => 'Digantikan transaksi baru',
                ]);

            $result = (new MidtransService())->createTransaction($tagihan, $metode);

            Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'warga_id'   => $tagihan->warga_id,
                'order_id'   => $result['order_id'],
                'metode'     => $metode,
                'nominal'    => $tagihan->nominal,
                'status'     => 'pending',
            ]);

            $tagihan->update(['status' => 'menunggu']);

            return response()->json(['snap_token' => $result['snap_token']]);

        } catch (\Exception $e) {
            Log::error('Bayar cepat error: ' . $e->getMessage());

            return response()->json(['error' => 'Gagal membuat transaksi. Coba lagi.'], 500);
        }
    }
}