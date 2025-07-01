<x-filament-panels::page>
    <!-- Current Status Section -->
    <x-filament::section>
        <x-slot name="heading">
            Status Absensi Hari Ini
        </x-slot>

        <x-slot name="description">
            {{ now()->format('d F Y') }}
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Check In Status -->
            <div class="rounded-lg border border-gray-300 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if($todayAttendance?->check_in)
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-check class="w-5 h-5 text-green-600" />
                            </div>
                        @else
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Check In</p>
                        <p class="text-lg font-semibold {{ $todayAttendance?->check_in ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $todayAttendance?->check_in?->format('H:i') ?? 'Belum check in' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Check Out Status -->
            <div class="rounded-lg border border-gray-300 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if($todayAttendance?->check_out)
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-check class="w-5 h-5 text-green-600" />
                            </div>
                        @else
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Check Out</p>
                        <p class="text-lg font-semibold {{ $todayAttendance?->check_out ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $todayAttendance?->check_out?->format('H:i') ?? 'Belum check out' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if($todayAttendance)
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mr-2" />
                    <span class="text-sm text-blue-700">
                        Tipe: {{ $todayAttendance->attendance_type }}
                        @if($todayAttendance->check_in && $todayAttendance->check_out)
                            | Durasi: {{ $todayAttendance->durasi_kerja ?? '-' }}
                        @else
                            | Sedang bekerja
                        @endif
                    </span>
                </div>
            </div>
        @endif
    </x-filament::section>

    <!-- Attendance Actions Section -->
    <x-filament::section>
        <x-slot name="heading">
            Aksi Absensi
        </x-slot>

        <x-slot name="description">
            Lakukan check in atau check out sesuai dengan waktu kerja Anda
        </x-slot>

        <div class="space-y-6">
            <!-- Current Time Display -->
            <div class="text-center py-4">
                <div class="text-3xl font-bold text-gray-900" id="current-time">
                    {{ now()->format('H:i:s') }}
                </div>
                <div class="text-sm text-gray-500">
                    Waktu Sekarang
                </div>
            </div>

            <!-- Location Status -->
            <div id="location-status" class="hidden text-center">
                <x-filament::badge id="location-badge" color="gray">
                    Mendeteksi lokasi...
                </x-filament::badge>
            </div>

            <!-- Action Buttons or Completion Message -->
            @if(!$canCheckIn && !$canCheckOut)
                <div class="text-center py-8">
                    <x-heroicon-o-check-circle class="w-16 h-16 text-green-500 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Absensi Selesai
                    </h3>
                    <p class="text-gray-600">
                        @if($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
                            Anda telah menyelesaikan absensi hari ini.
                        @elseif($todayAttendance)
                            Anda sudah melakukan absensi {{ $todayAttendance->attendance_type }} hari ini.
                        @else
                            Tidak ada absensi yang dapat dilakukan saat ini.
                        @endif
                    </p>
                </div>
            @else
                <!-- Camera and Photo Section -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <!-- Camera Placeholder -->
                    <div id="camera-placeholder" class="text-center py-8">
                        <x-heroicon-o-camera class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Ambil Foto Selfie</h3>
                        <p class="text-gray-600 mb-4">Klik tombol untuk mengaktifkan kamera dan ambil foto</p>

                        <button type="button" id="start-camera-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <x-heroicon-o-camera class="w-4 h-4 mr-2" />
                            Mulai Kamera
                        </button>
                    </div>

                    <!-- Camera Video -->
                    <div id="camera-container" class="hidden text-center">
                        <video id="camera" class="w-full max-w-md mx-auto h-64 object-cover rounded-lg mb-4" autoplay playsinline muted></video>

                        <div class="flex justify-center space-x-3">
                            <button type="button" id="capture-btn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <x-heroicon-o-photo class="w-4 h-4 mr-2" />
                                Ambil Foto
                            </button>

                            <button type="button" id="stop-camera-btn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                                Matikan
                            </button>
                        </div>
                    </div>

                    <!-- Photo Preview -->
                    <div id="photo-container" class="hidden text-center">
                        <img id="photo-preview" class="w-full max-w-md mx-auto h-64 object-cover rounded-lg mb-4" />

                        <button type="button" id="retake-btn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                            Ambil Ulang
                        </button>
                    </div>

                    <!-- Hidden canvas for photo capture -->
                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <!-- Submit Actions -->
                <div id="submit-container" class="hidden">
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if($canCheckIn)
                        <button type="button" id="submit-checkin" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                            Check In Sekarang
                        </button>
                    @endif

                    @if($canCheckOut)
                        <button type="button" id="submit-checkout" class="inline-flex items-center px-6 py-3 bg-orange-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 mr-2" />
                            Check Out Sekarang
                        </button>
                    @endif
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>

    @script
    <script>
        // Update current time every second
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        setInterval(updateTime, 1000);

        // Global variables
        let userLocation = null;
        let currentStream = null;
        let capturedPhoto = null;

        // Get user location
        function getUserLocation() {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };

                        // Show location status
                        const locationStatus = document.getElementById('location-status');
                        const locationBadge = document.getElementById('location-badge');
                        if (locationStatus && locationBadge) {
                            locationStatus.classList.remove('hidden');
                            locationBadge.textContent = `Lokasi terdeteksi: ${userLocation.latitude.toFixed(6)}, ${userLocation.longitude.toFixed(6)}`;
                            locationBadge.setAttribute('color', 'success');
                        }
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        const locationStatus = document.getElementById('location-status');
                        const locationBadge = document.getElementById('location-badge');
                        if (locationStatus && locationBadge) {
                            locationStatus.classList.remove('hidden');
                            locationBadge.textContent = 'Error mendapatkan lokasi';
                            locationBadge.setAttribute('color', 'danger');
                        }
                    }
                );
            }
        }

        // Camera functions
        async function startCamera() {
            console.log('startCamera function called');

            try {
                const constraints = {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                };

                console.log('Requesting camera access...');
                currentStream = await navigator.mediaDevices.getUserMedia(constraints);
                console.log('Camera access granted');

                const video = document.getElementById('camera');
                const placeholder = document.getElementById('camera-placeholder');
                const cameraContainer = document.getElementById('camera-container');

                if (video && placeholder && cameraContainer) {
                    console.log('Setting up video stream...');
                    video.srcObject = currentStream;

                    // Hide placeholder, show camera
                    placeholder.classList.add('hidden');
                    cameraContainer.classList.remove('hidden');

                    console.log('Camera started successfully');
                } else {
                    console.error('Required elements not found');
                }
            } catch (error) {
                console.error('Error accessing camera:', error);

                let errorMessage = 'Tidak dapat mengakses kamera. ';
                if (error.name === 'NotAllowedError') {
                    errorMessage += 'Akses kamera ditolak. Silakan izinkan akses kamera di browser.';
                } else if (error.name === 'NotFoundError') {
                    errorMessage += 'Kamera tidak ditemukan di perangkat ini.';
                } else {
                    errorMessage += error.message;
                }

                alert(errorMessage);
            }
        }

        function stopCamera() {
            console.log('stopCamera function called');

            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }

            const placeholder = document.getElementById('camera-placeholder');
            const cameraContainer = document.getElementById('camera-container');
            const photoContainer = document.getElementById('photo-container');

            if (placeholder && cameraContainer && photoContainer) {
                placeholder.classList.remove('hidden');
                cameraContainer.classList.add('hidden');
                photoContainer.classList.add('hidden');
            }

            capturedPhoto = null;
            hideSubmitButtons();
        }

        function capturePhoto() {
            console.log('capturePhoto function called');

            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('photo-preview');
            const cameraContainer = document.getElementById('camera-container');
            const photoContainer = document.getElementById('photo-container');

            if (video && canvas && preview && cameraContainer && photoContainer) {
                console.log('Capturing photo...');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0);

                capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
                preview.src = capturedPhoto;

                // Switch from camera to photo preview
                cameraContainer.classList.add('hidden');
                photoContainer.classList.remove('hidden');

                // Show submit buttons
                showSubmitButtons();

                console.log('Photo captured successfully');
            } else {
                console.error('Required elements not found for photo capture');
            }
        }

        function retakePhoto() {
            console.log('retakePhoto function called');

            const cameraContainer = document.getElementById('camera-container');
            const photoContainer = document.getElementById('photo-container');

            if (cameraContainer && photoContainer) {
                photoContainer.classList.add('hidden');
                cameraContainer.classList.remove('hidden');

                capturedPhoto = null;
                hideSubmitButtons();
            }
        }

        function showSubmitButtons() {
            const submitContainer = document.getElementById('submit-container');
            if (submitContainer) {
                submitContainer.classList.remove('hidden');
            }
        }

        function hideSubmitButtons() {
            const submitContainer = document.getElementById('submit-container');
            if (submitContainer) {
                submitContainer.classList.add('hidden');
            }
        }

        function submitAttendance(type) {
            if (!capturedPhoto) {
                alert('Silakan ambil foto terlebih dahulu');
                return;
            }

            if (!userLocation) {
                alert('Lokasi belum terdeteksi. Silakan tunggu sebentar dan coba lagi.');
                return;
            }

            // Show loading state
            const btn = document.getElementById(`submit-${type}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
            }

            // Call Livewire method
            if (type === 'checkin') {
                $wire.processCheckIn(capturedPhoto, userLocation.latitude, userLocation.longitude);
            } else if (type === 'checkout') {
                $wire.processCheckOut(capturedPhoto, userLocation.latitude, userLocation.longitude);
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up event listeners...');

            // Get location first
            getUserLocation();

            // Camera controls - with detailed logging
            const startCameraBtn = document.getElementById('start-camera-btn');
            const captureBtn = document.getElementById('capture-btn');
            const stopCameraBtn = document.getElementById('stop-camera-btn');
            const retakeBtn = document.getElementById('retake-btn');
            const submitCheckinBtn = document.getElementById('submit-checkin');
            const submitCheckoutBtn = document.getElementById('submit-checkout');

            if (startCameraBtn) {
                console.log('Found start camera button, adding event listener');
                startCameraBtn.addEventListener('click', function(e) {
                    console.log('Start camera button clicked');
                    e.preventDefault();
                    startCamera();
                });
            } else {
                console.error('Start camera button not found!');
            }

            if (captureBtn) {
                console.log('Found capture button, adding event listener');
                captureBtn.addEventListener('click', function(e) {
                    console.log('Capture button clicked');
                    e.preventDefault();
                    capturePhoto();
                });
            }

            if (stopCameraBtn) {
                console.log('Found stop camera button, adding event listener');
                stopCameraBtn.addEventListener('click', function(e) {
                    console.log('Stop camera button clicked');
                    e.preventDefault();
                    stopCamera();
                });
            }

            if (retakeBtn) {
                console.log('Found retake button, adding event listener');
                retakeBtn.addEventListener('click', function(e) {
                    console.log('Retake button clicked');
                    e.preventDefault();
                    retakePhoto();
                });
            }

            // Submit buttons
            if (submitCheckinBtn) {
                console.log('Found check-in button, adding event listener');
                submitCheckinBtn.addEventListener('click', function(e) {
                    console.log('Check-in button clicked');
                    e.preventDefault();
                    submitAttendance('checkin');
                });
            }

            if (submitCheckoutBtn) {
                console.log('Found check-out button, adding event listener');
                submitCheckoutBtn.addEventListener('click', function(e) {
                    console.log('Check-out button clicked');
                    e.preventDefault();
                    submitAttendance('checkout');
                });
            }

            console.log('All event listeners set up');
        });

        // Clean up camera stream when page unloads
        window.addEventListener('beforeunload', function() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
        });

        // Listen for Livewire events to refresh page after attendance
        Livewire.on('attendance-submitted', () => {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
            location.reload();
        });
    </script>
    @endscript
</x-filament-panels::page>
