<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="subtitle">Selamat datang di sistem keuangan RT/RW</x-slot>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-gray-700">
            Halo, <strong>{{ auth()->user()->nama_lengkap }}</strong>! 👋
        </p>
        <p class="text-gray-500 text-sm mt-1 capitalize">
            Role: {{ str_replace('_', ' ', auth()->user()->role) }}
        </p>
    </div>

</x-app-layout>