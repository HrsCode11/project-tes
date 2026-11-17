<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bayar Iuran — Keuangan RT/RW</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">

    <div class="bg-[#1B2A4A] px-6 py-5 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
            <x-icon name="home-modern" class="w-5 h-5 text-white" />
        </div>
        <div>
            <div class="text-white font-bold text-sm">Keuangan RT/RW</div>
            <div class="text-white/50 text-xs">RT 04 · Kel. Dukuh · Magetan</div>
        </div>
    </div>

    <div class="p-5">
        <p class="text-gray-500 text-xs">Halo,</p>
        <p class="font-bold text-gray-800 text-base mb-3">{{ $tagihan->warga->nama_lengkap }}</p>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-400 text-[11px]">Tagihan</div>
                    <div class="font-semibold text-sm text-gray-800">{{ $tagihan->iuran->nama_iuran }}</div>
                </div>
                <div class="text-right">
                    <div class="font-bold font-mono text-lg text-blue-500">
                        Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="text-gray-400 text-[11px] mt-1">
                Batas bayar: {{ $tagihan->iuran->deadline->translatedFormat('d F Y') }}
            </div>
        </div>

        @if($lunas)
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                Tagihan ini sudah lunas. Terima kasih!
            </div>
        @else
            @php
                $nominalIuran = (int) $tagihan->nominal;
                $adminQris    = \App\Services\MidtransService::hitungBiayaAdmin($nominalIuran, 'qris');
                $adminVa      = \App\Services\MidtransService::hitungBiayaAdmin($nominalIuran, 'va');
            @endphp

            <div class="space-y-2 mb-4">
                <label class="block cursor-pointer">
                    <input type="radio" name="metode" value="qris" class="sr-only peer" checked>
                    <div class="border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50/60 rounded-xl px-3.5 py-2.5 transition flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-xs text-gray-800 inline-flex items-center gap-1.5">
                                <x-icon name="qr-code" class="w-4 h-4 text-gray-500" />
                                <span>QRIS / GoPay</span>
                            </div>
                            <div class="text-[11px] text-gray-500">+Admin 0,7% (Rp {{ number_format($adminQris, 0, ',', '.') }})</div>
                        </div>
                        <div class="font-mono font-bold text-xs text-blue-600">
                            Rp {{ number_format($nominalIuran + $adminQris, 0, ',', '.') }}
                        </div>
                    </div>
                </label>

                <label class="block cursor-pointer">
                    <input type="radio" name="metode" value="va" class="sr-only peer">
                    <div class="border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50/60 rounded-xl px-3.5 py-2.5 transition flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-xs text-gray-800 inline-flex items-center gap-1.5">
                                <x-icon name="credit-card" class="w-4 h-4 text-gray-500" />
                                <span>Virtual Account</span>
                            </div>
                            <div class="text-[11px] text-gray-500">+Admin Rp {{ number_format($adminVa, 0, ',', '.') }}</div>
                        </div>
                        <div class="font-mono font-bold text-xs text-blue-600">
                            Rp {{ number_format($nominalIuran + $adminVa, 0, ',', '.') }}
                        </div>
                    </div>
                </label>
            </div>

            <button id="btn-bayar" onclick="bayar()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                <x-icon name="credit-card" class="w-4 h-4" />
                Bayar Sekarang
            </button>
            <p class="text-gray-400 text-[11px] text-center mt-2.5">
                Link ini khusus untuk tagihan di atas dan tidak memerlukan login.
            </p>
        @endif
    </div>
</div>

{{-- MODAL ALERT --}}
<div id="bayar-alert-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
        <div id="bayar-alert-icon" class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center"></div>
        <h3 id="bayar-alert-title" class="text-lg font-bold text-gray-800 mb-2"></h3>
        <p id="bayar-alert-message" class="text-sm text-gray-600 mb-6 leading-relaxed"></p>
        <button type="button" id="bayar-alert-btn" onclick="tutupModalPesan()"
            class="w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition">
            Mengerti
        </button>
    </div>
</div>

@unless($lunas)
<script>
    const checkoutUrl = {{ Js::from($checkoutUrl) }};

    function tampilkanPesan(tipe, pesan) {
        const modal = document.getElementById('bayar-alert-modal');
        const icon  = document.getElementById('bayar-alert-icon');
        const title = document.getElementById('bayar-alert-title');
        const msg   = document.getElementById('bayar-alert-message');
        const btn   = document.getElementById('bayar-alert-btn');

        msg.textContent = pesan;

        if (tipe === 'error') {
            icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-red-100 text-red-600';
            icon.innerHTML = `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>`;
            title.textContent = 'Perhatian';
            btn.className = 'w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition bg-red-500 hover:bg-red-600';
        } else {
            icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-green-100 text-green-600';
            icon.innerHTML = `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>`;
            title.textContent = 'Berhasil';
            btn.className = 'w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition bg-green-500 hover:bg-green-600';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalPesan() {
        const modal = document.getElementById('bayar-alert-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function bayar() {
        const btn    = document.getElementById('btn-bayar');
        const metode = document.querySelector('input[name="metode"]:checked')?.value || 'qris';
        btn.disabled = true;

        fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ metode: metode }),
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));

            // Semua respons selain sukses dianggap gagal, lengkap dengan kode HTTP-nya
            if (!res.ok || !data.snap_token) {
                throw new Error('HTTP ' + res.status + ': ' + (data.error || data.message || 'respons tidak dikenali'));
            }
            return data;
        })
        .then(data => {
            snap.pay(data.snap_token, {
                onSuccess: () => tampilkanPesan('ok', 'Pembayaran berhasil. Terima kasih!'),
                onPending: () => tampilkanPesan('ok', 'Selesaikan pembayaran kamu sesuai instruksi.'),
                onError:   () => { tampilkanPesan('error', 'Pembayaran gagal. Silakan coba lagi.'); btn.disabled = false; },
                onClose:   () => { btn.disabled = false; },
            });
        })
        .catch(err => {
            console.error(err);
            // Detail teknis ini sementara, untuk pengujian. Kembalikan ke pesan umum setelah selesai.
            tampilkanPesan('error', err.message);
            btn.disabled = false;
        });
    }
</script>
@endunless

</body>
</html>