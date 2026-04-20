<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
            🏘️
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
        <p class="text-gray-500 text-sm mt-1">Daftarkan diri sebagai warga RT/RW</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" />
            <x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text"
                name="nama_lengkap" :value="old('nama_lengkap')" required autofocus
                placeholder="Nama sesuai KTP" />
            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
        </div>

        <!-- NIK & No RT -->
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="nik" :value="__('NIK (16 digit)')" />
                <x-text-input id="nik" class="block mt-1 w-full" type="text"
                    name="nik" :value="old('nik')" required maxlength="16"
                    placeholder="16 digit NIK" />
                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="no_rt" :value="__('No. RT')" />
                <x-text-input id="no_rt" class="block mt-1 w-full" type="text"
                    name="no_rt" :value="old('no_rt')" required
                    placeholder="cth: 04" />
                <x-input-error :messages="$errors->get('no_rt')" class="mt-2" />
            </div>
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required
                placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password & Konfirmasi -->
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password"
                    name="password" required placeholder="Min. 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required placeholder="Ulangi password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Info role -->
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
            ℹ️ Akun baru otomatis terdaftar sebagai <strong>Warga</strong>. Role dapat diubah oleh Ketua RT.
        </div>

        <!-- Tombol Daftar -->
        <div class="mt-6">
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition">
                Daftar Sekarang
            </button>
        </div>

        <!-- Link Login -->
        <div class="mt-4 text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">
                Masuk di sini
            </a>
        </div>

    </form>
</x-guest-layout>