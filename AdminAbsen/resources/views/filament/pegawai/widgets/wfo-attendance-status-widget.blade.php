<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Absensi WFO Hari Ini
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @if($this->getCanCheckIn())
                        <x-filament::badge color="warning">
                            Belum Check In
                        </x-filament::badge>
                    @elseif($this->getCanCheckOut())
                        <x-filament::badge color="info">
                            Belum Check Out
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="success">
                            Absensi Selesai
                        </x-filament::badge>
                    @endif
                </div>
            </div>

            <!-- Status Cards -->
            @php
                $todayAttendance = $this->getTodayAttendance();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Check In Status -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $todayAttendance && $todayAttendance->check_in ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Check In</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $todayAttendance && $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Check Out Status -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $todayAttendance && $todayAttendance->check_out ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Check Out</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $todayAttendance && $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Work Duration -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $todayAttendance && $todayAttendance->durasi_kerja !== '-' ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Durasi Kerja</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $todayAttendance ? $todayAttendance->durasi_kerja : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Message & Action Button -->
            <div class="text-center py-4">
                @if($this->getCanCheckIn())
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Anda belum melakukan check in hari ini. Gunakan kamera untuk selfie dan pastikan lokasi dalam radius kantor.
                    </p>
                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Pegawai\Pages\WfoAttendance::getUrl() }}"
                        color="success"
                        size="lg"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Check In WFO
                    </x-filament::button>
                @elseif($this->getCanCheckOut())
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Anda sudah check in. Jangan lupa check out saat selesai bekerja.
                    </p>
                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Pegawai\Pages\WfoAttendance::getUrl() }}"
                        color="warning"
                        size="lg"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1" />
                        </svg>
                        Check Out WFO
                    </x-filament::button>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Absensi hari ini sudah selesai. Terima kasih!
                    </p>
                    <div class="flex justify-center gap-4">
                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Pegawai\Resources\MyAttendanceResource::getUrl() }}"
                            color="gray"
                            outlined
                        >
                            Lihat Riwayat Absensi
                        </x-filament::button>
                    </div>
                @endif
            </div>

            <!-- Monthly Stats -->
            @php
                $stats = $this->getAttendanceStats();
            @endphp
            <div class="border-t pt-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Statistik Bulan Ini</h4>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['total_hadir'] }}</div>
                        <div class="text-xs text-gray-500">Total Hadir</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-primary-600">{{ $stats['total_wfo'] }}</div>
                        <div class="text-xs text-gray-500">WFO</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600">{{ $stats['total_terlambat'] }}</div>
                        <div class="text-xs text-gray-500">Terlambat</div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
