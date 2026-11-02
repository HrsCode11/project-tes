<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <x-icon name="shield-check" class="w-8 h-8 text-white" />
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Aktivasi Akun</h1>
        <p class="text-gray-500 text-sm mt-1">Buat akun login untuk data warga yang sudah terdaftar</p>
    </div>

    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
        Data kamu harus sudah didaftarkan oleh pengurus RT. Masukkan NIK dan No. HP
        <strong>persis seperti yang kamu berikan ke pengurus</strong>.
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- NIK --}}
        <div>
            <x-input-label for="nik" :value="__('NIK (16 digit)')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text"
                name="nik" :value="old('nik')" required autofocus
                maxlength="16" inputmode="numeric" placeholder="16 digit NIK" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        {{-- No. HP --}}
        <div class="mt-4">
            <x-input-label for="no_hp" :value="__('No. HP (WhatsApp)')" />
            <x-text-input id="no_hp" class="block mt-1 w-full" type="tel"
                name="no_hp" :value="old('no_hp')" required
                inputmode="numeric" placeholder="081234567890" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email untuk login')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password"
                    name="password" required autocomplete="new-password" placeholder="Min. 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Ulangi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition">
                Aktifkan Akun
            </button>
        </div>

        <div class="mt-4 text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>