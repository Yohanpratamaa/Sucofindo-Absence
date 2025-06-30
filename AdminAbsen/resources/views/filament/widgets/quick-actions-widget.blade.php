<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="h-5 w-5 text-primary-600" />
                Aksi Cepat
            </div>
        </x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Tambahkan Data Pegawai Button -->
            <a href="{{ $createUrl }}"
               class="tambah-pegawai-btn inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white no-underline rounded-lg transition-all duration-200 hover:no-underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                Tambahkan Data Pegawai
            </a>

            <!-- Lihat Data Pegawai Button -->
            <a href="{{ $indexUrl }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 hover:no-underline focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 no-underline">
                <x-heroicon-o-users class="w-5 h-5" />
                Lihat Data Pegawai
            </a>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <p class="flex items-center gap-1">
                <x-heroicon-o-information-circle class="h-4 w-4" />
                Akses cepat untuk mengelola data pegawai
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
