<div class="space-y-6">
    @php
        $data = $this->getData();
        $todayAttendance = $data['today_attendance'];
        $monthlyStats = $data['monthly_stats'];
        $recentAttendance = $data['recent_attendance'];
        $currentTime = $data['current_time'];
        $currentMonthName = $data['current_month_name'];
    @endphp

    <!-- Absensi Hari Ini -->
    @if($todayAttendance)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Absensi Hari Ini
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tipe Absensi -->
                <div class="bg-white border rounded-lg p-4">
                    <div class="text-sm text-gray-600">Tipe Absensi</div>
                    <div class="text-lg font-semibold">{{ $todayAttendance->attendance_type }}</div>
                    @if($todayAttendance->attendance_type === 'WFO')
                        <x-heroicon-o-building-office-2 class="w-6 h-6 text-blue-500 mt-2" />
                    @else
                        <x-heroicon-o-map-pin class="w-6 h-6 text-green-500 mt-2" />
                    @endif
                </div>

                <!-- Check In -->
                <div class="bg-white border rounded-lg p-4">
                    <div class="text-sm text-gray-600">Check In</div>
                    <div class="text-lg font-semibold">
                        {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                    </div>
                    <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6 text-green-500 mt-2" />
                </div>

                <!-- Check Out -->
                <div class="bg-white border rounded-lg p-4">
                    <div class="text-sm text-gray-600">Check Out</div>
                    <div class="text-lg font-semibold">
                        {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                    </div>
                    <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 text-red-500 mt-2" />
                </div>

                <!-- Status -->
                <div class="bg-white border rounded-lg p-4">
                    <div class="text-sm text-gray-600">Status</div>
                    <x-filament::badge
                        :color="match($todayAttendance->status_kehadiran ?? '') {
                            'Tepat Waktu' => 'success',
                            'Terlambat' => 'warning',
                            'Tidak Hadir' => 'danger',
                            default => 'gray'
                        }"
                        size="sm"
                    >
                        {{ $todayAttendance->status_kehadiran ?? 'Belum Diketahui' }}
                    </x-filament::badge>
                    <x-heroicon-o-check-circle class="w-6 h-6 text-purple-500 mt-2" />
                </div>
            </div>
        </x-filament::section>
    @endif

    <!-- Statistik Bulanan -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5" />
                Statistik {{ $currentMonthName }}
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Total Hari Hadir -->
            <div class="bg-white border rounded-lg p-4 text-center">
                <div class="text-sm text-gray-600">Total Hari Hadir</div>
                <div class="text-2xl font-bold">{{ $monthlyStats->total_hari_hadir ?? 0 }}</div>
                <x-heroicon-o-calendar-days class="w-6 h-6 text-blue-500 mx-auto mt-2" />
            </div>

            <!-- Tepat Waktu -->
            <div class="bg-white border rounded-lg p-4 text-center">
                <div class="text-sm text-gray-600">Tepat Waktu</div>
                <div class="text-2xl font-bold">{{ $monthlyStats->tepat_waktu ?? 0 }}</div>
            </div>

            <!-- Terlambat -->
            <div class="bg-white border rounded-lg p-4 text-center">
                <div class="text-sm text-gray-600">Terlambat</div>
                <div class="text-2xl font-bold">{{ $monthlyStats->terlambat ?? 0 }}</div>
            </div>

            <!-- WFO -->
            <div class="bg-white border rounded-lg p-4 text-center">
                <div class="text-sm text-gray-600">WFO</div>
                <div class="text-2xl font-bold">{{ $monthlyStats->wfo ?? 0 }}</div>
            </div>

            <!-- Dinas Luar -->
            <div class="bg-white border rounded-lg p-4 text-center">
                <div class="text-sm text-gray-600">Dinas Luar</div>
                <div class="text-2xl font-bold">{{ $monthlyStats->dinas_luar ?? 0 }}</div>
            </div>
        </div>
    </x-filament::section>
</div>
