<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#1B2A4A] flex flex-col fixed top-0 left-0 h-full z-30">

        {{-- Logo --}}
        <div class="px-5 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center text-lg">
                    🏘️
                </div>
                <div>
                    <div class="text-white font-bold text-sm leading-tight">Keuangan RT/RW</div>
                    <div class="text-white/40 text-xs">RT 04 · Kel. Dukuh · Magetan</div>
                </div>
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
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition mb-1
                    {{ request()->routeIs('*.dashboard') ? 'bg-blue-500 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                    <span class="w-5 text-center">📊</span> Dashboard
                </a>
            </div>

            {{-- Keuangan --}}
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Keuangan</div>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">🧾</span> Kelola Iuran
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">💳</span> Pembayaran
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">📥</span> Pemasukan
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">📤</span> Pengeluaran
                </a>
            </div>

            {{-- Laporan --}}
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Laporan</div>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">📄</span> Ekspor Laporan
                </a>
            </div>

            {{-- Pengaturan - hanya Ketua RT --}}
            @if(auth()->user()->role === 'ketua_rt')
            <div class="mb-5">
                <div class="text-white/30 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Pengaturan</div>
                <a href="{{ route('ketua.users.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/10 hover:text-white text-sm font-medium transition mb-1">
                    <span class="w-5 text-center">👥</span> Kelola User
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
                    <button type="submit" class="text-white/40 hover:text-white text-lg" title="Logout">⏻</button>
                </form>
            </div>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="ml-64 flex-1 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ $subtitle ?? '' }}</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>