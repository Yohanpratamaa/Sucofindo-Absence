<div class="space-y-6">
    @php
        $data = $this->getData();
        $todayAttendance = $data['today_attendance'];
        $monthlyStats = $data['monthly_stats'];
        $weeklyAttendance = $data['weekly_attendance'];
        $recentAttendance = $data['recent_attendance'];
        $currentTime = $data['current_time'];
        $currentMonthName = $data['current_month_name'];
    @endphp

    <!-- Header with Quick Info -->
    <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-primary-100 mt-1">{{ $currentTime->isoFormat('dddd, D MMMM Y • HH:mm') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('filament.pegawai.pages.attendance-page') }}" 
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 px-4 py-2 rounded-lg font-medium transition-all duration-200">
                    <x-heroicon-o-camera class="w-4 h-4 inline mr-2" />
                    Absensi
                </a>
                <a href="{{ route('filament.pegawai.resources.my-all-attendances.index') }}" 
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 px-4 py-2 rounded-lg font-medium transition-all duration-200">
                    <x-heroicon-o-calendar-days class="w-4 h-4 inline mr-2" />
                    Riwayat
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Status -->
    @if($todayAttendance)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-success-500" />
                    Absensi Hari Ini
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Attendance Type -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Tipe Absensi</p>
                            <p class="text-lg font-bold text-blue-600">{{ $todayAttendance->attendance_type }}</p>
                        </div>
                        @if($todayAttendance->attendance_type === 'WFO')
                            <x-heroicon-o-building-office-2 class="w-8 h-8 text-blue-500" />
                        @else
                            <x-heroicon-o-map-pin class="w-8 h-8 text-blue-500" />
                        @endif
                    </div>
                </div>

                <!-- Check In -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">Check In</p>
                            <p class="text-lg font-bold text-green-600">
                                {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                            </p>
                        </div>
                        <x-heroicon-o-arrow-right-on-rectangle class="w-8 h-8 text-green-500" />
                    </div>
                </div>

                <!-- Check Out -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">Check Out</p>
                            <p class="text-lg font-bold text-red-600">
                                {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                            </p>
                            @if(!$todayAttendance->check_out)
                                <p class="text-xs text-red-500">≥ 15:00</p>
                            @endif
                        </div>
                        <x-heroicon-o-arrow-left-on-rectangle class="w-8 h-8 text-red-500" />
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">Status</p>
                            <div class="mt-1">
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
                            </div>
                        </div>
                        <x-heroicon-o-check-circle class="w-8 h-8 text-purple-500" />
                    </div>
                </div>
            </div>

            <!-- Progress for Dinas Luar -->
            @if($todayAttendance->attendance_type === 'Dinas Luar')
                @php
                    $progress = [
                        'pagi' => !is_null($todayAttendance->check_in),
                        'siang' => !is_null($todayAttendance->absen_siang),
                        'sore' => !is_null($todayAttendance->check_out),
                    ];
                    $completed = array_sum($progress);
                    $total = 3;
                @endphp
                <div class="mt-6 bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-700">Progress Dinas Luar</span>
                        <span class="text-sm text-gray-500">{{ $completed }}/{{ $total }} selesai</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                             style="width: {{ ($completed / $total) * 100 }}%"></div>
                    </div>
                    <div class="flex items-center mt-3 space-x-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['pagi'] ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['pagi'] ? 'text-green-700' : 'text-gray-500' }}">Pagi</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['siang'] ? 'bg-yellow-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['siang'] ? 'text-yellow-700' : 'text-gray-500' }}">Siang</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['sore'] ? 'bg-red-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['sore'] ? 'text-red-700' : 'text-gray-500' }}">Sore</span>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::section>
    @else
        <!-- No Attendance Today -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning-500" />
                    Belum Absen Hari Ini
                </div>
            </x-slot>

            <div class="text-center py-8">
                <div class="bg-gradient-to-br from-warning-50 to-orange-50 border border-warning-200 rounded-xl p-6 inline-block">
                    <x-heroicon-o-clock class="w-12 h-12 text-warning-500 mx-auto mb-3" />
                    <h3 class="text-lg font-semibold text-warning-800 mb-2">Anda belum melakukan absensi hari ini</h3>
                    <p class="text-warning-600 mb-4">Silakan lakukan absensi untuk mencatat kehadiran Anda</p>
                    <a href="{{ route('filament.pegawai.pages.attendance-page') }}" 
                       class="bg-warning-500 hover:bg-warning-600 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                        <x-heroicon-o-camera class="w-4 h-4 inline mr-2" />
                        Lakukan Absensi
                    </a>
                </div>
            </div>
        </x-filament::section>
    @endif

    <!-- Monthly Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Stats -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-primary-500" />
                    Statistik {{ $currentMonthName }}
                </div>
            </x-slot>

            @if($monthlyStats && $monthlyStats->total_days > 0)
                <div class="space-y-4">
                    <!-- Total Days -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-800">Total Hari Hadir</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $monthlyStats->total_days }}</p>
                            </div>
                            <x-heroicon-o-calendar-days class="w-8 h-8 text-blue-500" />
                        </div>
                    </div>

                    <!-- Performance Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-3">
                            <p class="text-xs font-medium text-green-800">Tepat Waktu</p>
                            <p class="text-lg font-bold text-green-600">{{ $monthlyStats->on_time ?? 0 }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-3">
                            <p class="text-xs font-medium text-yellow-800">Terlambat</p>
                            <p class="text-lg font-bold text-yellow-600">{{ $monthlyStats->late ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Attendance Type Distribution -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg p-3">
                            <p class="text-xs font-medium text-indigo-800">WFO</p>
                            <p class="text-lg font-bold text-indigo-600">{{ $monthlyStats->wfo_count ?? 0 }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-lg p-3">
                            <p class="text-xs font-medium text-emerald-800">Dinas Luar</p>
                            <p class="text-lg font-bold text-emerald-600">{{ $monthlyStats->dinas_luar_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <x-heroicon-o-chart-bar class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500">Belum ada data absensi bulan ini</p>
                </div>
            @endif
        </x-filament::section>

        <!-- Recent Attendance -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-primary-500" />
                    Riwayat Terbaru
                </div>
            </x-slot>

            @if($recentAttendance->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAttendance as $attendance)
                        <div class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full {{ $attendance->attendance_type === 'WFO' ? 'bg-blue-500' : 'bg-green-500' }}"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $attendance->created_at->isoFormat('dddd, D MMM') }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $attendance->attendance_type }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-mono text-gray-700">
                                        {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                        @if($attendance->check_out)
                                            - {{ $attendance->check_out->format('H:i') }}
                                        @endif
                                    </p>
                                    <x-filament::badge
                                        :color="match($attendance->status_kehadiran ?? '') {
                                            'Tepat Waktu' => 'success',
                                            'Terlambat' => 'warning',
                                            'Tidak Hadir' => 'danger',
                                            default => 'gray'
                                        }"
                                        size="xs"
                                    >
                                        {{ $attendance->status_kehadiran ?? 'Unknown' }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('filament.pegawai.resources.my-all-attendances.index') }}" 
                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Lihat Semua Riwayat →
                    </a>
                </div>
            @else
                <div class="text-center py-6">
                    <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500">Belum ada riwayat absensi</p>
                </div>
            @endif
        </x-filament::section>
    </div>

    <!-- Quick Actions -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="w-5 h-5 text-primary-500" />
                Aksi Cepat
            </div>
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('filament.pegawai.pages.attendance-page') }}" 
               class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="bg-blue-500 rounded-full p-3 w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-camera class="w-6 h-6 text-white" />
                    </div>
                    <h4 class="font-medium text-blue-900 text-sm">Absensi</h4>
                </div>
            </a>

            <a href="{{ route('filament.pegawai.resources.my-all-attendances.index') }}" 
               class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="bg-green-500 rounded-full p-3 w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                    </div>
                    <h4 class="font-medium text-green-900 text-sm">Riwayat</h4>
                </div>
            </a>

            <a href="{{ route('filament.pegawai.resources.my-izins.index') }}" 
               class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="bg-purple-500 rounded-full p-3 w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                    </div>
                    <h4 class="font-medium text-purple-900 text-sm">Izin</h4>
                </div>
            </a>

            <button onclick="window.location.reload()" 
                    class="group bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="bg-gray-500 rounded-full p-3 w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-arrow-path class="w-6 h-6 text-white" />
                    </div>
                    <h4 class="font-medium text-gray-900 text-sm">Refresh</h4>
                </div>
            </button>
        </div>
    </x-filament::section>
</div>
