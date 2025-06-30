<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Absensi Work From Office</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ date('d M Y') }}</p>
                <p class="text-sm text-gray-500" id="location-text">Mendeteksi lokasi...</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-900 dark:text-white" id="current-time">--:--:--</div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    @if($todayAttendance)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($todayAttendance->check_in)
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Check In</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $todayAttendance->check_in->format('H:i') }}</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Berhasil
                    </span>
                </div>
            </div>
        </div>
        @endif

        @if($todayAttendance->check_out)
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Check Out</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $todayAttendance->check_out->format('H:i') }}</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Berhasil
                    </span>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Check Out</h3>
                    <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">Belum checkout</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Menunggu
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Location Status -->
    <div id="location-status" class="bg-white dark:bg-gray-900 shadow rounded-lg p-4" style="display: none;">
        <div class="flex items-center mb-3">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Lokasi</h3>
        </div>
        <div id="location-info"></div>
    </div>

    <!-- Camera Section -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <div class="mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Kamera Selfie</h2>
            <p class="text-gray-600 dark:text-gray-400">Ambil foto untuk absensi</p>
        </div>

        <!-- Status Message -->
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-blue-800 dark:text-blue-200">
                    @if(!$canCheckIn && !$canCheckOut)
                        @if($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
                            Anda sudah melakukan check in dan check out hari ini.
                        @elseif($todayAttendance)
                            Anda sudah melakukan absensi hari ini dengan tipe {{ $todayAttendance->attendance_type }}.
                        @else
                            Tidak ada jadwal absensi yang aktif.
                        @endif
                    @elseif($canCheckIn)
                        Siap untuk check in. Klik tombol "Mulai Kamera" untuk memulai.
                    @elseif($canCheckOut)
                        Siap untuk check out. Klik tombol "Mulai Kamera" untuk memulai.
                    @endif
                </p>
            </div>
        </div>

        <!-- Camera Display -->
        <div class="mb-6">
            <div id="camera-placeholder" class="bg-gray-100 dark:bg-gray-800 rounded-lg h-64 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">Kamera belum diaktifkan</p>
                </div>
            </div>
            <video id="camera" class="w-full h-64 rounded-lg object-cover" autoplay playsinline muted style="display: none;"></video>
            <div id="photo-preview" class="relative" style="display: none;">
                <img id="captured-photo" class="w-full h-64 rounded-lg object-cover" alt="Foto yang diambil">
                <button type="button" id="retake-photo" class="absolute top-2 right-2 bg-white rounded-lg p-2 shadow-md hover:bg-gray-50">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
            @if($canCheckIn || $canCheckOut)
                <button type="button" id="start-camera-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Mulai Kamera
                </button>

                <button type="button" id="stop-camera-btn" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700" style="display: none;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Stop Kamera
                </button>

                <button type="button" id="capture-btn" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50" style="display: none;" disabled>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    </svg>
                    Ambil Foto
                </button>

                <button type="button" id="submit-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" style="display: none;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                    @if($canCheckIn)
                        Check In
                    @elseif($canCheckOut)
                        Check Out
                    @endif
                </button>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let stream = null;
    let capturedImageData = null;
    let userLocation = null;

    // Update time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current-time').textContent = timeString;
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Get location
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                document.getElementById('location-text').textContent =
                    `Lat: ${userLocation.latitude.toFixed(6)}, Lng: ${userLocation.longitude.toFixed(6)}`;

                // Show location status
                document.getElementById('location-status').style.display = 'block';
                document.getElementById('location-info').innerHTML =
                    '<div class="p-3 rounded-lg text-green-800 bg-green-100">Lokasi berhasil didapatkan</div>';
            },
            function(error) {
                document.getElementById('location-text').textContent = 'Error mendapatkan lokasi';
            }
        );
    }

    // Camera functions
    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' }
            });
            const video = document.getElementById('camera');
            video.srcObject = stream;

            document.getElementById('camera-placeholder').style.display = 'none';
            video.style.display = 'block';
            document.getElementById('start-camera-btn').style.display = 'none';
            document.getElementById('stop-camera-btn').style.display = 'inline-flex';
            document.getElementById('capture-btn').style.display = 'inline-flex';
            document.getElementById('capture-btn').disabled = false;
        } catch (error) {
            alert('Tidak dapat mengakses kamera');
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        document.getElementById('camera').style.display = 'none';
        document.getElementById('camera-placeholder').style.display = 'flex';
        document.getElementById('start-camera-btn').style.display = 'inline-flex';
        document.getElementById('stop-camera-btn').style.display = 'none';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('submit-btn').style.display = 'none';
    }

    function capturePhoto() {
        const video = document.getElementById('camera');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        capturedImageData = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('captured-photo').src = capturedImageData;

        video.style.display = 'none';
        document.getElementById('photo-preview').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('submit-btn').style.display = 'inline-flex';
    }

    function retakePhoto() {
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('camera').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'inline-flex';
        document.getElementById('submit-btn').style.display = 'none';
    }

    async function submitAttendance() {
        if (!capturedImageData || !userLocation) {
            alert('Foto dan lokasi harus tersedia');
            return;
        }

        // Here you would call the Livewire method
        // For now, just show success message
        alert('Absensi berhasil! (Demo)');
        location.reload();
    }

    // Event listeners
    document.getElementById('start-camera-btn')?.addEventListener('click', startCamera);
    document.getElementById('stop-camera-btn')?.addEventListener('click', stopCamera);
    document.getElementById('capture-btn')?.addEventListener('click', capturePhoto);
    document.getElementById('retake-photo')?.addEventListener('click', retakePhoto);
    document.getElementById('submit-btn')?.addEventListener('click', submitAttendance);
});
</script>
