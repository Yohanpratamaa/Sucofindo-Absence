<x-filament-panels::page>
    <div class="dinas-luar-attendance-container">
        <!-- Header Status -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            Status Absensi Dinas Luar Hari Ini
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                    @if($todayAttendance)
                        <div class="mt-3 sm:mt-0">
                            @php
                                $progress = $this->getAttendanceProgress();
                            @endphp
                            <x-filament::badge
                                :color="$progress['percentage'] == 100 ? 'success' : 'warning'"
                            >
                                Progress: {{ $progress['percentage'] }}%
                            </x-filament::badge>
                        </div>
                    @endif
                </div>

                @if($todayAttendance)
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Absen Pagi</dt>
                            <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Absen Siang</dt>
                            <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Absen Sore</dt>
                            <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <x-filament::badge
                                    :color="$todayAttendance->status_color"
                                >
                                    {{ $todayAttendance->status_kehadiran }}
                                </x-filament::badge>
                            </dd>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $progress = $this->getAttendanceProgress();
                    @endphp
                    <div class="mt-5">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span>Progress Absensi</span>
                            <span>{{ $progress['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                        </div>
                        <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span class="{{ $progress['pagi'] ? 'text-green-600' : '' }}">✓ Pagi</span>
                            <span class="{{ $progress['siang'] ? 'text-green-600' : '' }}">✓ Siang</span>
                            <span class="{{ $progress['sore'] ? 'text-green-600' : '' }}">✓ Sore</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Location Status -->
        <div id="location-status" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-6" style="display: none;">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Status Lokasi Saat Ini
                </h3>
                <div id="location-info" class="space-y-2">
                    <!-- Location info will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Camera Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @php
                    $currentAction = null;
                    $actionTitle = 'Tidak Ada Aksi Tersedia';

                    if ($canCheckInPagi) {
                        $currentAction = 'pagi';
                        $actionTitle = 'Absensi Pagi - Dinas Luar';
                    } elseif ($canCheckInSiang) {
                        $currentAction = 'siang';
                        $actionTitle = 'Absensi Siang - Dinas Luar';
                    } elseif ($canCheckOut) {
                        $currentAction = 'sore';
                        $actionTitle = 'Absensi Sore - Dinas Luar';
                    }
                @endphp

                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ $actionTitle }}
                </h3>

                @if($currentAction)
                    <!-- Action Information -->
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Informasi Absensi {{ ucfirst($currentAction) }}
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>
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
                    </div>

                    <!-- Camera Status -->
                    <div id="camera-status" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                        <div class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-blue-700">Mengakses kamera...</span>
                        </div>
                    </div>

                    <!-- Camera Preview -->
                    <div class="mb-4">
                        <video id="camera" width="100%" height="300" autoplay playsinline muted class="rounded-lg border bg-gray-100" style="display: none;"></video>
                        <div id="camera-placeholder" class="w-full h-72 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Kamera belum aktif</h3>
                                <p class="mt-1 text-sm text-gray-500">Klik tombol di bawah untuk mengaktifkan kamera</p>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="mb-4">
                        <button id="start-camera-btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Aktifkan Kamera
                        </button>

                        <button id="stop-camera-btn" style="display: none;" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            Matikan Kamera
                        </button>
                    </div>

                    <!-- Captured Photo Preview -->
                    <div id="photo-preview" style="display: none;" class="mb-4">
                        <img id="captured-photo" class="rounded-lg border w-full max-h-80 object-cover">
                        <div class="mt-2 flex gap-2">
                            <button id="retake-photo" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Ambil Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <button id="capture-btn" style="display: none;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ambil Foto
                        </button>

                        <button id="test-photo-btn" style="display: none;" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Test Foto
                        </button>

                        <button id="submit-btn" style="display: none;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Absen {{ ucfirst($currentAction) }}
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak Ada Aksi Tersedia</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($todayAttendance && $todayAttendance->check_out)
                                Semua absensi hari ini telah selesai.
                            @else
                                Silakan tunggu hingga waktu yang tepat untuk melakukan absensi.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    let stream;
    let currentLocation = null;
    let capturedPhoto = null;

    const currentAction = @json($currentAction);

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
        if (cameraStatus) {
            cameraStatus.style.display = 'block';
            const spinner = cameraStatus.querySelector('.animate-spin');
            const text = cameraStatus.querySelector('span');

            if (spinner) {
                spinner.style.display = isLoading ? 'block' : 'none';
            }
            if (text) {
                text.textContent = message;
            }
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

                    // Show camera controls
                    document.getElementById('start-camera-btn').style.display = 'none';
                    document.getElementById('stop-camera-btn').style.display = 'inline-flex';
                    document.getElementById('capture-btn').style.display = 'inline-flex';
                    document.getElementById('test-photo-btn').style.display = 'inline-flex';

                    hideCameraStatus();
                    showNotification('Kamera berhasil diaktifkan', 'success');
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

            // Show troubleshooting info
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
            <div class="flex items-center">
                <span class="text-green-600 text-lg font-bold mr-2">📍</span>
                <span class="text-green-600 font-medium">Lokasi berhasil terdeteksi</span>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p>Latitude: ${currentLocation.latitude.toFixed(6)}</p>
                <p>Longitude: ${currentLocation.longitude.toFixed(6)}</p>
                <p class="text-xs text-gray-500 mt-1">Lokasi ini akan disimpan sebagai bukti kehadiran dinas luar</p>
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
        testBtn.innerHTML = 'Testing...';

        @this.call('testPhotoSave', capturedPhoto)
            .then((result) => {
                console.log('Test photo result:', result);
                testBtn.disabled = false;
                testBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Test Foto';
            })
            .catch((error) => {
                console.error('Test photo error:', error);
                testBtn.disabled = false;
                testBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Test Foto';
                showNotification('Error testing photo: ' + error.message, 'danger');
            });
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
        console.log('Current action:', currentAction);

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';

        // Call appropriate Livewire method based on current action
        let methodName = '';
        switch(currentAction) {
            case 'pagi':
                methodName = 'processCheckInPagi';
                break;
            case 'siang':
                methodName = 'processCheckInSiang';
                break;
            case 'sore':
                methodName = 'processCheckOut';
                break;
        }

        if (methodName) {
            console.log('Calling method:', methodName);
            @this.call(methodName, capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                .then((result) => {
                    console.log('Method success:', result);
                    location.reload();
                })
                .catch((error) => {
                    console.error('Method error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Absen ' + currentAction.charAt(0).toUpperCase() + currentAction.slice(1);
                    showNotification('Error: ' + error.message, 'danger');
                });
        }
    }

    function showNotification(message, type = 'info') {
        // Create notification using Filament's notification system if available
        if (window.FilamentData && window.FilamentData.notifications) {
            window.FilamentData.notifications.push({
                id: Date.now(),
                title: type === 'danger' ? 'Error' : 'Info',
                body: message,
                color: type === 'danger' ? 'danger' : 'info',
                duration: 5000
            });
        } else {
            alert(message);
        }
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

@push('styles')
<style>
    #camera {
        max-height: 400px;
        object-fit: cover;
    }

    #captured-photo {
        max-height: 400px;
        object-fit: cover;
    }
</style>
@endpush
