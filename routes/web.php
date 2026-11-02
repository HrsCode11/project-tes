<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\WargaPembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\BayarCepatController;
use App\Http\Controllers\PengingatController;


Route::get('/', function () {
    return redirect('/login');
});

// ── KETUA RT ──────────────────────────────────────────────
Route::middleware(['auth', 'role:ketua_rt'])
    ->prefix('ketua')
    ->name('ketua.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'ketua'])
            ->name('dashboard');

        // Kelola User — hanya ketua RT
        Route::resource('akun', AkunController::class)->except(['show']);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::resource('warga', WargaController::class)->except(['show']);
    });

// ── BENDAHARA & KETUA RT (fitur keuangan) ─────────────────
// Kedua role bisa akses fitur keuangan
// Prefix disesuaikan per role lewat redirect
Route::middleware(['auth', 'role:bendahara'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'bendahara'])
            ->name('dashboard');

        Route::resource('iuran', IuranController::class);
        Route::resource('pembayaran', PembayaranController::class)
            ->only(['index', 'create', 'store']);
        Route::resource('pemasukan', PemasukanController::class)
            ->except(['show']);
        Route::resource('pengeluaran', PengeluaranController::class)
            ->except(['show']);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::resource('warga', WargaController::class)
            ->except(['show'])
            ->parameters(['warga' => 'warga']);
        Route::get('iuran/{iuran}/pengingat', [PengingatController::class, 'index'])
            ->name('iuran.pengingat');
        Route::post('tagihan/{tagihan}/pengingat', [PengingatController::class, 'tandai'])
            ->name('tagihan.pengingat');
    });

// Ketua RT bisa akses fitur keuangan via prefix 'ketua'
Route::middleware(['auth', 'role:ketua_rt'])
    ->prefix('ketua')
    ->name('ketua.')
    ->group(function () {
        Route::resource('iuran', IuranController::class);
        Route::resource('pembayaran', PembayaranController::class)
            ->only(['index', 'create', 'store']);
        Route::resource('pemasukan', PemasukanController::class)
            ->except(['show']);
        Route::resource('pengeluaran', PengeluaranController::class)
            ->except(['show']);

        Route::resource('warga', WargaController::class)
            ->except(['show'])
            ->parameters(['warga' => 'warga']);
        Route::get('iuran/{iuran}/pengingat', [PengingatController::class, 'index'])
            ->name('iuran.pengingat');
        Route::post('tagihan/{tagihan}/pengingat', [PengingatController::class, 'tandai'])
            ->name('tagihan.pengingat');
    });

// Warga
Route::middleware(['auth', 'role:warga'])
    ->prefix('warga')
    ->name('warga.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'warga'])
            ->name('dashboard');

        Route::get('/pembayaran', [WargaPembayaranController::class, 'index'])
            ->name('pembayaran.index');

        // Buat transaksi Midtrans
        Route::post('/pembayaran/{tagihan}/checkout', [WargaPembayaranController::class, 'checkout'])
            ->name('pembayaran.checkout');

        

        // Webhook dari Midtrans (tidak perlu auth)
    });

// Link bayar dari WhatsApp. Dilindungi signed URL, tanpa login
Route::middleware('signed:relative')->group(function () {
    Route::get('/bayar/{tagihan}', [BayarCepatController::class, 'show'])
        ->name('bayar.cepat');
    Route::post('/bayar/{tagihan}/checkout', [BayarCepatController::class, 'checkout'])
        ->name('bayar.cepat.checkout');
});

// Webhook Midtrans — di luar middleware auth karena dipanggil dari server Midtrans
Route::post('/midtrans/webhook', [WargaPembayaranController::class, 'webhook'])
    ->name('midtrans.webhook');

    

require __DIR__.'/auth.php';