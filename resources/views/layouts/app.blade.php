<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>
    {{-- Midtrans Snap.js --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex min-h-screen">

    {{-- OVERLAY (mobile) --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
        onclick="toggleSidebar()">
    </div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="w-64 bg-[#1B2A4A] flex flex-col fixed top-0 left-0 h-full z-30
        -translate-x-full lg:translate-x-0 transition-transform duration-300">

        {{-- Logo --}}
        <div class="px-5 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="home-modern" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="text-white font-bold text-sm leading-tight">Keuangan RT/RW</div>
                    <div class="text-white/40 text-xs">RT 04 · Kel. Dukuh · Magetan</div>
                </div>
                {{-- Tombol tutup sidebar di mobile --}}
                <button onclick="toggleSidebar()"
                    class="ml-auto text-white/40 hover:text-white lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Navigasi --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            {{-- Utama --}}
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Utama</div>
                <a href="{{ match(auth()->user()->role) {
                        'ketua_rt'  => route('ketua.dashboard'),
                        'bendahara' => route('bendahara.dashboard'),
                        default     => route('warga.dashboard'),
                    } }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs('*.dashboard') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="dashboard" class="w-5 h-5 flex-shrink-0" />
                    Dashboard
                </a>
            </div>

            {{-- Menu Keuangan — hanya Ketua RT & Bendahara --}}
            @if(auth()->user()->isKetuaRT() || auth()->user()->isBendahara())
            @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Keuangan</div>
                <a href="{{ route($prefix . '.iuran.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs($prefix . '.iuran.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="receipt" class="w-5 h-5 flex-shrink-0" />
                    Kelola Iuran
                </a>
                <a href="{{ route($prefix . '.pembayaran.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs($prefix . '.pembayaran.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="credit-card" class="w-5 h-5 flex-shrink-0" />
                    Pembayaran
                </a>
                <a href="{{ route($prefix . '.pemasukan.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs($prefix . '.pemasukan.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="arrow-down-tray" class="w-5 h-5 flex-shrink-0" />
                    Pemasukan
                </a>
                <a href="{{ route($prefix . '.pengeluaran.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs($prefix . '.pengeluaran.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="arrow-up-tray" class="w-5 h-5 flex-shrink-0" />
                    Pengeluaran
                </a>
            </div>
            @endif

            {{-- Menu Pembayaran — hanya Warga --}}
            @if(auth()->user()->isWarga())
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Iuran</div>
                <a href="{{ route('warga.pembayaran.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs('warga.pembayaran.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="credit-card" class="w-5 h-5 flex-shrink-0" />
                    Pembayaran
                </a>
            </div>
            @endif

            {{-- Menu Laporan --}}
            @if(auth()->user()->isKetuaRT() || auth()->user()->isBendahara())
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Laporan</div>
                <a href="{{ route(auth()->user()->isKetuaRT() ? 'ketua.laporan.index' : 'bendahara.laporan.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs('*.laporan.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="document-chart" class="w-5 h-5 flex-shrink-0" />
                    Ekspor Laporan
                </a>
            </div>
            @endif

            {{-- Data Warga: Ketua RT & Bendahara --}}
            @if(auth()->user()->isKetuaRT() || auth()->user()->isBendahara())
            @php $prefixWarga = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Data</div>
                <a href="{{ route($prefixWarga . '.warga.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs($prefixWarga . '.warga.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="users" class="w-5 h-5 flex-shrink-0" />
                    Kelola Warga
                </a>
            </div>
            @endif

            {{-- Menu Pengaturan — hanya Ketua RT --}}
            @if(auth()->user()->isKetuaRT())
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Pengaturan</div>
                <a href="{{ route('ketua.akun.index') }}"
                    onclick="closeSidebarOnMobile()"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs('ketua.akun.*') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <x-icon name="shield-check" class="w-5 h-5 flex-shrink-0" />
                    Kelola Akun
                </a>
            </div>
            @endif

        </nav>

        {{-- User Info --}}
        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 bg-white/5 rounded-xl px-3 py-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-semibold truncate">
                        {{ auth()->user()->nama_lengkap ?? 'User' }}
                    </div>
                    <div class="text-white/40 text-[11px] capitalize">
                        {{ str_replace('_', ' ', auth()->user()->role ?? '') }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white" title="Logout">
                        <x-icon name="power" class="w-5 h-5" />
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="lg:ml-64 flex-1 min-w-0 w-full flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Hamburger button (mobile only) --}}
                <button onclick="toggleSidebar()"
                    class="lg:hidden text-gray-500 hover:text-gray-700 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-lg lg:text-xl font-bold text-gray-800 truncate">{{ $title ?? 'Dashboard' }}</h1>
                    @if(isset($subtitle) && $subtitle)
                    <p class="text-gray-500 text-xs lg:text-sm mt-0.5 hidden sm:block truncate">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="text-xs lg:text-sm text-gray-500 hidden md:block flex-shrink-0">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 min-w-0 w-full p-4 lg:p-8">
            {{ $slot }}
        </main>

    </div>

</div>

{{-- MODAL ALERT (Success / Error / Info) --}}
<div id="global-alert-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center transform transition-all">
        <div id="global-alert-icon" class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center"></div>
        <h3 id="global-alert-title" class="text-lg font-bold text-gray-800 mb-2"></h3>
        <p id="global-alert-message" class="text-sm text-gray-600 mb-6 leading-relaxed"></p>
        <button type="button" id="global-alert-btn" onclick="closeModalAlert()"
            class="w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition">
            Mengerti
        </button>
    </div>
</div>

{{-- MODAL KONFIRMASI (Hapus / Tindakan) --}}
<div id="global-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center transform transition-all">
        <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 mx-auto mb-4 flex items-center justify-center">
            <x-icon name="exclamation-triangle" class="w-7 h-7" />
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
        <p id="global-confirm-message" class="text-sm text-gray-600 mb-6 leading-relaxed"></p>
        <div class="flex gap-3">
            <button type="button" onclick="closeModalConfirm()"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                Batal
            </button>
            <button type="button" id="global-confirm-yes"
                class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebarOnMobile() {
        if (window.innerWidth < 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    // ── Modal Alert Global ──
    let alertCallback = null;

    function showModalAlert(type, message, onClose = null) {
        const modal = document.getElementById('global-alert-modal');
        const icon  = document.getElementById('global-alert-icon');
        const title = document.getElementById('global-alert-title');
        const msg   = document.getElementById('global-alert-message');
        const btn   = document.getElementById('global-alert-btn');

        alertCallback = onClose;
        msg.textContent = message;

        if (type === 'success') {
            icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-green-100 text-green-600';
            icon.innerHTML = `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>`;
            title.textContent = 'Berhasil';
            btn.className = 'w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition bg-green-500 hover:bg-green-600';
        } else if (type === 'warning') {
            icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-orange-100 text-orange-600';
            icon.innerHTML = `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>`;
            title.textContent = 'Informasi';
            btn.className = 'w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition bg-orange-500 hover:bg-orange-600';
        } else {
            icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-red-100 text-red-600';
            icon.innerHTML = `<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>`;
            title.textContent = 'Perhatian';
            btn.className = 'w-full font-semibold py-2.5 px-4 rounded-xl text-white text-sm transition bg-red-500 hover:bg-red-600';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalAlert() {
        const modal = document.getElementById('global-alert-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (typeof alertCallback === 'function') {
            const cb = alertCallback;
            alertCallback = null;
            cb();
        }
    }

    // ── Modal Konfirmasi Global untuk Form [data-konfirmasi] ──
    let formToConfirm = null;

    function openModalConfirm(form, message) {
        formToConfirm = form;
        document.getElementById('global-confirm-message').textContent = message;
        const modal = document.getElementById('global-confirm-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalConfirm() {
        formToConfirm = null;
        const modal = document.getElementById('global-confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('global-confirm-yes').addEventListener('click', function () {
        if (formToConfirm) {
            formToConfirm.dataset.confirmed = 'true';
            formToConfirm.submit();
        }
    });

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.dataset && form.dataset.konfirmasi && form.dataset.confirmed !== 'true') {
            e.preventDefault();
            openModalConfirm(form, form.dataset.konfirmasi);
        }
    });

    // Tutup modal saat klik area luar atau tekan Escape
    document.getElementById('global-alert-modal').addEventListener('click', function (e) {
        if (e.target === this) closeModalAlert();
    });
    document.getElementById('global-confirm-modal').addEventListener('click', function (e) {
        if (e.target === this) closeModalConfirm();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModalAlert();
            closeModalConfirm();
        }
    });

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            showModalAlert('success', @json(session('success')));
        });
    @elseif(session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            showModalAlert('error', @json(session('error')));
        });
    @endif
</script>

</body>
</html>