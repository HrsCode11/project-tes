<x-app-layout>
    <x-slot name="header">Dashboard Warga</x-slot>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-gray-700">
            Halo, <strong>{{ auth()->user()->nama_lengkap }}</strong>! 👋
        </p>
        <span class="mt-2 inline-block bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
            Warga
        </span>
    </div>
</x-app-layout>