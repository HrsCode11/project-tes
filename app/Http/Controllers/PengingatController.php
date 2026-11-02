<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\URL;

class PengingatController extends Controller
{
    public function index(Iuran $iuran)
    {
        $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';

        // Status iuran baru otomatis "ditutup" saat halaman Kelola Iuran dibuka,
        // jadi deadline dicek langsung di sini. Link bayar berlaku sampai akhir hari deadline.
        if ($iuran->status !== 'aktif' || $iuran->deadline->copy()->endOfDay()->isPast()) {
            return redirect()->route("{$prefix}.iuran.show", $iuran)
                ->with('error', 'Pengingat hanya bisa dikirim untuk iuran aktif yang belum melewati deadline.');
        }

        $daftar = $iuran->tagihan()
            ->with('warga')
            ->where('status', 'belum_bayar')
            ->get()
            ->filter(fn($t) => $t->warga->isAktif())
            // Yang belum diingatkan tampil lebih dulu, lalu urut nama
            ->sortBy(fn($t) => [$t->pengingat_dikirim_at ? 1 : 0, $t->warga->nama_lengkap])
            ->values()
            ->map(function ($t) use ($iuran) {
                $t->wa_url = $this->waUrl($t, $iuran);
                return $t;
            });

        return view('iuran.pengingat', compact('iuran', 'daftar'));
    }

    // Dipanggil saat tombol "Kirim via WA" ditekan, untuk mencatat waktunya
    public function tandai(Tagihan $tagihan)
    {
        if ($tagihan->status === 'belum_bayar') {
            $tagihan->update(['pengingat_dikirim_at' => now()]);
        }

        return response()->json([
            'waktu' => $tagihan->pengingat_dikirim_at?->translatedFormat('d M, H:i'),
        ]);
    }

    private function waUrl(Tagihan $tagihan, Iuran $iuran): string
    {
        $path = URL::temporarySignedRoute(
            'bayar.cepat',
            $iuran->deadline->copy()->endOfDay(),
            ['tagihan' => $tagihan->id],
            absolute: false
        );

        $link = rtrim(config('app.url'), '/') . $path;

        $pesan = "Halo {$tagihan->warga->nama_lengkap},\n\n"
            . "Pengurus RT 04 mengingatkan bahwa iuran *{$iuran->nama_iuran}* sebesar Rp "
            . number_format($tagihan->nominal, 0, ',', '.') . " belum dibayar.\n"
            . "Batas pembayaran: " . $iuran->deadline->translatedFormat('d F Y') . "\n\n"
            . "Bayar langsung lewat link berikut, tanpa perlu login:\n{$link}\n\n"
            . "Jika sudah membayar, abaikan pesan ini. Terima kasih.";

        // Tanpa nomor, WhatsApp membuka pilihan kontak
        $tujuan = $tagihan->warga->no_hp
            ? "https://wa.me/{$tagihan->warga->no_hp}"
            : 'https://wa.me/';

        return $tujuan . '?text=' . rawurlencode($pesan);
    }
}