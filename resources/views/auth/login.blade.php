<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Keuangan RT/RW</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0F172A] flex items-center justify-center p-4">

    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl overflow-hidden shadow-2xl">

        {{-- KIRI — Branding --}}
        <div class="bg-[#1B2A4A] p-12 flex flex-col justify-between relative overflow-hidden">

            {{-- Background pattern --}}
            <div class="absolute inset-0 opacity-5">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Decorative circles --}}
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-blue-500/20 rounded-full"></div>
            <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-blue-400/10 rounded-full"></div>

            <div class="relative z-10">
                {{-- Logo --}}
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1.091M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg leading-tight">Keuangan RT/RW</div>
                        <div class="text-white/50 text-xs">Sistem Informasi Keuangan</div>
                    </div>
                </div>

                {{-- Headline --}}
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                        Kelola Keuangan<br>
                        <span class="text-blue-400">RT/RW</span> dengan<br>
                        Mudah & Transparan
                    </h1>
                    <p class="text-white/50 text-sm leading-relaxed">
                        Sistem pengelolaan keuangan berbasis web untuk RT 04,
                        Kelurahan Dukuh, Kecamatan Lembeyan, Kabupaten Magetan.
                    </p>
                </div>

                {{-- Features --}}
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM14.25 14.25h.008v.008h-.008v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z', 'text' => 'Kelola iuran & tagihan warga'],
                        ['icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z', 'text' => 'Pembayaran digital via Midtrans'],
                        ['icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z', 'text' => 'Laporan keuangan PDF & Excel'],
                    ] as $f)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-white/70 text-sm">{{ $f['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="relative z-10">
                <div class="flex items-center gap-2 mt-8">
                    <span class="w-6 h-6 bg-blue-500/30 rounded-full flex items-center justify-center text-xs text-blue-400 font-bold">K</span>
                    <span class="w-6 h-6 bg-blue-500/30 rounded-full flex items-center justify-center text-xs text-blue-400 font-bold">B</span>
                    <span class="w-6 h-6 bg-blue-500/30 rounded-full flex items-center justify-center text-xs text-blue-400 font-bold">W</span>
                    <span class="text-white/40 text-xs ml-1">3 role pengguna</span>
                </div>
            </div>

        </div>

        {{-- KANAN — Form Login --}}
        <div class="p-12 flex flex-col justify-center">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang</h2>
                <p class="text-gray-400 text-sm">Masuk ke akun kamu untuk melanjutkan</p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Error --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition bg-gray-50 focus:bg-white"
                            placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-500 hover:underline">
                            Lupa password?
                        </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password"
                            class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition bg-gray-50 focus:bg-white"
                            placeholder="Masukkan password" required>
                        {{-- Toggle password --}}
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500 cursor-pointer">
                    <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    Masuk ke Sistem
                </button>

            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-500 font-semibold hover:underline">Aktivasi akun di sini</a>
            </div>

            {{-- Role Info --}}
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-400 text-center mb-3">Akses sistem berdasarkan role</p>
                <div class="flex justify-center gap-2">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full">Ketua RT</span>
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">Bendahara</span>
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">Warga</span>
                </div>
            </div>

        </div>

    </div>

    <script>
        function togglePassword() {
            const input     = document.getElementById('password');
            const iconEye   = document.getElementById('icon-eye');
            const iconSlash = document.getElementById('icon-eye-slash');

            if (input.type === 'password') {
                input.type = 'text';
                iconEye.classList.add('hidden');
                iconSlash.classList.remove('hidden');
            } else {
                input.type = 'password';
                iconEye.classList.remove('hidden');
                iconSlash.classList.add('hidden');
            }
        }
    </script>

</body>
</html>