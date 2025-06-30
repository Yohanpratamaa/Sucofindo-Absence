<x-filament-panels::page>
    @php
        $currentTime = \Carbon\Carbon::now();
        $currentAction = null;
        $actionTitle = 'Tidak Ada Aksi Tersedia';
        $actionDescription = 'Belum saatnya untuk melakukan absensi';

        if ($canCheckInPagi) {
            $currentAction = 'pagi';
            $actionTitle = 'Absensi Pagi';
            $actionDescription = 'Memulai hari kerja dinas luar';
        } elseif ($canCheckInSiang) {
            $currentAction = 'siang';
            $actionTitle = 'Absensi Siang';
            $actionDescription = 'Waktu istirahat siang';
        } elseif ($canCheckOut) {
            $currentAction = 'sore';
            $actionTitle = 'Absensi Sore';
            $actionDescription = 'Mengakhiri hari kerja';
        }

        $progress = $this->getAttendanceProgress();
    @endphp

    <div class="space-y-6">
        <!-- Header Information -->
        <x-filament::section>
            <x-slot name="heading">
                {{ $actionTitle }}
            </x-slot>

            <x-slot name="description">
                {{ $currentTime->isoFormat('dddd, D MMMM Y • H:mm') }} WIB
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-heroicon-s-map-pin class="w-4 h-4" />
                        <span id="location-text">Mendeteksi lokasi...</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">
                        {{ $actionDescription }}
                    </div>
                    <!-- Location Controls -->
                    <div class="flex gap-2 mt-2">
                        <x-filament::button
                            color="gray"
                            size="xs"
                            id="manual-location-btn"
                            wire:ignore
                        >
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Coba Lagi
                        </x-filament::button>
                        @if(config('app.debug'))
                            <x-filament::button
                                color="warning"
                                size="xs"
                                id="force-location-btn"
                                wire:ignore
                            >
                                <x-heroicon-o-map-pin class="w-3 h-3" />
                                Paksa Lokasi
                            </x-filament::button>
                        @endif
                    </div>
                </div>

                @if($todayAttendance)
                    <div class="text-right">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                            Progress: {{ $progress['percentage'] }}%
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Status Absensi -->
        @if($todayAttendance)
            <x-filament::section>
                <x-slot name="heading">
                    Status Absensi Hari Ini
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Absen Pagi -->
                    <div class="border rounded-lg p-4 {{ $todayAttendance->check_in ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-700' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-sun class="w-8 h-8 {{ $todayAttendance->check_in ? 'text-green-600' : 'text-gray-400' }}" />
                            </div>
                            <div>
                                <h3 class="font-medium {{ $todayAttendance->check_in ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">
                                    Absen Pagi
                                </h3>
                                <p class="text-sm {{ $todayAttendance->check_in ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') . ' WIB' : 'Belum absen' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Absen Siang -->
                    <div class="border rounded-lg p-4 {{ $todayAttendance->absen_siang ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-700' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-sun class="w-8 h-8 {{ $todayAttendance->absen_siang ? 'text-blue-600' : 'text-gray-400' }}" />
                            </div>
                            <div>
                                <h3 class="font-medium {{ $todayAttendance->absen_siang ? 'text-blue-900 dark:text-blue-100' : 'text-gray-900 dark:text-gray-100' }}">
                                    Absen Siang
                                </h3>
                                <p class="text-sm {{ $todayAttendance->absen_siang ? 'text-blue-700 dark:text-blue-300' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') . ' WIB' : 'Belum absen' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Absen Sore -->
                    <div class="border rounded-lg p-4 {{ $todayAttendance->check_out ? 'bg-orange-50 border-orange-200 dark:bg-orange-900/20 dark:border-orange-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-700' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-moon class="w-8 h-8 {{ $todayAttendance->check_out ? 'text-orange-600' : 'text-gray-400' }}" />
                            </div>
                            <div>
                                <h3 class="font-medium {{ $todayAttendance->check_out ? 'text-orange-900 dark:text-orange-100' : 'text-gray-900 dark:text-gray-100' }}">
                                    Absen Sore
                                </h3>
                                <p class="text-sm {{ $todayAttendance->check_out ? 'text-orange-700 dark:text-orange-300' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') . ' WIB' : 'Belum absen' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress Absensi</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $progress['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        <!-- Lokasi Status -->
        <div id="location-status" class="hidden">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-map-pin class="w-5 h-5 text-green-600" />
                            Lokasi Terdeteksi
                        </div>
                        <x-filament::button
                            color="gray"
                            size="xs"
                            id="refresh-location-btn"
                            wire:ignore
                        >
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Refresh
                        </x-filament::button>
                    </div>
                </x-slot>

                <div id="location-info" class="space-y-2">
                    <!-- Will be populated by JavaScript -->
                </div>
            </x-filament::section>
        </div>

        <!-- Camera Section -->
        @if($currentAction)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $actionTitle }}
                </x-slot>

                <x-slot name="description">
                    Ambil foto selfie untuk melakukan absensi {{ strtolower($currentAction) }}
                </x-slot>

                <div class="space-y-6">
                    <!-- Camera Status -->
                    <div id="camera-status" class="hidden">
                        <div class="flex items-center justify-center p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                                <span class="text-sm font-medium text-blue-800 dark:text-blue-200" id="status-text">
                                    Mengakses kamera...
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Preview Area -->
                    <div class="relative bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden aspect-video max-w-md mx-auto">
                        <!-- Video Element -->
                        <video id="camera" class="hidden w-full h-full object-cover" autoplay playsinline muted></video>

                        <!-- Camera Placeholder -->
                        <div id="camera-placeholder" class="flex flex-col items-center justify-center h-full text-center p-6">
                            <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4">
                                <x-heroicon-o-camera class="w-8 h-8 text-gray-500 dark:text-gray-400" />
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Aktifkan Kamera</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Klik tombol di bawah untuk mengambil foto selfie</p>
                        </div>

                        <!-- Photo Preview -->
                        <div id="photo-preview" class="hidden relative w-full h-full">
                            <img id="captured-photo" class="w-full h-full object-cover">
                            <div class="absolute top-4 right-4">
                                <x-filament::button
                                    id="retake-photo"
                                    color="gray"
                                    size="sm"
                                    icon="heroicon-o-arrow-path"
                                >
                                    Ambil Ulang
                                </x-filament::button>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="flex flex-col items-center gap-4">
                        <!-- Primary Controls -->
                        <div class="flex gap-4">
                            <x-filament::button
                                id="start-camera-btn"
                                color="primary"
                                icon="heroicon-o-camera"
                            >
                                Aktifkan Kamera
                            </x-filament::button>

                            <x-filament::button
                                id="stop-camera-btn"
                                color="gray"
                                icon="heroicon-o-stop"
                                style="display: none;"
                            >
                                Matikan Kamera
                            </x-filament::button>
                        </div>

                        <!-- Action Controls -->
                        <div class="flex gap-4">
                            <x-filament::button
                                id="capture-btn"
                                color="success"
                                icon="heroicon-o-camera"
                                size="lg"
                                style="display: none;"
                            >
                                Ambil Foto
                            </x-filament::button>

                            <x-filament::button
                                id="submit-btn"
                                color="primary"
                                icon="heroicon-o-check"
                                size="lg"
                                style="display: none;"
                            >
                                Konfirmasi Absen {{ ucfirst($currentAction) }}
                            </x-filament::button>
                        </div>

                        <!-- Test Button (Development) -->
                        @if(config('app.debug'))
                            <x-filament::button
                                id="test-photo-btn"
                                color="warning"
                                icon="heroicon-o-beaker"
                                size="sm"
                                style="display: none;"
                            >
                                Test Photo Save
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        @else
            <!-- No Action Available -->
            <x-filament::section>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-s-check class="w-8 h-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        @if($todayAttendance && $todayAttendance->check_out)
                            Semua Absensi Selesai
                        @else
                            Belum Saatnya Absensi
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($todayAttendance && $todayAttendance->check_out)
                            Terima kasih! Semua absensi hari ini telah selesai.
                        @else
                            Silakan tunggu waktu absensi yang sesuai.
                        @endif
                    </p>
                </div>
            </x-filament::section>
        @endif
    </div>

    @push('scripts')
    <script>
        let stream;
        let currentLocation = null;
        let capturedPhoto = null;

        const currentAction = @json($currentAction);

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up dinas luar attendance...');
            setupEventListeners();
            updateTime();
            setInterval(updateTime, 1000);

            @if($currentAction)
                console.log('Current action available:', @json($currentAction));
                // Start geolocation immediately
                setTimeout(() => {
                    getCurrentLocation();
                }, 500); // Small delay to ensure DOM is ready
                hideCameraStatus();
            @else
                console.log('No current action available');
            @endif
        });

        // Event listeners
        function setupEventListeners() {
            const startCameraBtn = document.getElementById('start-camera-btn');
            const stopCameraBtn = document.getElementById('stop-camera-btn');
            const captureBtn = document.getElementById('capture-btn');
            const retakeBtn = document.getElementById('retake-photo');
            const submitBtn = document.getElementById('submit-btn');
            const testPhotoBtn = document.getElementById('test-photo-btn');
            const refreshLocationBtn = document.getElementById('refresh-location-btn');
            const manualLocationBtn = document.getElementById('manual-location-btn');
            const forceLocationBtn = document.getElementById('force-location-btn');

            startCameraBtn?.addEventListener('click', startCamera);
            stopCameraBtn?.addEventListener('click', stopCamera);
            captureBtn?.addEventListener('click', capturePhoto);
            retakeBtn?.addEventListener('click', retakePhoto);
            submitBtn?.addEventListener('click', submitAttendance);
            testPhotoBtn?.addEventListener('click', testPhoto);
            refreshLocationBtn?.addEventListener('click', () => {
                showNotification('Memperbarui lokasi...', 'info');
                getCurrentLocation();
            });
            manualLocationBtn?.addEventListener('click', () => {
                showNotification('Mencoba deteksi lokasi ulang...', 'info');
                currentLocation = null; // Reset location
                getCurrentLocation();
            });
            forceLocationBtn?.addEventListener('click', () => {
                // Force set location for debugging
                currentLocation = {
                    latitude: -6.2088, // Jakarta
                    longitude: 106.8456,
                    accuracy: 1000
                };
                updateLocationText(`${currentLocation.latitude}, ${currentLocation.longitude} (Manual)`);
                updateLocationStatus();
                showNotification('Lokasi dipaksa untuk testing', 'warning');
            });
        }

        // Time update
        function updateTime() {
            const timeElements = document.querySelectorAll('#current-time');
            timeElements.forEach(element => {
                const now = new Date();
                element.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            });
        }        // Location functions
        function getCurrentLocation() {
            console.log('Requesting geolocation...');
            updateLocationText('Mendeteksi lokasi...');

            if (!navigator.geolocation) {
                const errorMsg = 'GPS tidak didukung oleh browser ini';
                updateLocationText(errorMsg);
                showNotification(errorMsg, 'danger');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Geolocation success:', position);
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    console.log('Location set:', currentLocation);
                    updateLocationStatus();
                    updateLocationText(`${currentLocation.latitude.toFixed(6)}, ${currentLocation.longitude.toFixed(6)}`);
                    showNotification('Lokasi berhasil terdeteksi', 'success');
                },
                function(error) {
                    console.error('Error getting location:', error);
                    console.error('Error code:', error.code);
                    console.error('Error message:', error.message);

                    let errorMessage = 'Gagal mendeteksi lokasi: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Akses lokasi ditolak. Silakan izinkan akses lokasi pada browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Timeout. Mencoba lagi...';
                            break;
                        default:
                            errorMessage += 'Error tidak diketahui: ' + error.message;
                            break;
                    }
                    updateLocationText(errorMessage);
                    showNotification(errorMessage, 'warning');

                    // Auto retry for timeout or unavailable errors
                    if (error.code === error.TIMEOUT || error.code === error.POSITION_UNAVAILABLE) {
                        setTimeout(() => {
                            console.log('Retrying geolocation...');
                            getCurrentLocation();
                        }, 3000);
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000, // 15 seconds timeout
                    maximumAge: 30000 // 30 seconds cache
                }
            );
        }

        function updateLocationText(text) {
            const locationText = document.getElementById('location-text');
            if (locationText) {
                locationText.textContent = text;
            }
        }

        function updateLocationStatus() {
            if (!currentLocation) return;

            const locationStatus = document.getElementById('location-status');
            const locationInfo = document.getElementById('location-info');

            locationStatus.classList.remove('hidden');

            locationInfo.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Lokasi berhasil terdeteksi</span>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Koordinat:</strong> ${currentLocation.latitude.toFixed(6)}, ${currentLocation.longitude.toFixed(6)}</p>
                        <p><strong>Akurasi:</strong> ±${Math.round(currentLocation.accuracy)} meter</p>
                        <p class="text-gray-500 dark:text-gray-500 mt-2">Lokasi ini akan dicatat untuk keperluan absensi</p>
                    </div>
                </div>
            `;
        }

        // Camera functions
        function showCameraStatus(message) {
            const cameraStatus = document.getElementById('camera-status');
            const statusText = document.getElementById('status-text');
            if (cameraStatus && statusText) {
                statusText.textContent = message;
                cameraStatus.classList.remove('hidden');
            }
        }

        function hideCameraStatus() {
            const cameraStatus = document.getElementById('camera-status');
            if (cameraStatus) {
                cameraStatus.classList.add('hidden');
            }
        }

        function startCamera() {
            console.log('Starting camera...');
            showCameraStatus('Mengakses kamera...');
            initializeCamera();
        }

        function stopCamera() {
            console.log('Stopping camera...');
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            document.getElementById('camera').classList.add('hidden');
            document.getElementById('camera-placeholder').classList.remove('hidden');
            document.getElementById('stop-camera-btn').style.display = 'none';
            document.getElementById('capture-btn').style.display = 'none';
            document.getElementById('test-photo-btn').style.display = 'none';
            document.getElementById('start-camera-btn').style.display = 'block';

            hideCameraStatus();
            showNotification('Kamera dimatikan', 'info');
        }

        async function initializeCamera() {
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser tidak mendukung akses kamera');
                }

                const constraints = {
                    video: {
                        width: { ideal: 1280, max: 1920 },
                        height: { ideal: 720, max: 1080 },
                        facingMode: "user"
                    }
                };

                stream = await navigator.mediaDevices.getUserMedia(constraints);

                const videoElement = document.getElementById('camera');
                if (videoElement) {
                    videoElement.srcObject = stream;
                    videoElement.classList.remove('hidden');
                    document.getElementById('camera-placeholder').classList.add('hidden');

                    document.getElementById('start-camera-btn').style.display = 'none';
                    document.getElementById('stop-camera-btn').style.display = 'block';
                    document.getElementById('capture-btn').style.display = 'block';
                    document.getElementById('test-photo-btn').style.display = 'block';

                    hideCameraStatus();
                    showNotification('Kamera berhasil diaktifkan', 'success');
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

        function capturePhoto() {
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

            context.drawImage(video, 0, 0, canvasWidth, canvasHeight);
            capturedPhoto = canvas.toDataURL('image/jpeg', 0.6);

            // Show preview
            const capturedPhotoImg = document.getElementById('captured-photo');
            if (capturedPhotoImg) {
                capturedPhotoImg.src = capturedPhoto;
            }

            document.getElementById('camera').classList.add('hidden');
            document.getElementById('photo-preview').classList.remove('hidden');
            document.getElementById('capture-btn').style.display = 'none';
            document.getElementById('submit-btn').style.display = 'block';

            showNotification('Foto berhasil diambil!', 'success');
        }

        function retakePhoto() {
            capturedPhoto = null;
            document.getElementById('photo-preview').classList.add('hidden');
            document.getElementById('camera').classList.remove('hidden');
            document.getElementById('capture-btn').style.display = 'block';
            document.getElementById('submit-btn').style.display = 'none';
            showNotification('Siap untuk mengambil foto lagi', 'info');
        }

        function testPhoto() {
            if (!capturedPhoto) {
                showNotification('Silakan ambil foto terlebih dahulu dengan tombol "Ambil Foto".', 'danger');
                return;
            }

            const testBtn = document.getElementById('test-photo-btn');
            const originalText = testBtn.textContent;
            testBtn.disabled = true;
            testBtn.textContent = 'Testing...';

            @this.call('testPhotoSave', capturedPhoto)
                .then((result) => {
                    testBtn.textContent = originalText;
                    testBtn.disabled = false;
                })
                .catch((error) => {
                    testBtn.textContent = originalText;
                    testBtn.disabled = false;
                    showNotification('Error testing photo: ' + error.message, 'danger');
                });
        }

        function submitAttendance() {
            console.log('submitAttendance called', {
                currentAction: currentAction,
                capturedPhoto: capturedPhoto ? 'exists' : 'null',
                currentLocation: currentLocation
            });

            if (!capturedPhoto) {
                showNotification('Silakan ambil foto terlebih dahulu.', 'danger');
                return;
            }

            if (!currentLocation) {
                console.log('Location not available, trying to get location...');
                showNotification('Lokasi belum terdeteksi. Mencoba mendapatkan lokasi...', 'warning');

                // Try to get location immediately
                getCurrentLocation();

                // Wait a bit and try again
                setTimeout(() => {
                    if (currentLocation) {
                        console.log('Location obtained, retrying submit...');
                        submitAttendance(); // Retry after getting location
                    } else {
                        // Use fallback coordinates if still no location
                        console.log('Using fallback location...');
                        currentLocation = {
                            latitude: -6.2088, // Jakarta coordinates as fallback
                            longitude: 106.8456,
                            accuracy: 1000 // Large accuracy to indicate fallback
                        };
                        showNotification('Menggunakan lokasi default. Pastikan GPS aktif untuk akurasi yang lebih baik.', 'warning');
                        setTimeout(() => submitAttendance(), 1000); // Retry with fallback
                    }
                }, 5000);
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';

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

            console.log('Calling method:', methodName, 'with location:', currentLocation);

            if (methodName) {
                @this.call(methodName, capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                    .then((result) => {
                        console.log('Method call success:', result);
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        showNotification('Absensi berhasil disimpan!', 'success');
                        // Dispatch event for auto-refresh
                        @this.dispatch('attendance-submitted');
                    })
                    .catch((error) => {
                        console.error('Method call error:', error);
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        showNotification('Error: ' + (error.message || 'Terjadi kesalahan saat menyimpan absensi'), 'danger');
                    });
            } else {
                console.error('No method name found for action:', currentAction);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                showNotification('Error: Action tidak valid', 'danger');
            }
        }

        function showNotification(message, type = 'info') {
            console.log(`${type.toUpperCase()}: ${message}`);

            // Try different Livewire notification methods
            try {
                if (window.Livewire && window.Livewire.emit) {
                    // Livewire v2
                    window.Livewire.emit('notification', { message, type });
                } else if (window.Livewire && window.Livewire.dispatch) {
                    // Livewire v3
                    window.Livewire.dispatch('notification', { message, type });
                } else if (window.$wire && window.$wire.dispatchSelf) {
                    // Alternative Livewire v3 method
                    window.$wire.dispatchSelf('notification', { message, type });
                } else {
                    // Fallback: Create a toast-like notification
                    createToastNotification(message, type);
                }
            } catch (error) {
                console.error('Notification error:', error);
                createToastNotification(message, type);
            }
        }

        function createToastNotification(message, type) {
            // Create a simple toast notification as fallback
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white max-w-md transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'danger' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' :
                'bg-blue-500'
            }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        window.addEventListener('pagehide', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        // Listen for attendance events to auto-refresh
        document.addEventListener('livewire:init', () => {
            Livewire.on('attendance-submitted', () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
