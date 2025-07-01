<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Status Absensi Hari Ini -->
        <x-filament::section>
            <x-slot name="heading">
                Status Absensi Hari Ini
            </x-slot>

            <x-slot name="description">
                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </x-slot>

            <div class="space-y-6">
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
                @else
                    <div class="rounded-lg bg-info-50 p-4 border border-info-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-info-800">Belum Ada Absensi</h4>
                                <p class="text-sm text-info-700 mt-1">Anda belum melakukan absensi hari ini. Silakan lakukan check in terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Status Lokasi -->
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

        <!-- Absensi WFO -->
        <x-filament::section>
            <x-slot name="heading">
                {{ $canCheckIn ? 'Check In WFO' : ($canCheckOut ? 'Check Out WFO' : 'Absensi WFO') }}
            </x-slot>

            <x-slot name="description">
                @if($canCheckIn)
                    Ambil foto selfie untuk melakukan check in
                @elseif($canCheckOut)
                    Ambil foto selfie untuk melakukan check out
                @else
                    Tidak ada aksi absensi yang tersedia saat ini
                @endif
            </x-slot>

            @if($canCheckIn || $canCheckOut)
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
                            Klik tombol "Aktifkan Kamera" untuk memulai proses absensi
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
                                <p class="text-success-700">Preview foto yang akan digunakan untuk absensi.</p>
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

                    <x-filament::button
                        id="test-size-btn"
                        type="button"
                        outlined
                        color="warning"
                        icon="heroicon-m-scale"
                        style="display: none;"
                    >
                        Cek Ukuran
                    </x-filament::button>

                    <x-filament::button
                        id="submit-btn"
                        type="button"
                        color="success"
                        icon="heroicon-m-check-circle"
                        style="display: none;"
                    >
                        {{ $canCheckIn ? 'Check In Sekarang' : 'Check Out Sekarang' }}
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
                                    <h4 class="font-medium text-success-800">Absensi Hari Ini Selesai</h4>
                                    <p class="text-success-700">Anda telah menyelesaikan check in dan check out untuk hari ini.</p>
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
                        Lihat riwayat absensi Anda atau hubungi administrator jika ada pertanyaan.
                    </div>

                    <x-filament::button
                        tag="a"
                        :href="url('/pegawai/my-attendances')"
                        outlined
                        color="primary"
                        icon="heroicon-m-clock"
                    >
                        Lihat Riwayat Absensi
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

    // Office locations (passed from backend)
    const offices = @json($this->getOffices());

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();

        @if($canCheckIn || $canCheckOut)
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
        const testSizeBtn = document.getElementById('test-size-btn');

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

        if (testSizeBtn) {
            testSizeBtn.addEventListener('click', testPhotoSize);
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
        document.getElementById('camera-placeholder').style.display = 'block';
        document.getElementById('camera-overlay').style.display = 'none';

        // Hide buttons
        document.getElementById('stop-camera-btn').style.display = 'none';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('test-photo-btn').style.display = 'none';
        document.getElementById('test-size-btn').style.display = 'none';

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
                    document.getElementById('test-size-btn').style.display = 'inline-flex';

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
                    checkLocationValidity();
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

    function checkLocationValidity() {
        if (!currentLocation) return;

        // Calculate distance to nearest office
        let nearestOffice = null;
        let nearestDistance = Infinity;

        offices.forEach(office => {
            const distance = calculateDistance(
                currentLocation.latitude,
                currentLocation.longitude,
                parseFloat(office.latitude),
                parseFloat(office.longitude)
            );

            if (distance < nearestDistance) {
                nearestDistance = distance;
                nearestOffice = office;
            }
        });

        const isWithinRadius = nearestOffice && nearestDistance <= nearestOffice.radius;

        // Update location status UI
        updateLocationStatus(nearestOffice, nearestDistance, isWithinRadius);
    }

    function updateLocationStatus(nearestOffice, distance, isWithinRadius) {
        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');

        locationStatus.style.display = 'block';

        const statusText = isWithinRadius ? 'Dalam radius kantor' : 'Di luar radius kantor';
        const alertType = isWithinRadius ? 'success' : 'danger';
        const statusIcon = isWithinRadius ? '✅' : '❌';

        locationInfo.innerHTML = `
            <div class="rounded-lg border p-4 ${isWithinRadius ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">${statusIcon}</span>
                        <div>
                            <h4 class="font-semibold ${isWithinRadius ? 'text-green-800' : 'text-red-800'}">${statusText}</h4>
                            <p class="text-sm text-gray-600">Status lokasi untuk absensi WFO</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Kantor:</strong> ${nearestOffice ? nearestOffice.name : 'Tidak ditemukan'}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <span class="text-gray-700"><strong>Jarak:</strong> ${Math.round(distance)}m (Max: ${nearestOffice ? nearestOffice.radius : 0}m)</span>
                    </div>
                </div>

                ${!isWithinRadius ? `
                    <div class="mt-3 p-3 bg-red-100 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-red-700 font-medium text-sm">Anda harus berada dalam radius kantor untuk melakukan absensi</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;

        // Enable/disable buttons based on location
        const captureBtn = document.getElementById('capture-btn');
        const submitBtn = document.getElementById('submit-btn');

        if (captureBtn) {
            captureBtn.disabled = !isWithinRadius;
        }

        if (submitBtn) {
            submitBtn.disabled = !isWithinRadius;
        }
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

    function testPhotoSize() {
        if (!capturedPhoto) {
            showNotification('Silakan ambil foto terlebih dahulu dengan tombol "Ambil Foto".', 'danger');
            return;
        }

        const testSizeBtn = document.getElementById('test-size-btn');
        testSizeBtn.disabled = true;
        testSizeBtn.textContent = 'Checking...';

        // Calculate sizes locally
        const originalSize = capturedPhoto.length;
        const originalSizeMB = (originalSize / 1024 / 1024).toFixed(2);

        // Remove base64 prefix and decode
        const cleanedData = capturedPhoto.replace(/^data:image\/[^;]+;base64,/, '');
        const binaryLength = (cleanedData.length * 3) / 4; // Approximate binary size
        const binarySizeMB = (binaryLength / 1024 / 1024).toFixed(2);

        console.log('Photo size analysis:', {
            original_size: originalSize,
            original_size_mb: originalSizeMB,
            binary_size_mb: binarySizeMB,
            php_upload_limit: '2M'
        });

        const isWithinLimits = binaryLength < (2 * 1024 * 1024); // 2MB check

        let message = `
            Ukuran Base64: ${originalSizeMB} MB
            Ukuran Gambar: ${binarySizeMB} MB
            PHP Upload Limit: 2M
            Dalam Batas: ${isWithinLimits ? 'Ya' : 'Tidak'}
        `;

        const alertType = isWithinLimits ? 'info' : 'danger';
        showNotification(message, alertType);

        testSizeBtn.disabled = false;
        testSizeBtn.textContent = 'Cek Ukuran';
    }

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

        console.log('Submitting attendance with photo length:', capturedPhoto.length);
        console.log('Location:', currentLocation);

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        // Call Livewire method
        const isCheckIn = {{ $canCheckIn ? 'true' : 'false' }};

        if (isCheckIn) {
            console.log('Calling processCheckIn...');
            @this.call('processCheckIn', capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                .then((result) => {
                    console.log('processCheckIn success:', result);
                    showNotification('Check in berhasil! Halaman akan dimuat ulang...', 'success');
                    setTimeout(() => location.reload(), 2000);
                })
                .catch((error) => {
                    console.error('processCheckIn error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '{{ $canCheckIn ? "Check In Sekarang" : "Check Out Sekarang" }}';
                    showNotification('Error: ' + error.message, 'danger');
                });
        } else {
            console.log('Calling processCheckOut...');
            @this.call('processCheckOut', capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                .then((result) => {
                    console.log('processCheckOut success:', result);
                    showNotification('Check out berhasil! Halaman akan dimuat ulang...', 'success');
                    setTimeout(() => location.reload(), 2000);
                })
                .catch((error) => {
                    console.error('processCheckOut error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '{{ $canCheckIn ? "Check In Sekarang" : "Check Out Sekarang" }}';
                    showNotification('Error: ' + error.message, 'danger');
                });
        }
    }    function showNotification(message, type = 'info') {
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
