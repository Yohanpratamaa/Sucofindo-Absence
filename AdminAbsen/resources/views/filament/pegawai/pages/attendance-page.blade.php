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
    @endphp

    <div class="space-y-6">
        <!-- Dropdown Pilihan Tipe Absensi -->
        <x-filament::section>
            <x-slot name="heading">
                Pilih Tipe Absensi
            </x-slot>

            <x-slot name="description">
                Pilih tipe absensi yang ingin Anda lakukan hari ini
            </x-slot>

            <div class="max-w-md">
                <x-filament::input.wrapper>
                    <x-filament::input.select
                        wire:model.live="attendanceType"
                        :disabled="!$canChangeType"
                    >
                        <option value="WFO">WFO (Work From Office)</option>
                        <option value="Dinas Luar">Dinas Luar</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                @if(!$canChangeType && $lockedType)
                    <div class="mt-2 text-sm text-amber-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Tipe absensi terkunci karena Anda sudah melakukan absensi {{ $lockedType }} hari ini.
                    </div>
                @endif
            </div>

            <div class="mt-4 p-4 rounded-lg border {{ $attendanceType === 'WFO' ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200' }}">
                <div class="flex items-center">
                    @if($attendanceType === 'WFO')
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-blue-800">
                                Mode WFO Aktif
                                @if(!$canChangeType)
                                    <x-filament::badge color="warning" size="sm" class="ml-2">Terkunci</x-filament::badge>
                                @endif
                            </h4>
                            <p class="text-sm text-blue-700">Absensi dengan verifikasi lokasi kantor (Check In & Check Out)</p>
                        </div>
                    @else
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-green-800">
                                Mode Dinas Luar Aktif
                                @if(!$canChangeType)
                                    <x-filament::badge color="warning" size="sm" class="ml-2">Terkunci</x-filament::badge>
                                @endif
                            </h4>
                            <p class="text-sm text-green-700">Absensi 3 waktu dengan pembatasan jam (Pagi, Siang 12:00-14:59, Sore ≥15:00)</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        <!-- Status Absensi Hari Ini -->
        <x-filament::section>
            <x-slot name="heading">
                Status Absensi {{ $attendanceType }} Hari Ini
            </x-slot>

            <x-slot name="description">
                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </x-slot>

            <div class="space-y-6">
                @if($attendanceType === 'WFO')
                    <!-- WFO Status Display -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Status Kehadiran
                        </div>
                        @if($todayAttendance)
                            <x-filament::badge
                                :color="$todayAttendance->check_out ? 'success' : 'warning'"
                                size="lg"
                            >
                                {{ $todayAttendance->check_out ? 'Sudah Check Out' : 'Sudah Check In' }}
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" size="lg">
                                Belum Absen
                            </x-filament::badge>
                        @endif
                    </div>

                    @if($todayAttendance)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Check In</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Check Out</div>
                            </div>
                            <div class="text-center">
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
                                <div class="text-sm text-gray-500 mt-1">Status Kehadiran</div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Dinas Luar Status Display -->
                    @if($todayAttendance)
                        @php
                            $progress = $this->getAttendanceProgress();
                        @endphp

                        <!-- Progress Badge -->
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Progress Absensi
                            </div>
                            <x-filament::badge
                                :color="$progress['percentage'] == 100 ? 'success' : 'warning'"
                                size="lg"
                            >
                                {{ $progress['percentage'] }}% Selesai
                            </x-filament::badge>
                        </div>

                        <!-- Waktu Absensi -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Absen Pagi</div>
                                @if($progress['pagi'])
                                    <x-filament::badge color="success" size="sm" class="mt-1">✓</x-filament::badge>
                                @endif
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    {{ $todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Absen Siang</div>
                                @if($progress['siang'])
                                    <x-filament::badge color="warning" size="sm" class="mt-1">✓</x-filament::badge>
                                @endif
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">
                                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Absen Sore</div>
                                @if($progress['sore'])
                                    <x-filament::badge color="info" size="sm" class="mt-1">✓</x-filament::badge>
                                @endif
                            </div>
                            <div class="text-center">
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
                                <div class="text-sm text-gray-500 mt-1">Status Kehadiran</div>
                            </div>
                        </div>

                        <!-- Progress Bar for Dinas Luar -->
                        <div class="space-y-2">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div
                                    class="bg-primary-600 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $progress['percentage'] }}%"
                                ></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span class="{{ $progress['pagi'] ? 'text-success-600 font-medium' : '' }}">Pagi</span>
                                <span class="{{ $progress['siang'] ? 'text-warning-600 font-medium' : '' }}">Siang</span>
                                <span class="{{ $progress['sore'] ? 'text-info-600 font-medium' : '' }}">Sore</span>
                            </div>
                        </div>
                    @endif
                @endif

                @if(!$todayAttendance)
                    <div class="rounded-lg bg-info-50 p-4 border border-info-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-info-800">Belum Ada Absensi</h4>
                                <p class="text-sm text-info-700 mt-1">
                                    @if($attendanceType === 'WFO')
                                        Anda belum melakukan absensi WFO hari ini. Silakan lakukan check in terlebih dahulu.
                                    @else
                                        Anda belum melakukan absensi dinas luar hari ini. Silakan lakukan absensi pagi terlebih dahulu.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Status Lokasi untuk WFO -->
        @if($attendanceType === 'WFO')
        <x-filament::section id="location-status" style="display: none;">
            <x-slot name="heading">
                Status Lokasi
            </x-slot>

            <x-slot name="description">
                Verifikasi lokasi untuk absensi WFO
            </x-slot>

            <div id="location-info">
                <!-- Location info will be populated by JavaScript -->
            </div>
        </x-filament::section>
        @endif

        <!-- Waktu Absensi untuk Dinas Luar -->
        @if($attendanceType === 'Dinas Luar' && $currentAction)
        <x-filament::section>
            <x-slot name="heading">
                Jadwal Waktu Absensi
            </x-slot>

            <x-slot name="description">
                Informasi waktu yang diperbolehkan untuk melakukan absensi dinas luar
            </x-slot>

            @php
                $timeInfo = $this->getTimeWindowInfo();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Current Time -->
                <div class="text-center p-4 rounded-lg bg-primary-50 border border-primary-200">
                    <div class="text-2xl font-bold text-primary-600" id="current-time">
                        {{ $timeInfo['current_time'] }}
                    </div>
                    <div class="text-sm text-primary-800 font-medium">Waktu Sekarang</div>
                </div>

                <!-- Absensi Siang Window -->
                <div class="text-center p-4 rounded-lg {{ $timeInfo['siang_window']['is_active'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="text-lg font-bold {{ $timeInfo['siang_window']['is_active'] ? 'text-green-600' : 'text-gray-600' }}">
                        {{ $timeInfo['siang_window']['start'] }} - {{ $timeInfo['siang_window']['end'] }}
                    </div>
                    <div class="text-sm {{ $timeInfo['siang_window']['is_active'] ? 'text-green-800' : 'text-gray-600' }} font-medium">
                        Absensi Siang
                        @if($timeInfo['siang_window']['is_active'])
                            <x-filament::badge color="success" size="sm" class="ml-1">Aktif</x-filament::badge>
                        @endif
                    </div>
                </div>

                <!-- Absensi Sore Window -->
                <div class="text-center p-4 rounded-lg {{ $timeInfo['sore_window']['is_active'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="text-lg font-bold {{ $timeInfo['sore_window']['is_active'] ? 'text-green-600' : 'text-gray-600' }}">
                        {{ $timeInfo['sore_window']['start'] }} - Selesai
                    </div>
                    <div class="text-sm {{ $timeInfo['sore_window']['is_active'] ? 'text-green-800' : 'text-gray-600' }} font-medium">
                        Absensi Sore
                        @if($timeInfo['sore_window']['is_active'])
                            <x-filament::badge color="success" size="sm" class="ml-1">Aktif</x-filament::badge>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Time Window Alerts -->
            @if($currentAction === 'siang' && !$timeInfo['siang_window']['is_active'])
                <div class="rounded-lg bg-warning-50 p-4 border border-warning-200 mt-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-warning-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-warning-800">Belum Waktu Absensi Siang</h4>
                            <p class="text-warning-700">Absensi siang hanya dapat dilakukan antara {{ $timeInfo['siang_window']['start'] }} - {{ $timeInfo['siang_window']['end'] }}. Silakan tunggu hingga waktu yang tepat.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentAction === 'sore' && !$timeInfo['sore_window']['is_active'])
                <div class="rounded-lg bg-warning-50 p-4 border border-warning-200 mt-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-warning-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-warning-800">Belum Waktu Absensi Sore</h4>
                            <p class="text-warning-700">Absensi sore hanya dapat dilakukan mulai pukul {{ $timeInfo['sore_window']['start'] }}. Silakan tunggu hingga waktu yang tepat.</p>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::section>
        @endif

        <!-- Main Attendance Section -->
        <x-filament::section>
            <x-slot name="heading">
                {{ $actionTitle }}
            </x-slot>

            <x-slot name="description">
                @if($attendanceType === 'WFO')
                    @if($canCheckIn)
                        Ambil foto selfie untuk melakukan check in
                    @elseif($canCheckOut)
                        Ambil foto selfie untuk melakukan check out
                    @else
                        Absensi WFO hari ini sudah selesai
                    @endif
                @else
                    @if($canCheckInPagi)
                        Ambil foto selfie untuk absensi pagi
                    @elseif($canCheckInSiang)
                        Ambil foto selfie untuk absensi siang (12:00-14:59)
                    @elseif($canCheckOut)
                        Ambil foto selfie untuk absensi sore (≥15:00)
                    @else
                        Absensi dinas luar hari ini sudah selesai
                    @endif
                @endif
            </x-slot>

            <div class="space-y-6">
                <!-- Camera Section -->
                @if(($attendanceType === 'WFO' && ($canCheckIn || $canCheckOut)) || ($attendanceType === 'Dinas Luar' && ($canCheckInPagi || $canCheckInSiang || $canCheckOut)))
                    <div class="space-y-4">
                        <!-- Live Camera -->
                        <div class="flex flex-col items-center space-y-4">
                            <div class="relative">
                                <video
                                    id="camera"
                                    width="320"
                                    height="240"
                                    autoplay
                                    class="rounded-lg border-4 border-primary-200"
                                    style="display: none;"
                                ></video>
                                <canvas
                                    id="canvas"
                                    width="320"
                                    height="240"
                                    class="rounded-lg border-4 border-success-200"
                                    style="display: none;"
                                ></canvas>
                                <div
                                    id="camera-placeholder"
                                    class="w-80 h-60 bg-gray-100 rounded-lg border-4 border-gray-200 flex items-center justify-center"
                                >
                                    <div class="text-center">
                                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p class="text-gray-500">Kamera akan muncul di sini</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Camera Controls -->
                            <div class="flex space-x-4">
                                <x-filament::button
                                    id="start-camera"
                                    color="primary"
                                    icon="heroicon-m-camera"
                                >
                                    Aktifkan Kamera
                                </x-filament::button>

                                <x-filament::button
                                    id="take-photo"
                                    color="success"
                                    icon="heroicon-m-photo"
                                    style="display: none;"
                                >
                                    Ambil Foto
                                </x-filament::button>

                                <x-filament::button
                                    id="retake-photo"
                                    color="warning"
                                    icon="heroicon-m-arrow-path"
                                    style="display: none;"
                                >
                                    Ambil Ulang
                                </x-filament::button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            @if($attendanceType === 'WFO')
                                @if($canCheckIn)
                                    <x-filament::button
                                        id="submit-attendance"
                                        color="success"
                                        size="lg"
                                        icon="heroicon-m-check"
                                        style="display: none;"
                                        onclick="submitWfoCheckIn()"
                                    >
                                        Proses Check In
                                    </x-filament::button>
                                @elseif($canCheckOut)
                                    <x-filament::button
                                        id="submit-attendance"
                                        color="success"
                                        size="lg"
                                        icon="heroicon-m-check"
                                        style="display: none;"
                                        onclick="submitWfoCheckOut()"
                                    >
                                        Proses Check Out
                                    </x-filament::button>
                                @endif
                            @else
                                @if($canCheckInPagi)
                                    <x-filament::button
                                        id="submit-attendance"
                                        color="success"
                                        size="lg"
                                        icon="heroicon-m-check"
                                        style="display: none;"
                                        onclick="submitDinasLuarPagi()"
                                    >
                                        Proses Absensi Pagi
                                    </x-filament::button>
                                @elseif($canCheckInSiang)
                                    <x-filament::button
                                        id="submit-attendance"
                                        color="success"
                                        size="lg"
                                        icon="heroicon-m-check"
                                        style="display: none;"
                                        onclick="submitDinasLuarSiang()"
                                    >
                                        Proses Absensi Siang
                                    </x-filament::button>
                                @elseif($canCheckOut)
                                    <x-filament::button
                                        id="submit-attendance"
                                        color="success"
                                        size="lg"
                                        icon="heroicon-m-check"
                                        style="display: none;"
                                        onclick="submitDinasLuarSore()"
                                    >
                                        Proses Absensi Sore
                                    </x-filament::button>
                                @endif
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Already completed attendance -->
                    <div class="rounded-lg bg-success-50 p-4 border border-success-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-success-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-success-800">Absensi Selesai</h4>
                                <p class="text-sm text-success-700 mt-1">
                                    @if($attendanceType === 'WFO')
                                        Anda sudah menyelesaikan absensi WFO hari ini.
                                    @else
                                        Anda sudah menyelesaikan semua absensi dinas luar hari ini.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>

@push('scripts')
    <script>
        let stream;
        let capturedImageData;
        let currentLocation = null;

        // Office locations (for WFO)
        const offices = @json($this->getOffices());

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentTime();
            setInterval(updateCurrentTime, 1000); // Update time every second

            // Get location if WFO mode
            if ('{{ $attendanceType }}' === 'WFO') {
                getLocation();
            }

            // Set up camera controls
            setupCameraControls();
        });

        function updateCurrentTime() {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                timeElement.textContent = timeString;
            }
        }

        function setupCameraControls() {
            const startCameraBtn = document.getElementById('start-camera');
            const takePhotoBtn = document.getElementById('take-photo');
            const retakePhotoBtn = document.getElementById('retake-photo');
            const submitBtn = document.getElementById('submit-attendance');
            const camera = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const placeholder = document.getElementById('camera-placeholder');

            if (startCameraBtn) {
                startCameraBtn.addEventListener('click', startCamera);
            }

            if (takePhotoBtn) {
                takePhotoBtn.addEventListener('click', takePhoto);
            }

            if (retakePhotoBtn) {
                retakePhotoBtn.addEventListener('click', retakePhoto);
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 320, height: 240 }
                });

                const camera = document.getElementById('camera');
                const placeholder = document.getElementById('camera-placeholder');
                const startBtn = document.getElementById('start-camera');
                const takeBtn = document.getElementById('take-photo');

                camera.srcObject = stream;
                camera.style.display = 'block';
                placeholder.style.display = 'none';
                startBtn.style.display = 'none';
                takeBtn.style.display = 'inline-flex';
            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
            }
        }

        function takePhoto() {
            const camera = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            const takeBtn = document.getElementById('take-photo');
            const retakeBtn = document.getElementById('retake-photo');
            const submitBtn = document.getElementById('submit-attendance');

            // Draw the video frame to canvas
            context.drawImage(camera, 0, 0, 320, 240);

            // Get image data
            capturedImageData = canvas.toDataURL('image/jpeg', 0.8);

            // Update UI
            camera.style.display = 'none';
            canvas.style.display = 'block';
            takeBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-flex';
            if (submitBtn) {
                submitBtn.style.display = 'inline-flex';
            }
        }

        function retakePhoto() {
            const camera = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const takeBtn = document.getElementById('take-photo');
            const retakeBtn = document.getElementById('retake-photo');
            const submitBtn = document.getElementById('submit-attendance');

            // Reset UI
            camera.style.display = 'block';
            canvas.style.display = 'none';
            takeBtn.style.display = 'inline-flex';
            retakeBtn.style.display = 'none';
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }

            capturedImageData = null;
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            currentLocation = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            };

            updateLocationStatus();
        }

        function showError(error) {
            let message = "";
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = "Akses lokasi ditolak oleh pengguna.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = "Informasi lokasi tidak tersedia.";
                    break;
                case error.TIMEOUT:
                    message = "Request untuk mendapatkan lokasi timeout.";
                    break;
                default:
                    message = "Error tidak diketahui terjadi.";
                    break;
            }
            console.error("Location error: " + message);
        }

        function updateLocationStatus() {
            if (!currentLocation) return;

            const locationSection = document.getElementById('location-status');
            const locationInfo = document.getElementById('location-info');

            if (locationSection && locationInfo) {
                const isInRange = checkOfficeLocation();

                locationInfo.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Status Lokasi:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${isInRange ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${isInRange ? '✓ Dalam Radius Kantor' : '✗ Di Luar Radius Kantor'}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400">
                            Lat: ${currentLocation.latitude.toFixed(6)}, Lng: ${currentLocation.longitude.toFixed(6)}
                        </div>
                    </div>
                `;

                locationSection.style.display = 'block';
            }
        }

        function checkOfficeLocation() {
            if (!currentLocation) return false;

            for (const office of offices) {
                const distance = calculateDistance(
                    currentLocation.latitude,
                    currentLocation.longitude,
                    office.latitude,
                    office.longitude
                );

                if (distance <= office.radius) {
                    return true;
                }
            }
            return false;
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                     Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // WFO Attendance Functions
        function submitWfoCheckIn() {
            if (!capturedImageData) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            if (!currentLocation) {
                alert('Lokasi belum didapatkan. Silakan tunggu atau refresh halaman.');
                return;
            }

            @this.call('processCheckIn', capturedImageData, currentLocation.latitude, currentLocation.longitude);
        }

        function submitWfoCheckOut() {
            if (!capturedImageData) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            if (!currentLocation) {
                alert('Lokasi belum didapatkan. Silakan tunggu atau refresh halaman.');
                return;
            }

            @this.call('processCheckOut', capturedImageData, currentLocation.latitude, currentLocation.longitude);
        }

        // Dinas Luar Attendance Functions
        function submitDinasLuarPagi() {
            if (!capturedImageData) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                @this.call('processCheckInPagi', capturedImageData, position.coords.latitude, position.coords.longitude);
            }, function(error) {
                alert('Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.');
            });
        }

        function submitDinasLuarSiang() {
            if (!capturedImageData) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                @this.call('processCheckInSiang', capturedImageData, position.coords.latitude, position.coords.longitude);
            }, function(error) {
                alert('Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.');
            });
        }

        function submitDinasLuarSore() {
            if (!capturedImageData) {
                alert('Silakan ambil foto terlebih dahulu!');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                @this.call('processCheckOut', capturedImageData, position.coords.latitude, position.coords.longitude);
            }, function(error) {
                alert('Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.');
            });
        }

        // Test function for development
        function testPhotoSave() {
            // Create a simple test image data
            const canvas = document.createElement('canvas');
            canvas.width = 320;
            canvas.height = 240;
            const ctx = canvas.getContext('2d');

            // Draw a simple test pattern
            ctx.fillStyle = '#4F46E5';
            ctx.fillRect(0, 0, 320, 240);
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '20px Arial';
            ctx.fillText('Test Photo', 100, 120);

            const testImageData = canvas.toDataURL('image/jpeg', 0.8);

            @this.call('testPhotoSave', testImageData);
        }

        // Clean up when page unloads
        window.addEventListener('beforeunload', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
@endpush
