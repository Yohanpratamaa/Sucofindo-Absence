<x-filament-panels::page>
    @php
        // Get data from component
        $canCheckInPagi = $this->canCheckInPagi;
        $canCheckInSiang = $this->canCheckInSiang;
        $canCheckOut = $this->canCheckOut;
        $canCheckIn = $this->canCheckIn;
        $todayAttendance = $this->todayAttendance;
        $attendanceType = $this->attendanceType;
        $canChangeType = $this->canChangeAttendanceType();
        $timeInfo = $this->getTimeWindowInfo();
        $progress = $this->getAttendanceProgress();
    @endphp

    <!-- Header with Attendance Type Selection -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pilih Jenis Absensi</h2>
        
        <div class="flex gap-4">
            <label class="flex items-center">
                <input type="radio" 
                       wire:model.live="attendanceType" 
                       value="WFO" 
                       {{ !$canChangeType ? 'disabled' : '' }}
                       class="mr-2">
                <span class="text-sm font-medium">WFO (Work From Office)</span>
            </label>
            <label class="flex items-center">
                <input type="radio" 
                       wire:model.live="attendanceType" 
                       value="Dinas Luar" 
                       {{ !$canChangeType ? 'disabled' : '' }}
                       class="mr-2">
                <span class="text-sm font-medium">Dinas Luar</span>
            </label>
        </div>

        @if(!$canChangeType)
            <p class="text-sm text-amber-600 mt-2">
                <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                Jenis absensi tidak dapat diubah setelah melakukan absensi hari ini.
            </p>
        @endif
    </div>

    <!-- Status Absensi Hari Ini -->
    @if($todayAttendance)
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Absensi Hari Ini</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                @if($attendanceType === 'WFO')
                    <!-- WFO Status -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-clock class="w-5 h-5 text-green-600 mr-2" />
                            <div>
                                <p class="text-sm font-medium text-green-800">Check In</p>
                                <p class="text-lg font-bold text-green-600">
                                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-clock class="w-5 h-5 text-blue-600 mr-2" />
                            <div>
                                <p class="text-sm font-medium text-blue-800">Check Out</p>
                                <p class="text-lg font-bold text-blue-600">
                                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Dinas Luar Status -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-sun class="w-5 h-5 text-yellow-600 mr-2" />
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Pagi</p>
                                <p class="text-lg font-bold text-yellow-600">
                                    {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-sun class="w-5 h-5 text-orange-600 mr-2" />
                            <div>
                                <p class="text-sm font-medium text-orange-800">Siang</p>
                                <p class="text-lg font-bold text-orange-600">
                                    {{ $todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-moon class="w-5 h-5 text-purple-600 mr-2" />
                            <div>
                                <p class="text-sm font-medium text-purple-800">Sore</p>
                                <p class="text-lg font-bold text-purple-600">
                                    {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress Absensi</span>
                    <span class="text-sm text-gray-500">{{ $progress['completed'] }}/{{ $progress['total'] }} selesai</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $progress['total'] > 0 ? ($progress['completed'] / $progress['total']) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informasi Waktu -->
    @if($attendanceType === 'Dinas Luar')
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Absensi Dinas Luar</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <p class="font-medium text-gray-900">Pagi</p>
                    <p class="text-gray-600">Kapan saja</p>
                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs mt-1">
                        Selalu Aktif
                    </span>
                </div>
                <div class="text-center">
                    <p class="font-medium text-gray-900">Siang</p>
                    <p class="text-gray-600">12:00 - 14:59</p>
                    <span class="inline-block px-2 py-1 rounded-full text-xs mt-1 {{ $timeInfo['siang_window']['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $timeInfo['siang_window']['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                <div class="text-center">
                    <p class="font-medium text-gray-900">Sore</p>
                    <p class="text-gray-600">≥ 15:00</p>
                    <span class="inline-block px-2 py-1 rounded-full text-xs mt-1 {{ $timeInfo['sore_window']['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $timeInfo['sore_window']['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
            <p class="text-center text-sm text-gray-500 mt-4">
                Waktu saat ini: <strong>{{ $timeInfo['current_time'] }}</strong>
            </p>
        </div>
    @endif

    <!-- Form Absensi -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        @php
            $showCamera = false;
            $buttonText = '';
            $buttonColor = 'primary';
            $actionFunction = '';
            
            if($attendanceType === 'WFO') {
                if($canCheckIn) {
                    $showCamera = true;
                    $buttonText = 'Check In WFO';
                    $actionFunction = 'processCheckIn';
                } elseif($canCheckOut) {
                    $showCamera = true;
                    $buttonText = 'Check Out WFO';
                    $actionFunction = 'processCheckOut';
                }
            } else {
                if($canCheckInPagi) {
                    $showCamera = true;
                    $buttonText = 'Absensi Pagi';
                    $actionFunction = 'processCheckInPagi';
                } elseif($canCheckInSiang) {
                    $showCamera = true;
                    $buttonText = 'Absensi Siang';
                    $actionFunction = 'processCheckInSiang';
                } elseif($canCheckOut) {
                    $showCamera = true;
                    $buttonText = 'Absensi Sore';
                    $actionFunction = 'processCheckOut';
                }
            }
        @endphp

        @if($showCamera)
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $buttonText }}</h3>
            
            <!-- Camera Section -->
            <div class="max-w-md mx-auto">
                <div class="bg-gray-100 rounded-lg p-4 mb-4">
                    <video id="camera" autoplay muted class="w-full rounded-lg bg-black" style="max-height: 300px;"></video>
                    <canvas id="snapshot" style="display: none;"></canvas>
                </div>
                
                <div class="text-center space-y-3">
                    <button type="button" 
                            id="startCamera" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <x-heroicon-o-camera class="w-4 h-4 inline mr-1" />
                        Buka Kamera
                    </button>
                    
                    <button type="button" 
                            id="takePhoto" 
                            style="display: none;"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <x-heroicon-o-camera class="w-4 h-4 inline mr-1" />
                        Ambil Foto
                    </button>
                    
                    <button type="button" 
                            id="submitAttendance" 
                            style="display: none;"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg text-lg font-medium">
                        {{ $buttonText }}
                    </button>
                    
                    <button type="button" 
                            id="retakePhoto" 
                            style="display: none;"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Foto Ulang
                    </button>
                </div>
                
                <div id="locationInfo" class="mt-4 text-sm text-gray-600 text-center" style="display: none;">
                    <p>📍 Mengambil lokasi...</p>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <x-heroicon-o-check-circle class="w-16 h-16 text-green-500 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Absensi Selesai</h3>
                <p class="text-gray-600">
                    @if($attendanceType === 'WFO')
                        @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                            Check out tersedia setelah jam 15:00
                        @else
                            Absensi WFO hari ini sudah selesai
                        @endif
                    @else
                        @if(!$todayAttendance)
                            Belum ada absensi hari ini
                        @else
                            Absensi dinas luar hari ini sudah selesai
                        @endif
                    @endif
                </p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        let currentStream = null;
        let photoData = null;
        let currentLocation = null;

        document.getElementById('startCamera').addEventListener('click', function() {
            startCamera();
        });

        document.getElementById('takePhoto').addEventListener('click', function() {
            takePhoto();
        });

        document.getElementById('submitAttendance').addEventListener('click', function() {
            submitAttendance();
        });

        document.getElementById('retakePhoto').addEventListener('click', function() {
            retakePhoto();
        });

        function startCamera() {
            // Get location first
            if (navigator.geolocation) {
                document.getElementById('locationInfo').style.display = 'block';
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    
                    document.getElementById('locationInfo').innerHTML = 
                        '<p>📍 Lokasi ditemukan: ' + currentLocation.latitude.toFixed(6) + ', ' + currentLocation.longitude.toFixed(6) + '</p>';
                    
                    // Start camera after getting location
                    navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        } 
                    })
                    .then(function(stream) {
                        currentStream = stream;
                        document.getElementById('camera').srcObject = stream;
                        document.getElementById('startCamera').style.display = 'none';
                        document.getElementById('takePhoto').style.display = 'inline-block';
                    })
                    .catch(function(err) {
                        alert('Error accessing camera: ' + err.message);
                    });
                    
                }, function(error) {
                    alert('Error getting location: ' + error.message);
                    document.getElementById('locationInfo').style.display = 'none';
                });
            } else {
                alert('Geolocation tidak didukung browser ini.');
            }
        }

        function takePhoto() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('snapshot');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);

            photoData = canvas.toDataURL('image/jpeg', 0.8);
            
            // Show photo in video element
            video.srcObject = null;
            video.src = photoData;
            
            // Stop camera stream
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }

            document.getElementById('takePhoto').style.display = 'none';
            document.getElementById('submitAttendance').style.display = 'inline-block';
            document.getElementById('retakePhoto').style.display = 'inline-block';
        }

        function retakePhoto() {
            document.getElementById('camera').src = '';
            document.getElementById('submitAttendance').style.display = 'none';
            document.getElementById('retakePhoto').style.display = 'none';
            document.getElementById('startCamera').style.display = 'inline-block';
            photoData = null;
        }

        function submitAttendance() {
            if (!photoData || !currentLocation) {
                alert('Foto dan lokasi diperlukan untuk absensi.');
                return;
            }

            const actionFunction = '{{ $actionFunction }}';
            if (!actionFunction) {
                alert('Tidak ada aksi yang dapat dilakukan saat ini.');
                return;
            }

            // Disable button to prevent double submission
            const submitBtn = document.getElementById('submitAttendance');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses...';

            // Call Livewire method
            @this.call(actionFunction, photoData, currentLocation.latitude, currentLocation.longitude)
                .then(() => {
                    // Reset form
                    retakePhoto();
                    currentLocation = null;
                    document.getElementById('locationInfo').style.display = 'none';
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses absensi.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '{{ $buttonText }}';
                });
        }

        // Cleanup on page leave
        window.addEventListener('beforeunload', function() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
