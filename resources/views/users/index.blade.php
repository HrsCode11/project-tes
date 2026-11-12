<x-app-layout>
    <x-slot name="title">Kelola User</x-slot>
    <x-slot name="subtitle">Manajemen pengguna sistem RT/RW</x-slot>

    {{-- Panel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <div class="font-bold text-gray-800">Daftar User</div>
                <div class="text-gray-400 text-xs mt-0.5">Total {{ $users->total() }} user terdaftar</div>
            </div>
            <a href="{{ route('ketua.users.create') }}" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                ＋ Tambah User
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. RT</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $user->nama_lengkap }}</div>
                                    <div class="text-gray-400 text-xs">{{ $user->email }}</div>
                                    <div class="text-gray-400 text-xs">{{ $user->no_hp ? '+' . $user->no_hp : 'No. HP belum diisi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-sm text-gray-600">{{ $user->nik }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-semibold text-sm">{{ $user->no_rt }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                            $roleColors = [
                                'ketua_rt' => 'bg-purple-100 text-purple-700',
                                'bendahara' => 'bg-green-100 text-green-700',
                                'warga' => 'bg-blue-100 text-blue-700',
                            ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ str_replace('_', ' ', ucwords($user->role)) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ketua.users.edit', $user) }}" 
                                    class="text-blue-500 hover:text-blue-700 font-semibold text-sm">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('ketua.users.destroy', $user) }}" method="POST" 
                                    data-konfirmasi="Yakin hapus user {{ $user->nama_lengkap }} ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                            Belum ada user terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif

    </div>

</x-app-layout>