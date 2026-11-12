<x-app-layout>
    <x-slot name="title">Kelola Akun</x-slot>
    <x-slot name="subtitle">Akun login, role, dan status akses pengguna</x-slot>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-wrap gap-3">
            <div>
                <div class="font-bold text-gray-800">Daftar Akun</div>
                <div class="text-gray-400 text-xs mt-0.5">{{ $akun->total() }} akun terdaftar</div>
            </div>
            <a href="{{ route('ketua.akun.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                <x-icon name="plus" class="w-4 h-4" /> Buat Akun
            </a>
        </div>

        <form method="GET" class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400" />
                </div>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 focus:bg-white"
                    placeholder="Cari nama atau email...">
            </div>
            <select name="role"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50">
                <option value="">Semua role</option>
                <option value="ketua_rt"  {{ request('role') === 'ketua_rt'  ? 'selected' : '' }}>Ketua RT</option>
                <option value="bendahara" {{ request('role') === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                <option value="warga"     {{ request('role') === 'warga'     ? 'selected' : '' }}>Warga</option>
            </select>
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">Cari</button>
            @if(request()->hasAny(['q', 'role']))
            <a href="{{ route('ketua.akun.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($akun as $a)
                    @php
                        $nama = $a->warga?->nama_lengkap ?? $a->nama_lengkap;
                        $roleColor = match($a->role) {
                            'ketua_rt'  => 'bg-purple-100 text-purple-700',
                            'bendahara' => 'bg-green-100 text-green-700',
                            default     => 'bg-blue-100 text-blue-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-sm text-gray-800">
                                        {{ $nama }}
                                        @if($a->id === auth()->id())<span class="text-gray-400 font-normal">(kamu)</span>@endif
                                    </div>
                                    <div class="text-gray-400 text-xs">{{ $a->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleColor }}">
                                {{ str_replace('_', ' ', ucwords($a->role, '_')) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $a->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                // Tidak bisa menghapus akun sendiri, atau akun yang pernah mencatat iuran/pemasukan/pengeluaran
                                $bisaHapus = $a->id !== auth()->id()
                                    && $a->iuran_dibuat_count === 0
                                    && $a->pemasukan_dicatat_count === 0
                                    && $a->pengeluaran_dicatat_count === 0;
                            @endphp
                            <div class="flex items-center gap-4">
                                <a href="{{ route('ketua.akun.edit', $a) }}"
                                    class="text-blue-500 hover:underline text-sm font-semibold inline-flex items-center gap-1">
                                    <x-icon name="pencil" class="w-3.5 h-3.5" /> Edit
                                </a>

                                @if($bisaHapus)
                                <form action="{{ route('ketua.akun.destroy', $a) }}" method="POST"
                                    data-konfirmasi="Hapus akun {{ $nama }}? Data warganya tetap tersimpan, tetapi akun ini tidak bisa login lagi.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:underline text-sm font-semibold inline-flex items-center gap-1">
                                        <x-icon name="trash" class="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada akun yang cocok</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($akun->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $akun->links() }}</div>
        @endif
    </div>
</x-app-layout>