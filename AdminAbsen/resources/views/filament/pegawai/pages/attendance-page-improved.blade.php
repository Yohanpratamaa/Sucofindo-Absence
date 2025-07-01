<x-filament-panels::page>
    @php
        // Define variables at the top level using component methods
        $currentAction = $this->getCurrentAction();
        $actionTitle = $this->getActionTitle();

        // Get properties from the Livewire component
        $canCheckInPagi = $this->canCheckInPagi;
        $canCheckInSiang = $this->canCheckInSiang;
        $canCheckOut = $this->canCheckOut;
        $canCheckIn = $this->canCheckIn;
        $todayAttendance = $this->todayAttendance;
        $attendanceType = $this->attendanceType;
        $canChangeType = $this->canChangeAttendanceType();
        $lockedType = $this->getLockedAttendanceType();
        $timeInfo = $this->getTimeWindowInfo();
    @endphp

    <!-- Header Section with Quick Stats -->
    @if($todayAttendance)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-primary-500" />
                    Status Absensi Hari Ini
                </div>
            </x-slot>
            
            <x-slot name="description">
                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} • 
                <x-filament::badge 
                    :color="$attendanceType === 'WFO' ? 'primary' : 'success'" 
                    size="sm"
                >
                    {{ $attendanceType }}
                </x-filament::badge>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Check In Status -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">Check In</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                            </p>
                        </div>
                        <x-heroicon-o-arrow-right-on-rectangle class="w-8 h-8 text-green-500" />
                    </div>
                </div>

                <!-- Check In Siang (Dinas Luar) -->
                @if($attendanceType === 'Dinas Luar')
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Check In Siang</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ $todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') : '-' }}
                                </p>
                            </div>
                            <x-heroicon-o-sun class="w-8 h-8 text-yellow-500" />
                        </div>
                    </div>
                @endif

                <!-- Check Out Status -->
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">Check Out</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                            </p>
                            @if($todayAttendance->check_in && !$todayAttendance->check_out)
                                <p class="text-xs text-red-500 mt-1">≥ 15:00</p>
                            @endif
                        </div>
                        <x-heroicon-o-arrow-left-on-rectangle class="w-8 h-8 text-red-500" />
                    </div>
                </div>

                <!-- Status Kehadiran -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Status</p>
                            <div class="mt-1">
                                <x-filament::badge
                                    :color="match($todayAttendance->status_kehadiran ?? '') {
                                        'Tepat Waktu' => 'success',
                                        'Terlambat' => 'warning',
                                        'Tidak Hadir' => 'danger',
                                        default => 'gray'
                                    }"
                                    size="lg"
                                >
                                    {{ $todayAttendance->status_kehadiran ?? 'Belum Diketahui' }}
                                </x-filament::badge>
                            </div>
                        </div>
                        <x-heroicon-o-check-circle class="w-8 h-8 text-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Progress Bar untuk Dinas Luar -->
            @if($attendanceType === 'Dinas Luar')
                @php
                    $progress = $this->getAttendanceProgress();
                @endphp
                <div class="mt-6 bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress Absensi</span>
                        <span class="text-sm text-gray-500">{{ $progress['completed'] }}/{{ $progress['total'] }} selesai</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-300" 
                             style="width: {{ ($progress['completed'] / $progress['total']) * 100 }}%"></div>
                    </div>
                    <div class="flex items-center mt-3 space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['check_in'] ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['check_in'] ? 'text-green-700' : 'text-gray-500' }}">Pagi</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['check_in_siang'] ? 'bg-yellow-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['check_in_siang'] ? 'text-yellow-700' : 'text-gray-500' }}">Siang</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $progress['check_out'] ? 'bg-red-500' : 'bg-gray-300' }} mr-2"></div>
                            <span class="{{ $progress['check_out'] ? 'text-red-700' : 'text-gray-500' }}">Sore</span>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::section>
    @endif

    <!-- Attendance Type Selection -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-primary-500" />
                Pilih Tipe Absensi
            </div>
        </x-slot>

        <x-slot name="description">
            Pilih tipe absensi yang sesuai dengan aktivitas kerja Anda hari ini
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Attendance Type Selector -->
            <div class="space-y-4">
                <x-filament::input.wrapper>
                    <x-filament::input.select
                        wire:model.live="attendanceType"
                        :disabled="!$canChangeType"
                        class="w-full"
                    >
                        <option value="WFO">🏢 WFO (Work From Office)</option>
                        <option value="Dinas Luar">🚗 Dinas Luar</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                @if(!$canChangeType && $lockedType)
                    <x-filament::section collapsed>
                        <x-slot name="heading">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-lock-closed class="w-4 h-4 text-warning-500" />
                                Tipe Terkunci
                            </div>
                        </x-slot>
                        <div class="bg-warning-50 border border-warning-200 rounded-lg p-3">
                            <p class="text-sm text-warning-800">
                                Tipe absensi terkunci karena Anda sudah melakukan absensi <strong>{{ $lockedType }}</strong> hari ini.
                            </p>
                        </div>
                    </x-filament::section>
                @endif
            </div>

            <!-- Attendance Type Info Card -->
            <div class="bg-gradient-to-br {{ $attendanceType === 'WFO' ? 'from-blue-50 to-indigo-100' : 'from-green-50 to-emerald-100' }} 
                        border {{ $attendanceType === 'WFO' ? 'border-blue-200' : 'border-green-200' }} rounded-xl p-6">
                <div class="flex items-start gap-4">
                    @if($attendanceType === 'WFO')
                        <div class="bg-blue-500 rounded-full p-3">
                            <x-heroicon-o-building-office-2 class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-blue-900 flex items-center gap-2">
                                Mode WFO Aktif
                                @if(!$canChangeType)
                                    <x-filament::badge color="warning" size="sm">Terkunci</x-filament::badge>
                                @endif
                            </h3>
                            <p class="text-sm text-blue-700 mt-1">
                                Absensi dengan verifikasi lokasi kantor
                            </p>
                            <div class="mt-3 space-y-1 text-xs text-blue-600">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-m-check-circle class="w-3 h-3" />
                                    <span>Check In: Kapan saja</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-heroicon-m-check-circle class="w-3 h-3" />
                                    <span>Check Out: Setelah jam 15:00</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-500 rounded-full p-3">
                            <x-heroicon-o-map-pin class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-green-900 flex items-center gap-2">
                                Mode Dinas Luar Aktif
                                @if(!$canChangeType)
                                    <x-filament::badge color="warning" size="sm">Terkunci</x-filament::badge>
                                @endif
                            </h3>
                            <p class="text-sm text-green-700 mt-1">
                                Absensi 3 waktu dengan pembatasan jam
                            </p>
                            <div class="mt-3 space-y-1 text-xs text-green-600">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-m-check-circle class="w-3 h-3" />
                                    <span>Pagi: Kapan saja</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-heroicon-m-check-circle class="w-3 h-3" />
                                    <span>Siang: 12:00 - 14:59</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-heroicon-m-check-circle class="w-3 h-3" />
                                    <span>Sore: Setelah jam 15:00</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Time Windows for Dinas Luar -->
    @if($attendanceType === 'Dinas Luar')
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-primary-500" />
                    Jadwal Absensi Dinas Luar
                </div>
            </x-slot>

            <x-slot name="description">
                Waktu saat ini: <strong>{{ $timeInfo['current_time'] }}</strong>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Pagi -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-blue-800">Absensi Pagi</h4>
                            <p class="text-sm text-blue-600">Kapan saja</p>
                        </div>
                        <x-heroicon-o-sun class="w-8 h-8 text-blue-500" />
                    </div>
                    <div class="mt-3">
                        <x-filament::badge color="primary" size="sm">
                            Selalu Aktif
                        </x-filament::badge>
                    </div>
                </div>

                <!-- Siang -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-yellow-800">Absensi Siang</h4>
                            <p class="text-sm text-yellow-600">
                                {{ $timeInfo['siang_window']['start'] }} - {{ $timeInfo['siang_window']['end'] }}
                            </p>
                        </div>
                        <x-heroicon-o-sun class="w-8 h-8 text-yellow-500" />
                    </div>
                    <div class="mt-3">
                        <x-filament::badge 
                            :color="$timeInfo['siang_window']['is_active'] ? 'success' : 'gray'" 
                            size="sm"
                        >
                            {{ $timeInfo['siang_window']['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                        </x-filament::badge>
                    </div>
                </div>

                <!-- Sore -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-red-800">Absensi Sore</h4>
                            <p class="text-sm text-red-600">
                                {{ $timeInfo['sore_window']['start'] }} - Selesai
                            </p>
                        </div>
                        <x-heroicon-o-moon class="w-8 h-8 text-red-500" />
                    </div>
                    <div class="mt-3">
                        <x-filament::badge 
                            :color="$timeInfo['sore_window']['is_active'] ? 'success' : 'gray'" 
                            size="sm"
                        >
                            {{ $timeInfo['sore_window']['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                        </x-filament::badge>
                    </div>
                </div>
            </div>

            <!-- Time Window Alerts -->
            @if($currentAction === 'siang' && !$timeInfo['siang_window']['is_active'])
                <x-filament::section class="mt-4">
                    <div class="bg-warning-50 border border-warning-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-warning-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-medium text-warning-800">Belum Waktu Absensi Siang</h4>
                                <p class="text-sm text-warning-700 mt-1">
                                    Absensi siang hanya dapat dilakukan antara {{ $timeInfo['siang_window']['start'] }} - {{ $timeInfo['siang_window']['end'] }}. 
                                    Silakan tunggu hingga waktu yang tepat.
                                </p>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            @if($currentAction === 'sore' && !$timeInfo['sore_window']['is_active'])
                <x-filament::section class="mt-4">
                    <div class="bg-warning-50 border border-warning-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-warning-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-medium text-warning-800">Belum Waktu Absensi Sore</h4>
                                <p class="text-sm text-warning-700 mt-1">
                                    Absensi sore hanya dapat dilakukan mulai pukul {{ $timeInfo['sore_window']['start'] }}. 
                                    Silakan tunggu hingga waktu yang tepat.
                                </p>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </x-filament::section>
    @endif

    <!-- Main Attendance Action Section -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-camera class="w-5 h-5 text-primary-500" />
                {{ $actionTitle }}
            </div>
        </x-slot>

        <x-slot name="description">
            @if($attendanceType === 'WFO')
                @if($canCheckIn)
                    Ambil foto selfie untuk melakukan check in
                @elseif($canCheckOut)
                    Ambil foto selfie untuk melakukan check out (tersedia setelah jam 15:00)
                @else
                    @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                        Check out hanya dapat dilakukan setelah jam 15:00. Waktu saat ini: {{ \Carbon\Carbon::now()->format('H:i') }}
                    @else
                        Absensi WFO hari ini sudah selesai
                    @endif
                @endif
            @else
                @if($canCheckInPagi)
                    Ambil foto selfie untuk absensi pagi (dinas luar)
                @elseif($canCheckInSiang)
                    Ambil foto selfie untuk absensi siang (12:00-14:59)
                @elseif($canCheckOut)
                    Ambil foto selfie untuk absensi sore (≥15:00)
                @else
                    Absensi dinas luar hari ini sudah selesai atau belum waktunya
                @endif
            @endif
        </x-slot>

        <div class="max-w-2xl mx-auto">
            <!-- Camera Section -->
            @if(($attendanceType === 'WFO' && ($canCheckIn || $canCheckOut)) || 
                ($attendanceType === 'Dinas Luar' && ($canCheckInPagi || $canCheckInSiang || $canCheckOut)))
                
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-6">
                    <!-- Camera Preview -->
                    <div class="relative bg-black rounded-xl overflow-hidden mb-6" style="aspect-ratio: 4/3;">
                        <video id="camera" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        
                        <!-- Overlay UI -->
                        <div class="absolute inset-0 pointer-events-none">
                            <!-- Corner guides -->
                            <div class="absolute top-4 left-4 w-8 h-8 border-l-2 border-t-2 border-white/70"></div>
                            <div class="absolute top-4 right-4 w-8 h-8 border-r-2 border-t-2 border-white/70"></div>
                            <div class="absolute bottom-4 left-4 w-8 h-8 border-l-2 border-b-2 border-white/70"></div>
                            <div class="absolute bottom-4 right-4 w-8 h-8 border-r-2 border-b-2 border-white/70"></div>
                            
                            <!-- Center guide -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="w-32 h-32 border-2 border-white/50 rounded-full"></div>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2">
                                <div class="bg-black/70 text-white px-4 py-2 rounded-full text-sm">
                                    Posisikan wajah di dalam lingkaran
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                        <!-- Capture Button -->
                        <button 
                            type="button" 
                            id="capture-btn"
                            class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 
                                   text-white font-semibold py-4 px-8 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200
                                   flex items-center gap-3 text-lg"
                        >
                            <x-heroicon-o-camera class="w-6 h-6" />
                            Ambil Foto
                        </button>

                        <!-- Location Toggle -->
                        <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" id="location-toggle" checked class="sr-only">
                            <div class="relative">
                                <div class="block bg-gray-300 w-14 h-8 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                            </div>
                            <span class="font-medium">Ambil Lokasi</span>
                        </label>
                    </div>

                    <!-- Status Messages -->
                    <div id="status-messages" class="mt-4 space-y-2"></div>
                </div>

                <!-- Photo Preview Section -->
                <div id="photo-preview" class="hidden mt-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Foto</h3>
                        <div class="flex flex-col lg:flex-row gap-6">
                            <div class="flex-1">
                                <img id="captured-photo" alt="Captured photo" class="w-full rounded-lg shadow-md">
                            </div>
                            <div class="lg:w-1/3 space-y-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Waktu</label>
                                    <p id="photo-time" class="text-lg font-mono bg-gray-50 px-3 py-2 rounded-md"></p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Lokasi</label>
                                    <p id="photo-location" class="text-sm bg-gray-50 px-3 py-2 rounded-md"></p>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button 
                                        type="button" 
                                        id="retake-btn"
                                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                    >
                                        Ulangi
                                    </button>
                                    <button 
                                        type="button" 
                                        id="submit-btn"
                                        class="flex-1 bg-success-500 hover:bg-success-600 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                    >
                                        Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Action Available -->
                <div class="text-center py-12">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-check-circle class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Aksi Tersedia</h3>
                    <p class="text-gray-600">
                        @if($attendanceType === 'WFO')
                            @if($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
                                Absensi WFO hari ini sudah lengkap.
                            @elseif($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                                Check out tersedia setelah jam 15:00.
                            @else
                                Silakan refresh halaman jika ada masalah.
                            @endif
                        @else
                            @if($todayAttendance)
                                Semua absensi dinas luar hari ini sudah selesai.
                            @else
                                Silakan mulai dengan absensi pagi.
                            @endif
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </x-filament::section>

    <!-- Quick Actions -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="w-5 h-5 text-primary-500" />
                Aksi Cepat
            </div>
        </x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('filament.pegawai.resources.my-all-attendances.index') }}" 
               class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-500 rounded-lg p-2 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-calendar-days class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h4 class="font-medium text-blue-900">Riwayat Absensi</h4>
                        <p class="text-sm text-blue-600">Lihat data absensi</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.pegawai.resources.my-izins.index') }}" 
               class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="bg-green-500 rounded-lg p-2 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-document-text class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h4 class="font-medium text-green-900">Pengajuan Izin</h4>
                        <p class="text-sm text-green-600">Ajukan izin/cuti</p>
                    </div>
                </div>
            </a>

            <button 
                onclick="window.location.reload()" 
                class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-500 rounded-lg p-2 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-arrow-path class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h4 class="font-medium text-purple-900">Refresh Halaman</h4>
                        <p class="text-sm text-purple-600">Perbarui status</p>
                    </div>
                </div>
            </button>
        </div>
    </x-filament::section>

    @push('scripts')
    <script>
        // Enhanced camera and attendance functionality
        let currentStream = null;
        let capturedPhotoData = null;
        let currentLatitude = null;
        let currentLongitude = null;

        // Initialize camera when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCamera();
            setupLocationToggle();
            setupCaptureButton();
        });

        function initializeCamera() {
            const video = document.getElementById('camera');
            const constraints = {
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    currentStream = stream;
                    video.srcObject = stream;
                    showStatusMessage('Kamera siap digunakan', 'success');
                })
                .catch(function(error) {
                    console.error('Error accessing camera:', error);
                    showStatusMessage('Gagal mengakses kamera: ' + error.message, 'error');
                });
        }

        function setupLocationToggle() {
            const toggle = document.getElementById('location-toggle');
            const dot = toggle.parentElement.querySelector('.dot');
            
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    dot.style.transform = 'translateX(100%)';
                    dot.style.backgroundColor = '#10B981';
                    getCurrentLocation();
                } else {
                    dot.style.transform = 'translateX(0%)';
                    dot.style.backgroundColor = '#fff';
                    currentLatitude = null;
                    currentLongitude = null;
                }
            });

            // Initialize
            if (toggle.checked) {
                getCurrentLocation();
            }
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        currentLatitude = position.coords.latitude;
                        currentLongitude = position.coords.longitude;
                        showStatusMessage('Lokasi berhasil diambil', 'success');
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        showStatusMessage('Gagal mengambil lokasi: ' + error.message, 'warning');
                    }
                );
            } else {
                showStatusMessage('Geolocation tidak didukung browser ini', 'error');
            }
        }

        function setupCaptureButton() {
            const captureBtn = document.getElementById('capture-btn');
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');

            captureBtn.addEventListener('click', function() {
                if (!video.srcObject) {
                    showStatusMessage('Kamera belum siap', 'error');
                    return;
                }

                // Capture photo
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);

                // Convert to base64
                capturedPhotoData = canvas.toDataURL('image/jpeg', 0.8);

                // Show preview
                showPhotoPreview();
            });
        }

        function showPhotoPreview() {
            const previewSection = document.getElementById('photo-preview');
            const capturedPhoto = document.getElementById('captured-photo');
            const photoTime = document.getElementById('photo-time');
            const photoLocation = document.getElementById('photo-location');

            // Set photo
            capturedPhoto.src = capturedPhotoData;

            // Set time
            const now = new Date();
            photoTime.textContent = now.toLocaleTimeString('id-ID');

            // Set location
            if (currentLatitude && currentLongitude) {
                photoLocation.textContent = `${currentLatitude.toFixed(6)}, ${currentLongitude.toFixed(6)}`;
            } else {
                photoLocation.textContent = 'Lokasi tidak tersedia';
            }

            // Show preview
            previewSection.classList.remove('hidden');

            // Setup buttons
            setupPreviewButtons();
        }

        function setupPreviewButtons() {
            const retakeBtn = document.getElementById('retake-btn');
            const submitBtn = document.getElementById('submit-btn');
            const previewSection = document.getElementById('photo-preview');

            retakeBtn.onclick = function() {
                previewSection.classList.add('hidden');
                capturedPhotoData = null;
            };

            submitBtn.onclick = function() {
                if (!capturedPhotoData) {
                    showStatusMessage('Tidak ada foto untuk dikirim', 'error');
                    return;
                }

                // Submit attendance
                submitAttendance();
            };
        }

        function submitAttendance() {
            const attendanceType = '{{ $attendanceType }}';
            const submitBtn = document.getElementById('submit-btn');
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim...';

            // Determine which action to call
            let action = '';
            @if($attendanceType === 'WFO')
                @if($canCheckIn)
                    action = 'processCheckIn';
                @elseif($canCheckOut)
                    action = 'processCheckOut';
                @endif
            @else
                @if($canCheckInPagi)
                    action = 'processCheckInPagi';
                @elseif($canCheckInSiang)
                    action = 'processCheckInSiang';
                @elseif($canCheckOut)
                    action = 'processCheckOut';
                @endif
            @endif

            if (action) {
                @this.call(action, capturedPhotoData, currentLatitude, currentLongitude)
                    .then(() => {
                        // Refresh page after successful submission
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    })
                    .catch(() => {
                        // Re-enable button on error
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Kirim';
                    });
            } else {
                showStatusMessage('Tidak ada aksi yang tersedia', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim';
            }
        }

        function showStatusMessage(message, type = 'info') {
            const statusContainer = document.getElementById('status-messages');
            const messageElement = document.createElement('div');
            
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };

            messageElement.className = `border rounded-lg p-3 text-sm ${colors[type] || colors.info}`;
            messageElement.textContent = message;

            statusContainer.appendChild(messageElement);

            // Remove message after 5 seconds
            setTimeout(() => {
                if (messageElement.parentNode) {
                    messageElement.parentNode.removeChild(messageElement);
                }
            }, 5000);
        }

        // Cleanup when leaving page
        window.addEventListener('beforeunload', function() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
