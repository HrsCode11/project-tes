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

    <div class="p-6">
        <p class="text-gray-500 text-sm">Halo,</p>
        <p class="font-bold text-gray-800 text-lg mb-5">{{ $tagihan->warga->nama_lengkap }}</p>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-5">
            <div class="text-gray-400 text-xs mb-1">Tagihan</div>
            <div class="font-semibold text-gray-800">{{ $tagihan->iuran->nama_iuran }}</div>
            <div class="font-bold font-mono text-2xl text-blue-500 my-2">
                Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
            </div>
            <div class="text-gray-400 text-xs">
                Batas bayar: {{ $tagihan->iuran->deadline->translatedFormat('d F Y') }}
            </div>
        </div>

        <div id="alert-box" class="hidden mb-4 px-4 py-3 rounded-xl text-sm"></div>

        @if($lunas)
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-4 text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                Tagihan ini sudah lunas. Terima kasih!
            </div>
        @else
            <button id="btn-bayar" onclick="bayar()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <x-icon name="credit-card" class="w-5 h-5" />
                Bayar via QRIS / Virtual Account
            </button>
            <p class="text-gray-400 text-xs text-center mt-3">
                Link ini khusus untuk tagihan di atas dan tidak memerlukan login.
            </p>
        @endif
    </div>
</div>

@unless($lunas)
<script>
    const checkoutUrl = {{ Js::from($checkoutUrl) }};

    function tampilkanPesan(tipe, pesan) {
        const box = document.getElementById('alert-box');
        box.className = 'mb-4 px-4 py-3 rounded-xl text-sm border ' + (tipe === 'error'
            ? 'bg-red-50 border-red-200 text-red-700'
            : 'bg-green-50 border-green-200 text-green-700');
        box.textContent = pesan;
    }

    function bayar() {
        const btn = document.getElementById('btn-bayar');
        btn.disabled = true;

        fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
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