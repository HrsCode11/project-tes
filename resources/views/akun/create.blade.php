<x-app-layout>
    <x-slot name="title">Buat Akun</x-slot>
    <x-slot name="subtitle">Buatkan akun login untuk warga yang sudah terdata</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Buat Akun</div>
            </div>

            <form action="{{ route('ketua.akun.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="warga_id" id="warga_id" value="{{ old('warga_id') }}">

                {{-- Pilih warga --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Warga</label>

                    @if($wargaTersedia->isEmpty())
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
                        Semua warga aktif sudah punya akun. Tambahkan data warga baru di Kelola Warga jika diperlukan.
                    </div>
                    @else
                    <div id="kotak-cari">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon name="magnifying-glass" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input type="text" id="cari" autocomplete="off"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50 focus:bg-white"
                                placeholder="Ketik nama warga...">
                        </div>
                        <div id="hasil" class="hidden mt-2 border border-gray-200 rounded-xl shadow-lg bg-white max-h-60 overflow-y-auto"></div>
                    </div>

                    <div id="terpilih" class="hidden flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                        <div id="terpilih-avatar" class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div id="terpilih-nama" class="font-semibold text-sm text-gray-800"></div>
                            <div id="terpilih-info" class="text-gray-500 text-xs"></div>
                        </div>
                        <button type="button" onclick="gantiWarga()" class="text-sm text-blue-500 font-semibold hover:underline">Ganti</button>
                    </div>
                    @endif

                    @error('warga_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email untuk login</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="contoh@email.com" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password awal</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Minimal 8 karakter" required>
                    <p class="text-gray-400 text-xs mt-1">Sampaikan langsung kepada yang bersangkutan.</p>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Role --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                    <select name="role"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="warga"     {{ old('role', 'warga') === 'warga'     ? 'selected' : '' }}>Warga</option>
                        <option value="bendahara" {{ old('role') === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                        <option value="ketua_rt"  {{ old('role') === 'ketua_rt'  ? 'selected' : '' }}>Ketua RT</option>
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Simpan Akun
                    </button>
                    <a href="{{ route('ketua.akun.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @if($wargaTersedia->isNotEmpty())
    <script>
        const daftarWarga = {{ Js::from($wargaTersedia) }};

        const inputCari = document.getElementById('cari');
        const kotakHasil = document.getElementById('hasil');

        // Escape agar nama berisi karakter HTML tidak dieksekusi
        function esc(teks) {
            const d = document.createElement('div');
            d.textContent = teks ?? '';
            return d.innerHTML;
        }

        inputCari.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            if (q.length < 2) { kotakHasil.classList.add('hidden'); return; }

            const cocok = daftarWarga.filter(w =>
                w.nama_lengkap.toLowerCase().includes(q) || w.nik.includes(q)
            ).slice(0, 8);

            kotakHasil.innerHTML = cocok.length === 0
                ? '<div class="px-4 py-5 text-center text-gray-400 text-sm">Warga tidak ditemukan</div>'
                : cocok.map(w => `
                    <div onclick="pilihWarga(${w.id})"
                        class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            ${esc(w.nama_lengkap.charAt(0).toUpperCase())}
                        </div>
                        <div>
                            <div class="font-semibold text-sm text-gray-800">${esc(w.nama_lengkap)}</div>
                            <div class="text-gray-400 text-xs">RT ${esc(w.no_rt)} · NIK ${esc(w.nik)}</div>
                        </div>
                    </div>`).join('');

            kotakHasil.classList.remove('hidden');
        });

        function pilihWarga(id) {
            const w = daftarWarga.find(x => x.id === id);
            if (!w) return;

            document.getElementById('warga_id').value = w.id;
            document.getElementById('terpilih-avatar').textContent = w.nama_lengkap.charAt(0).toUpperCase();
            document.getElementById('terpilih-nama').textContent = w.nama_lengkap;
            document.getElementById('terpilih-info').textContent =
                'RT ' + w.no_rt + ' · NIK ' + w.nik + (w.no_hp ? ' · +' + w.no_hp : ' · No. HP belum diisi');

            kotakHasil.classList.add('hidden');
            document.getElementById('kotak-cari').classList.add('hidden');
            document.getElementById('terpilih').classList.remove('hidden');
        }

        function gantiWarga() {
            document.getElementById('warga_id').value = '';
            inputCari.value = '';
            document.getElementById('terpilih').classList.add('hidden');
            document.getElementById('kotak-cari').classList.remove('hidden');
            inputCari.focus();
        }

        // Pulihkan pilihan jika validasi gagal dan halaman dimuat ulang
        const idLama = parseInt(document.getElementById('warga_id').value);
        if (idLama) pilihWarga(idLama);
    </script>
    @endif
</x-app-layout>