<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Absensi Dinas Luar Hari Ini
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @php
                        $progress = $this->getAttendanceProgress();
                    @endphp
                    @if($progress['percentage'] == 0)
                        <x-filament::badge color="warning">
                            Belum Absen
                        </x-filament::badge>
                    @elseif($progress['percentage'] == 100)
                        <x-filament::badge color="success">
                            Selesai ({{ $progress['percentage'] }}%)
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="info">
                            Progress: {{ $progress['percentage'] }}%
                        </x-filament::badge>
                    @endif
                </div>
            </div>

            <!-- Status Hari Ini -->
            @php
                $attendanceStatus = $this->getAttendanceStatus();
            @endphp

            @if($attendanceStatus['status'] !== 'Belum Absen')
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Status Kehadiran</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <x-filament::badge :color="$attendanceStatus['color']">
                                    {{ $attendanceStatus['status'] }}
                                </x-filament::badge>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $attendanceStatus['type'] ?? 'Dinas Luar' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                @if($attendanceStatus['check_in'])
                                    <p>✅ Pagi: {{ $attendanceStatus['check_in'] }}</p>
                                @endif
                                @if($attendanceStatus['absen_siang'])
                                    <p>✅ Siang: {{ $attendanceStatus['absen_siang'] }}</p>
                                @endif
                                @if($attendanceStatus['check_out'])
                                    <p>✅ Sore: {{ $attendanceStatus['check_out'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress Bar untuk Dinas Luar -->
            @if($progress['percentage'] > 0)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <span>Progress Absensi Dinas Luar</span>
                        <span>{{ $progress['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300"
                             style="width: {{ $progress['percentage'] }}%"></div>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-4 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $progress['pagi'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                @if($progress['pagi'])
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <span class="text-xs font-bold">1</span>
                                @endif
                            </div>
                            <span class="text-xs mt-1 {{ $progress['pagi'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Pagi</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $progress['siang'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                @if($progress['siang'])
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <span class="text-xs font-bold">2</span>
                                @endif
                            </div>
                            <span class="text-xs mt-1 {{ $progress['siang'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Siang</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $progress['sore'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                @if($progress['sore'])
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <span class="text-xs font-bold">3</span>
                                @endif
                            </div>
                            <span class="text-xs mt-1 {{ $progress['sore'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Sore</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Absen Pagi Status -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $attendanceStatus['check_in'] ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Pagi</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $attendanceStatus['check_in'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Absen Siang Status -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $attendanceStatus['absen_siang'] ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Siang</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $attendanceStatus['absen_siang'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Absen Sore Status -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 {{ $attendanceStatus['check_out'] ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Sore</p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $attendanceStatus['check_out'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics -->
            @php
                $stats = $this->getAttendanceStats();
            @endphp

            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                    Statistik Dinas Luar Bulan Ini
                </h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_hadir'] }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Hari Hadir</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['total_terlambat'] }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Terlambat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['total_lengkap'] }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Absen Lengkap</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                @if($this->getCanCheckInPagi())
                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}"
                        color="success"
                        size="sm"
                    >
                        <x-slot name="icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </x-slot>
                        Absen Pagi
                    </x-filament::button>
                @elseif($this->getCanCheckInSiang())
                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}"
                        color="warning"
                        size="sm"
                    >
                        <x-slot name="icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </x-slot>
                        Absen Siang
                    </x-filament::button>
                @elseif($this->getCanCheckOut())
                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}"
                        color="info"
                        size="sm"
                    >
                        <x-slot name="icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </x-slot>
                        Absen Sore
                    </x-filament::button>
                @endif

                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}"
                    color="gray"
                    size="sm"
                    outlined
                >
                    Lihat Detail
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
