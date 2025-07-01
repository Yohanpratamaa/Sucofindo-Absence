<x-filament-panels::page>
    @php
        // Define currentAction at the top level using component methods
        $currentAction = $this->getCurrentAction();
        $actionTitle = $this->getActionTitle();

        // Get properties from the Livewire component
        $canCheckInPagi = $this->canCheckInPagi;
        $canCheckInSiang = $this->canCheckInSiang;
        $canCheckOut = $this->canCheckOut;
        $todayAttendance = $this->todayAttendance;
    @endphp

    <div class="space-y-6">
        <!-- Status Absensi Dinas Luar Hari Ini -->
        <x-filament::section>
            <x-slot name="heading">
                Status Absensi Dinas Luar Hari Ini
            </x-slot>

            <x-slot name="description">
                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </x-slot>

            <div class="space-y-6">
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

                    <!-- Progress Bar -->
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
                @else
                    <div class="rounded-lg bg-info-50 p-4 border border-info-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-info-800">Belum Ada Absensi</h4>
                                <p class="text-info-700">Anda belum melakukan absensi dinas luar hari ini. Silakan lakukan absensi pagi terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Waktu Absensi -->
        @if($currentAction)
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

        <!-- Status Lokasi -->
        <x-filament::section id="location-status" style="display: none;">
            <x-slot name="heading">
                Status Lokasi Saat Ini
            </x-slot>

            <x-slot name="description">
                Lokasi Anda akan dicatat secara otomatis untuk absensi dinas luar
            </x-slot>

            <div id="location-info">
                <!-- Location info will be populated by JavaScript -->
            </div>
        </x-filament::section>

        <!-- Absensi Dinas Luar -->
        <x-filament::section>
            <x-slot name="heading">
                {{ $actionTitle }}
            </x-slot>

            <x-slot name="description">
                @if($currentAction === 'pagi')
                    Ambil foto selfie untuk memulai hari kerja dinas luar
                @elseif($currentAction === 'siang')
                    Ambil foto selfie untuk absensi siang
                @elseif($currentAction === 'sore')
                    Ambil foto selfie untuk mengakhiri hari kerja dinas luar
                @else
                    Tidak ada aksi absensi yang tersedia saat ini
                @endif
            </x-slot>

            @if($currentAction)
                <!-- Information Alert -->
                <div class="rounded-lg bg-info-50 p-4 border border-info-200 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-info-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-info-800">Informasi Absensi {{ ucfirst($currentAction) }}</h4>
                            <p class="text-info-700">
                                @if($currentAction === 'pagi')
                                    Lakukan absensi pagi untuk memulai hari kerja dinas luar. Lokasi Anda akan dicatat secara otomatis.
                                @elseif($currentAction === 'siang')
                                    Waktu absensi siang. Pastikan Anda berada di lokasi tugas yang tepat.
                                @else
                                    Absensi sore untuk mengakhiri hari kerja dinas luar. Terima kasih atas kerja keras Anda.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Camera Status Alert -->
                <div id="camera-status" class="rounded-lg bg-info-50 p-4 border border-info-200" style="display: none;">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-info-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-info-800">Status Kamera</h4>
                            <span id="camera-status-text" class="text-info-700">Mengakses kamera...</span>
                        </div>
                    </div>
                </div>

                <!-- Camera Preview Area -->
                <div class="space-y-4">
                    <video
                        id="camera"
                        class="w-full h-80 object-cover rounded-lg border-2 border-gray-200"
                        autoplay
                        playsinline
                        muted
                        style="display: none;"
                    ></video>

                    <div id="camera-placeholder" class="flex flex-col items-center justify-center h-80 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Kamera Belum Aktif</h4>
                        <p class="text-sm text-gray-600 text-center mb-2">
                            Klik tombol "Aktifkan Kamera" untuk memulai proses absensi {{ $currentAction }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pastikan izin kamera sudah diaktifkan
                        </p>
                    </div>

                    <div id="camera-overlay" class="relative" style="display: none;">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-black bg-opacity-60 text-white px-4 py-2 rounded-lg text-sm">
                                Posisikan wajah dalam frame
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Camera Controls -->
                <div class="flex flex-wrap gap-3">
                    <x-filament::button
                        id="start-camera-btn"
                        type="button"
                        color="primary"
                        icon="heroicon-m-camera"
                    >
                        Aktifkan Kamera
                    </x-filament::button>

                    <x-filament::button
                        id="stop-camera-btn"
                        type="button"
                        outlined
                        color="gray"
                        icon="heroicon-m-stop"
                        style="display: none;"
                    >
                        Matikan Kamera
                    </x-filament::button>
                </div>

                <!-- Photo Preview -->
                <div id="photo-preview" class="space-y-4" style="display: none;">
                    <div class="rounded-lg bg-success-50 p-4 border border-success-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-success-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-success-800">Foto Berhasil Diambil</h4>
                                <p class="text-success-700">Preview foto yang akan digunakan untuk absensi {{ $currentAction }}.</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <img
                            id="captured-photo"
                            class="w-full h-80 object-cover rounded-lg border-2 border-green-200"
                            alt="Preview foto absensi"
                        >
                        <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs font-medium">
                            ✓ Foto Siap
                        </div>
                    </div>

                    <x-filament::button
                        id="retake-photo"
                        type="button"
                        outlined
                        color="gray"
                        icon="heroicon-m-arrow-path"
                    >
                        Ambil Ulang
                    </x-filament::button>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <x-filament::button
                        id="capture-btn"
                        type="button"
                        color="info"
                        icon="heroicon-m-camera"
                        style="display: none;"
                    >
                        Ambil Foto
                    </x-filament::button>

                    <x-filament::button
                        id="test-photo-btn"
                        type="button"
                        outlined
                        color="gray"
                        icon="heroicon-m-beaker"
                        style="display: none;"
                    >
                        Test Foto
                    </x-filament::button>

                    @php
                        $timeInfo = $this->getTimeWindowInfo();
                        $isTimeAllowed = true;
                        $timeMessage = '';

                        if ($currentAction === 'siang' && !$timeInfo['siang_window']['is_active']) {
                            $isTimeAllowed = false;
                            $timeMessage = 'Absensi siang hanya dapat dilakukan antara 12:00 - 14:59';
                        } elseif ($currentAction === 'sore' && !$timeInfo['sore_window']['is_active']) {
                            $isTimeAllowed = false;
                            $timeMessage = 'Absensi sore hanya dapat dilakukan mulai pukul 15:00';
                        }
                    @endphp

                    <x-filament::button
                        id="submit-btn"
                        type="button"
                        :color="$isTimeAllowed ? 'success' : 'gray'"
                        icon="heroicon-m-check-circle"
                        :disabled="!$isTimeAllowed"
                        style="display: none;"
                        :tooltip="!$isTimeAllowed ? $timeMessage : null"
                    >
                        @if($isTimeAllowed)
                            Absen {{ ucfirst($currentAction) }} Sekarang
                        @else
                            Waktu Belum Tepat
                        @endif
                    </x-filament::button>
                </div>
            @else
                <!-- No Action Available -->
                <div class="text-center py-12">
                    <svg class="w-20 h-20 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Tidak Ada Aksi Tersedia</h3>

                    @if($todayAttendance && $todayAttendance->check_out)
                        <div class="rounded-lg bg-success-50 p-4 border border-success-200 max-w-md mx-auto mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-success-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-success-800">Absensi Dinas Luar Selesai</h4>
                                    <p class="text-success-700">Anda telah menyelesaikan semua absensi dinas luar untuk hari ini.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg bg-info-50 p-4 border border-info-200 max-w-md mx-auto mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-info-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-info-800">Menunggu Waktu Absensi</h4>
                                    <p class="text-info-700">Silakan tunggu hingga waktu yang tepat untuk melakukan absensi.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="text-gray-600 mb-6">
                        Lihat riwayat absensi dinas luar Anda atau hubungi administrator jika ada pertanyaan.
                    </div>

                    <x-filament::button
                        tag="a"
                        :href="url('/pegawai/my-dinas-luar-attendances')"
                        outlined
                        color="primary"
                        icon="heroicon-m-clock"
                    >
                        Lihat Riwayat Dinas Luar
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    let stream;
    let currentLocation = null;
    let capturedPhoto = null;

    const currentAction = @json($currentAction ?? null);

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();

        @if($currentAction)
            // Auto-start location detection
            getCurrentLocation();

            // Hide camera status initially
            hideCameraStatus();
        @endif
    });

    function hideCameraStatus() {
        const cameraStatus = document.getElementById('camera-status');
        if (cameraStatus) {
            cameraStatus.style.display = 'none';
        }
    }

    function showCameraStatus(message, isLoading = false) {
        const cameraStatus = document.getElementById('camera-status');
        const statusText = document.getElementById('camera-status-text');

        if (cameraStatus) {
            cameraStatus.style.display = 'block';
        }
        if (statusText) {
            statusText.textContent = message;
        }
    }

    function setupEventListeners() {
        const startCameraBtn = document.getElementById('start-camera-btn');
        const stopCameraBtn = document.getElementById('stop-camera-btn');
        const captureBtn = document.getElementById('capture-btn');
        const retakeBtn = document.getElementById('retake-photo');
        const submitBtn = document.getElementById('submit-btn');
        const testPhotoBtn = document.getElementById('test-photo-btn');

        if (startCameraBtn) {
            startCameraBtn.addEventListener('click', startCamera);
        }

        if (stopCameraBtn) {
            stopCameraBtn.addEventListener('click', stopCamera);
        }

        if (captureBtn) {
            captureBtn.addEventListener('click', capturePhoto);
        }

        if (retakeBtn) {
            retakeBtn.addEventListener('click', retakePhoto);
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', submitAttendance);
        }

        if (testPhotoBtn) {
            testPhotoBtn.addEventListener('click', testPhoto);
        }
    }

    function startCamera() {
        console.log('Starting camera...');
        showCameraStatus('Mengakses kamera...', true);
        initializeCamera();
    }

    function stopCamera() {
        console.log('Stopping camera...');
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        // Hide camera and show placeholder
        document.getElementById('camera').style.display = 'none';
        document.getElementById('camera-placeholder').style.display = 'flex';
        document.getElementById('camera-overlay').style.display = 'none';

        // Hide buttons
        document.getElementById('stop-camera-btn').style.display = 'none';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('test-photo-btn').style.display = 'none';

        // Show start button
        document.getElementById('start-camera-btn').style.display = 'inline-flex';

        hideCameraStatus();
        showNotification('Kamera dimatikan', 'info');
    }

    async function initializeCamera() {
        try {
            console.log('Requesting camera access...');

            // Check if browser supports getUserMedia
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Browser tidak mendukung akses kamera');
            }

            // Request camera access with fallback options
            const constraints = {
                video: {
                    facingMode: 'user', // Front camera for selfie
                    width: { ideal: 640, max: 1280 },
                    height: { ideal: 480, max: 720 }
                },
                audio: false
            };

            console.log('Camera constraints:', constraints);
            stream = await navigator.mediaDevices.getUserMedia(constraints);

            const videoElement = document.getElementById('camera');
            if (videoElement) {
                videoElement.srcObject = stream;

                // Wait for video to be ready
                videoElement.onloadedmetadata = function() {
                    console.log('Camera ready, video dimensions:', videoElement.videoWidth, 'x', videoElement.videoHeight);

                    // Show camera and hide placeholder
                    videoElement.style.display = 'block';
                    document.getElementById('camera-placeholder').style.display = 'none';
                    document.getElementById('camera-overlay').style.display = 'block';

                    // Show camera controls
                    document.getElementById('start-camera-btn').style.display = 'none';
                    document.getElementById('stop-camera-btn').style.display = 'inline-flex';
                    document.getElementById('capture-btn').style.display = 'inline-flex';
                    document.getElementById('test-photo-btn').style.display = 'inline-flex';

                    hideCameraStatus();
                    showNotification('Kamera berhasil diaktifkan! Posisikan wajah dalam frame dan ambil foto.', 'success');
                };

                videoElement.onerror = function(error) {
                    console.error('Video element error:', error);
                    throw new Error('Gagal menampilkan video dari kamera');
                };
            }

        } catch (err) {
            console.error('Error accessing camera:', err);
            hideCameraStatus();

            let errorMessage = 'Error mengakses kamera: ';
            if (err.name === 'NotAllowedError') {
                errorMessage += 'Akses kamera ditolak. Silakan izinkan akses kamera pada browser.';
            } else if (err.name === 'NotFoundError') {
                errorMessage += 'Kamera tidak ditemukan pada perangkat ini.';
            } else if (err.name === 'NotSupportedError') {
                errorMessage += 'Browser tidak mendukung fitur kamera.';
            } else {
                errorMessage += err.message;
            }

            showNotification(errorMessage, 'danger');
            showCameraStatus('❌ ' + errorMessage);
        }
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    updateLocationStatus();
                },
                function(error) {
                    console.error('Error getting location:', error);
                    showNotification('Error mendapatkan lokasi: ' + error.message, 'danger');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            showNotification('Geolocation tidak didukung oleh browser ini.', 'danger');
        }
    }

    function updateLocationStatus() {
        if (!currentLocation) return;

        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');

        locationStatus.style.display = 'block';

        locationInfo.innerHTML = `
            <div class="rounded-lg border p-4 border-green-200 bg-green-50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">📍</span>
                        <div>
                            <h4 class="font-semibold text-green-800">Lokasi Terdeteksi</h4>
                            <p class="text-sm text-gray-600">Lokasi untuk absensi dinas luar</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</span>
                    </div>
                </div>

                <div class="mt-3 p-3 bg-green-100 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-green-700 font-medium text-sm">Lokasi siap untuk absensi dinas luar</span>
                    </div>
                </div>
            </div>
        `;
    }

    function capturePhoto() {
        console.log('Capturing photo...');

        const video = document.getElementById('camera');
        if (!video || !stream) {
            showNotification('Kamera belum diaktifkan. Silakan aktifkan kamera terlebih dahulu.', 'danger');
            return;
        }

        if (video.videoWidth === 0 || video.videoHeight === 0) {
            showNotification('Video belum siap. Silakan tunggu sebentar dan coba lagi.', 'danger');
            return;
        }

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        // Set smaller canvas dimensions to reduce file size
        const maxWidth = 640;
        const maxHeight = 480;
        const videoAspectRatio = video.videoWidth / video.videoHeight;

        let canvasWidth, canvasHeight;

        if (video.videoWidth > video.videoHeight) {
            canvasWidth = Math.min(maxWidth, video.videoWidth);
            canvasHeight = canvasWidth / videoAspectRatio;
        } else {
            canvasHeight = Math.min(maxHeight, video.videoHeight);
            canvasWidth = canvasHeight * videoAspectRatio;
        }

        canvas.width = canvasWidth;
        canvas.height = canvasHeight;

        console.log('Original video dimensions:', video.videoWidth, 'x', video.videoHeight);
        console.log('Canvas dimensions:', canvasWidth, 'x', canvasHeight);

        // Draw the video frame to canvas
        context.drawImage(video, 0, 0, canvasWidth, canvasHeight);

        // Convert to base64 with compression
        capturedPhoto = canvas.toDataURL('image/jpeg', 0.6);

        console.log('Captured photo data length:', capturedPhoto.length);
        console.log('Captured photo size MB:', (capturedPhoto.length / 1024 / 1024).toFixed(2));

        // Show preview
        const capturedPhotoImg = document.getElementById('captured-photo');
        if (capturedPhotoImg) {
            capturedPhotoImg.src = capturedPhoto;
        }

        // Hide camera and show photo preview
        document.getElementById('camera').style.display = 'none';
        document.getElementById('photo-preview').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('submit-btn').style.display = 'inline-flex';

        showNotification('Foto berhasil diambil!', 'success');
    }

    function retakePhoto() {
        console.log('Retaking photo...');

        capturedPhoto = null;

        // Hide photo preview and show camera
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('camera').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'inline-flex';
        document.getElementById('submit-btn').style.display = 'none';

        showNotification('Siap untuk mengambil foto lagi', 'info');
    }

    function testPhoto() {
        if (!capturedPhoto) {
            showNotification('Silakan ambil foto terlebih dahulu dengan tombol "Ambil Foto".', 'danger');
            return;
        }

        console.log('Testing photo save...');
        const testBtn = document.getElementById('test-photo-btn');
        testBtn.disabled = true;
        testBtn.textContent = 'Testing...';

        @this.call('testPhotoSave', capturedPhoto)
            .then((result) => {
                console.log('Test photo result:', result);
                testBtn.disabled = false;
                testBtn.textContent = 'Test Foto';
            })
            .catch((error) => {
                console.error('Test photo error:', error);
                testBtn.disabled = false;
                testBtn.textContent = 'Test Foto';
                showNotification('Error testing photo: ' + error.message, 'danger');
            });
    }

    // Update current time every second
    function updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }

        // Check time windows for enable/disable buttons
        checkTimeWindows(now);
    }

    function checkTimeWindows(now) {
        // Only check time windows if currentAction exists
        if (!currentAction) {
            return;
        }

        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentTimeMinutes = currentHour * 60 + currentMinute;

        // Siang window: 12:00 - 14:59 (720 - 899 minutes)
        const siangStart = 12 * 60; // 720 minutes
        const siangEnd = 15 * 60 - 1; // 899 minutes (14:59)

        // Sore window: 15:00 and after (900+ minutes)
        const soreStart = 15 * 60; // 900 minutes

        const submitBtn = document.getElementById('submit-btn');

        if (currentAction === 'siang') {
            const isInSiangWindow = currentTimeMinutes >= siangStart && currentTimeMinutes <= siangEnd;

            if (submitBtn) {
                submitBtn.disabled = !isInSiangWindow;
                submitBtn.className = submitBtn.className.replace(/(bg-gray-\d+|bg-success-\d+)/g, '');
                submitBtn.classList.add(isInSiangWindow ? 'bg-success-600' : 'bg-gray-400');

                const buttonText = isInSiangWindow ? 'Absen Siang Sekarang' : 'Waktu Belum Tepat (12:00-14:59)';
                if (submitBtn.querySelector('span')) {
                    submitBtn.querySelector('span').textContent = buttonText;
                } else {
                    submitBtn.textContent = buttonText;
                }
            }
        } else if (currentAction === 'sore') {
            const isInSoreWindow = currentTimeMinutes >= soreStart;

            if (submitBtn) {
                submitBtn.disabled = !isInSoreWindow;
                submitBtn.className = submitBtn.className.replace(/(bg-gray-\d+|bg-success-\d+)/g, '');
                submitBtn.classList.add(isInSoreWindow ? 'bg-success-600' : 'bg-gray-400');

                const buttonText = isInSoreWindow ? 'Absen Sore Sekarang' : 'Waktu Belum Tepat (Mulai 15:00)';
                if (submitBtn.querySelector('span')) {
                    submitBtn.querySelector('span').textContent = buttonText;
                } else {
                    submitBtn.textContent = buttonText;
                }
            }
        }
    }

    // Start the time update interval
    if (document.getElementById('current-time')) {
        updateCurrentTime(); // Initial update
        setInterval(updateCurrentTime, 1000); // Update every second
    }

    // Updated submitAttendance function with time validation
    function submitAttendance() {
        if (!capturedPhoto) {
            showNotification('Silakan ambil foto terlebih dahulu.', 'danger');
            return;
        }

        if (!currentLocation) {
            showNotification('Lokasi belum terdeteksi. Silakan coba lagi.', 'danger');
            getCurrentLocation();
            return;
        }

        // Check if currentAction exists
        if (!currentAction) {
            showNotification('Tidak ada aksi absensi yang tersedia saat ini.', 'danger');
            return;
        }

        // Check time restrictions before submitting
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentTimeMinutes = currentHour * 60 + currentMinute;

        if (currentAction === 'siang') {
            const siangStart = 12 * 60; // 720 minutes (12:00)
            const siangEnd = 15 * 60 - 1; // 899 minutes (14:59)

            if (currentTimeMinutes < siangStart || currentTimeMinutes > siangEnd) {
                showNotification('Absensi siang hanya dapat dilakukan antara 12:00 - 14:59', 'danger');
                return;
            }
        } else if (currentAction === 'sore') {
            const soreStart = 15 * 60; // 900 minutes (15:00)

            if (currentTimeMinutes < soreStart) {
                showNotification('Absensi sore hanya dapat dilakukan mulai pukul 15:00', 'danger');
                return;
            }
        }

        console.log('Submitting attendance with photo length:', capturedPhoto.length);
        console.log('Location:', currentLocation);
        console.log('Current action:', currentAction);

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        // Call appropriate Livewire method based on current action
        let livewireMethod = '';
        if (currentAction === 'pagi') {
            livewireMethod = 'processCheckInPagi';
        } else if (currentAction === 'siang') {
            livewireMethod = 'processCheckInSiang';
        } else if (currentAction === 'sore') {
            livewireMethod = 'processCheckOut';
        }

        if (livewireMethod) {
            console.log('Calling ' + livewireMethod + '...');
            @this.call(livewireMethod, capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                .then((result) => {
                    console.log(livewireMethod + ' success:', result);
                    showNotification('Absensi ' + currentAction + ' berhasil! Halaman akan dimuat ulang...', 'success');
                    setTimeout(() => location.reload(), 2000);
                })
                .catch((error) => {
                    console.error(livewireMethod + ' error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Absen ' + currentAction.charAt(0).toUpperCase() + currentAction.slice(1) + ' Sekarang';
                    showNotification('Error: ' + error.message, 'danger');
                });
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Absen Sekarang';
            showNotification('Error: Aksi tidak dikenali', 'danger');
        }
    }

    function showNotification(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);

        // Use Filament's native notification system when available
        if (window.Livewire) {
            // Try to use Livewire's notification system through the component
            try {
                window.Livewire.find('{{ $this->getId() }}').dispatch('notify', {
                    message: message,
                    type: type
                });
                return;
            } catch (e) {
                console.log('Livewire notification not available, using fallback');
            }
        }

        // Fallback: Create a simple toast notification
        const toast = document.createElement('div');
        const bgColor = {
            'success': 'bg-green-100 border-green-500 text-green-900',
            'danger': 'bg-red-100 border-red-500 text-red-900',
            'warning': 'bg-yellow-100 border-yellow-500 text-yellow-900',
            'info': 'bg-blue-100 border-blue-500 text-blue-900'
        };

        toast.className = `fixed top-4 right-4 max-w-sm w-full ${bgColor[type] || bgColor.info} border-l-4 p-4 rounded shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="mr-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Cleanup camera stream when page unloads
    window.addEventListener('beforeunload', function() {
        if (stream) {
            console.log('Cleaning up camera stream...');
            stream.getTracks().forEach(track => track.stop());
        }
    });

    // Also cleanup when user navigates away
    window.addEventListener('pagehide', function() {
        if (stream) {
            console.log('Page hidden, cleaning up camera stream...');
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endpush
