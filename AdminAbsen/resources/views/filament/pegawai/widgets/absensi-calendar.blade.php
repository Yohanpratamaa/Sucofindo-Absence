<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Kalender Absensi - {{ $currentMonth }}
        </x-slot>

        <div class="space-y-4">
            <!-- Statistics Summary -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg text-center">
                    <div class="text-green-600 dark:text-green-400 font-semibold text-lg">{{ $stats['total_hadir'] }}</div>
                    <div class="text-green-500 dark:text-green-300 text-xs">Tepat Waktu</div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg text-center">
                    <div class="text-yellow-600 dark:text-yellow-400 font-semibold text-lg">{{ $stats['total_terlambat'] }}</div>
                    <div class="text-yellow-500 dark:text-yellow-300 text-xs">Terlambat</div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg text-center">
                    <div class="text-orange-600 dark:text-orange-400 font-semibold text-lg">{{ $stats['total_dinas_luar'] }}</div>
                    <div class="text-orange-500 dark:text-orange-300 text-xs">Dinas Luar</div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg text-center">
                    <div class="text-blue-600 dark:text-blue-400 font-semibold text-lg">{{ $stats['total_izin'] }}</div>
                    <div class="text-blue-500 dark:text-blue-300 text-xs">Izin</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg text-center">
                    <div class="text-red-600 dark:text-red-400 font-semibold text-lg">{{ $stats['total_tidak_hadir'] }}</div>
                    <div class="text-red-500 dark:text-red-300 text-xs">Tidak Hadir</div>
                </div>
            </div>

            <!-- Calendar Header -->
            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                <div class="p-2">Min</div>
                <div class="p-2">Sen</div>
                <div class="p-2">Sel</div>
                <div class="p-2">Rab</div>
                <div class="p-2">Kam</div>
                <div class="p-2">Jum</div>
                <div class="p-2">Sab</div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
                @php
                    $firstDay = \Carbon\Carbon::parse($calendarData[0]['date'])->startOfMonth();
                    $startWeekday = $firstDay->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

                    // Add empty cells for days before the first day of the month
                    for ($i = 0; $i < $startWeekday; $i++) {
                        echo '<div class="p-2"></div>';
                    }
                @endphp

                @foreach ($calendarData as $day)
                    @php
                        $bgClass = match($day['status']) {
                            'hadir_tepat_waktu' => 'bg-green-100 dark:bg-green-900/30 border-green-300',
                            'terlambat' => 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-300',
                            'dinas_luar' => 'bg-orange-100 dark:bg-orange-900/30 border-orange-300',
                            'izin' => 'bg-blue-100 dark:bg-blue-900/30 border-blue-300',
                            'tidak_hadir' => 'bg-red-100 dark:bg-red-900/30 border-red-300',
                            default => 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'
                        };

                        $textClass = match($day['status']) {
                            'hadir_tepat_waktu' => 'text-green-800 dark:text-green-200',
                            'terlambat' => 'text-yellow-800 dark:text-yellow-200',
                            'dinas_luar' => 'text-orange-800 dark:text-orange-200',
                            'izin' => 'text-blue-800 dark:text-blue-200',
                            'tidak_hadir' => 'text-red-800 dark:text-red-200',
                            default => 'text-gray-600 dark:text-gray-400'
                        };

                        $icon = match($day['status']) {
                            'hadir_tepat_waktu' => '✓',
                            'terlambat' => '⏰',
                            'dinas_luar' => '📍',
                            'izin' => '📄',
                            'tidak_hadir' => '✗',
                            default => ''
                        };
                    @endphp

                    <div class="relative aspect-square">
                        <div class="h-full border rounded-lg p-1 {{ $bgClass }} {{ $day['is_today'] ? 'ring-2 ring-primary-500' : '' }}">
                            <div class="flex flex-col h-full">
                                <div class="text-xs {{ $textClass }} font-medium">
                                    {{ $day['day'] }}
                                </div>
                                @if ($icon)
                                    <div class="flex-1 flex items-center justify-center text-xs">
                                        {{ $icon }}
                                    </div>
                                @endif
                                @if ($day['attendance'])
                                    <div class="text-xs {{ $textClass }} text-center">
                                        @if ($day['attendance']->check_in)
                                            {{ \Carbon\Carbon::parse($day['attendance']->check_in)->format('H:i') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-xs">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-200 border border-green-300 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Tepat Waktu</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yellow-200 border border-yellow-300 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Terlambat</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-orange-200 border border-orange-300 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Dinas Luar</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-200 border border-blue-300 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Izin</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-200 border border-red-300 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Tidak Hadir</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
