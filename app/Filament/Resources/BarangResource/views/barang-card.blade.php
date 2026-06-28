@php
    $barang = $getRecord();
@endphp

<div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl shadow-sm ring-1 ring-gray-950/5">
    {{-- Gambar Placeholder --}}
    <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 010-2.828L14 8" />
        </svg>
    </div>

    {{-- Nama dan Kode Barang --}}
    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $barang->nama }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $barang->kode }}</p>

    {{-- Harga dan Stok --}}
    <div class="mt-3 flex justify-between items-center">
        <p class="text-lg font-semibold text-primary-600 dark:text-primary-500">
            {{ \Illuminate\Support\Number::currency($barang->harga, 'IDR') }}
        </p>
        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
            Stok: {{ $barang->stok }}
        </span>
    </div>
</div>