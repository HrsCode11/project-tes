<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
            🏘️
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Masuk ke Sistem</h1>
        <p class="text-gray-500 text-sm mt-1">Sistem Informasi Keuangan RT/RW</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm text-blue-500 hover:underline">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Tombol Login -->
        <div class="mt-6">
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition">
                Masuk
            </button>
        </div>

        <!-- Link Register -->
        <div class="mt-4 text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-500 font-semibold hover:underline">
                Daftar di sini
            </a>
        </div>

        <!-- Role Info -->
        <div class="mt-6 flex justify-center gap-2">
            <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">Ketua RT</span>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Bendahara</span>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">Warga</span>
        </div>

    </form>
</x-guest-layout>