@php
use Carbon\Carbon;
@endphp

<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-4 rounded-lg border border-emerald-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-emerald-800">👤 Pegawai & Tanggal</h4>
                <p class="text-sm text-gray-700">{{ $record->user->nama }} ({{ $record->user->npp }})</p>
                <p class="text-xs text-gray-600">{{ $record->created_at->format('d F Y') }} - {{ $record->attendance_type }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-emerald-800">📍 Status Lokasi</h4>
                @if($record->attendance_type === 'WFO')
                    <p class="text-sm text-blue-700">🏢 Lokasi harus di area kantor</p>
                @else
                    <p class="text-sm text-yellow-700">🌍 Lokasi fleksibel (Dinas Luar)</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Check In Location -->
        <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-green-600 text-sm">🌅</span>
                </div>
                <div>
                    <h3 class="font-semibold text-green-800">Check In</h3>
                    @if($record->check_in)
                        <p class="text-xs text-green-600">{{ \Carbon\Carbon::parse($record->check_in)->format('H:i:s') }}</p>
                    @else
                        <p class="text-xs text-gray-500">Belum check in</p>
                    @endif
                </div>
            </div>

            @if($record->latitude_absen_masuk && $record->longitude_absen_masuk)
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📍 Koordinat</h4>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Latitude:</span>
                                <span class="font-mono">{{ number_format($record->latitude_absen_masuk, 8) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Longitude:</span>
                                <span class="font-mono">{{ number_format($record->longitude_absen_masuk, 8) }}</span>
                            </div>
                        </div>
                        <button onclick="copyToClipboard('{{ $record->latitude_absen_masuk }}, {{ $record->longitude_absen_masuk }}')" 
                                class="mt-2 text-xs bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded transition-colors">
                            📋 Copy Koordinat
                        </button>
                    </div>

                    @if($record->attendance_type === 'WFO' && isset($record->officeSchedule->office))
                        @php
                            $office = $record->officeSchedule->office;
                            $distance = calculateDistance(
                                $office->latitude,
                                $office->longitude,
                                $record->latitude_absen_masuk,
                                $record->longitude_absen_masuk
                            );
                        @endphp
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-700 mb-2">🏢 Validasi Kantor</h4>
                            <div class="text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kantor:</span>
                                    <span>{{ $office->nama }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jarak:</span>
                                    <span class="font-semibold {{ $distance <= 100 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $distance }}m
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-semibold {{ $distance <= 100 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $distance <= 100 ? '✅ Valid' : '❌ Terlalu Jauh' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <a href="https://www.google.com/maps?q={{ $record->latitude_absen_masuk }},{{ $record->longitude_absen_masuk }}" 
                           target="_blank" 
                           class="inline-flex items-center text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded transition-colors">
                            🗺️ Lihat di Google Maps
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">Lokasi tidak tersedia</p>
                </div>
            @endif
        </div>

        <!-- Absen Siang Location -->
        <div class="bg-white p-4 rounded-lg border border-yellow-200 shadow-sm">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-yellow-600 text-sm">☀️</span>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-800">Absen Siang</h3>
                    @if($record->attendance_type === 'WFO')
                        <p class="text-xs text-gray-500">Tidak diperlukan</p>
                    @elseif($record->absen_siang)
                        <p class="text-xs text-yellow-600">{{ \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s') }}</p>
                    @else
                        <p class="text-xs text-red-500">Belum absen siang</p>
                    @endif
                </div>
            </div>

            @if($record->attendance_type === 'WFO')
                <div class="text-center py-6 text-gray-400">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="mt-2 text-sm">WFO - Tidak perlu absen siang</p>
                </div>
            @elseif($record->latitude_absen_siang && $record->longitude_absen_siang)
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📍 Koordinat</h4>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Latitude:</span>
                                <span class="font-mono">{{ number_format($record->latitude_absen_siang, 8) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Longitude:</span>
                                <span class="font-mono">{{ number_format($record->longitude_absen_siang, 8) }}</span>
                            </div>
                        </div>
                        <button onclick="copyToClipboard('{{ $record->latitude_absen_siang }}, {{ $record->longitude_absen_siang }}')" 
                                class="mt-2 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded transition-colors">
                            📋 Copy Koordinat
                        </button>
                    </div>

                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <h4 class="text-sm font-medium text-yellow-700 mb-2">🌍 Status Dinas Luar</h4>
                        <p class="text-xs text-gray-600">Lokasi fleksibel sesuai dengan penugasan dinas luar</p>
                        <span class="inline-block mt-1 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                            ✅ Valid untuk Dinas Luar
                        </span>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <a href="https://www.google.com/maps?q={{ $record->latitude_absen_siang }},{{ $record->longitude_absen_siang }}" 
                           target="_blank" 
                           class="inline-flex items-center text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded transition-colors">
                            🗺️ Lihat di Google Maps
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">Lokasi tidak tersedia</p>
                </div>
            @endif
        </div>

        <!-- Check Out Location -->
        <div class="bg-white p-4 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600 text-sm">🌆</span>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-800">Check Out</h3>
                    @if($record->check_out)
                        <p class="text-xs text-blue-600">{{ \Carbon\Carbon::parse($record->check_out)->format('H:i:s') }}</p>
                    @else
                        <p class="text-xs text-gray-500">Belum check out</p>
                    @endif
                </div>
            </div>

            @if($record->latitude_absen_pulang && $record->longitude_absen_pulang)
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📍 Koordinat</h4>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Latitude:</span>
                                <span class="font-mono">{{ number_format($record->latitude_absen_pulang, 8) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Longitude:</span>
                                <span class="font-mono">{{ number_format($record->longitude_absen_pulang, 8) }}</span>
                            </div>
                        </div>
                        <button onclick="copyToClipboard('{{ $record->latitude_absen_pulang }}, {{ $record->longitude_absen_pulang }}')" 
                                class="mt-2 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded transition-colors">
                            📋 Copy Koordinat
                        </button>
                    </div>

                    @if($record->attendance_type === 'WFO' && isset($record->officeSchedule->office))
                        @php
                            $office = $record->officeSchedule->office;
                            $distance = calculateDistance(
                                $office->latitude,
                                $office->longitude,
                                $record->latitude_absen_pulang,
                                $record->longitude_absen_pulang
                            );
                        @endphp
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-700 mb-2">🏢 Validasi Kantor</h4>
                            <div class="text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kantor:</span>
                                    <span>{{ $office->nama }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jarak:</span>
                                    <span class="font-semibold {{ $distance <= 100 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $distance }}m
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-semibold {{ $distance <= 100 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $distance <= 100 ? '✅ Valid' : '❌ Terlalu Jauh' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <a href="https://www.google.com/maps?q={{ $record->latitude_absen_pulang }},{{ $record->longitude_absen_pulang }}" 
                           target="_blank" 
                           class="inline-flex items-center text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded transition-colors">
                            🗺️ Lihat di Google Maps
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm">Lokasi tidak tersedia</p>
                </div>
            @endif
        </div>
    </div>

    @if($record->attendance_type === 'WFO' && isset($record->officeSchedule->office))
        <!-- Office Info -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="font-semibold text-blue-800 mb-3">🏢 Informasi Kantor</h4>
            @php $office = $record->officeSchedule->office; @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Nama Kantor:</span>
                    <p class="font-medium">{{ $office->nama }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Koordinat Kantor:</span>
                    <p class="font-medium font-mono">{{ number_format($office->latitude, 6) }}, {{ number_format($office->longitude, 6) }}</p>
                </div>
                <div class="md:col-span-2">
                    <a href="https://www.google.com/maps?q={{ $office->latitude }},{{ $office->longitude }}" 
                       target="_blank" 
                       class="inline-flex items-center text-sm bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                        🗺️ Lihat Lokasi Kantor di Google Maps
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Summary -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-800 mb-3">📊 Ringkasan Lokasi</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full {{ ($record->latitude_absen_masuk && $record->longitude_absen_masuk) ? 'bg-green-400' : 'bg-gray-300' }} mr-2"></span>
                <span>Check In: {{ ($record->latitude_absen_masuk && $record->longitude_absen_masuk) ? 'Tersedia' : 'Tidak ada' }}</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full {{ ($record->attendance_type === 'WFO') ? 'bg-gray-300' : (($record->latitude_absen_siang && $record->longitude_absen_siang) ? 'bg-yellow-400' : 'bg-red-400') }} mr-2"></span>
                <span>Absen Siang: 
                    @if($record->attendance_type === 'WFO')
                        Tidak perlu
                    @else
                        {{ ($record->latitude_absen_siang && $record->longitude_absen_siang) ? 'Tersedia' : 'Tidak ada' }}
                    @endif
                </span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full {{ ($record->latitude_absen_pulang && $record->longitude_absen_pulang) ? 'bg-blue-400' : 'bg-gray-300' }} mr-2"></span>
                <span>Check Out: {{ ($record->latitude_absen_pulang && $record->longitude_absen_pulang) ? 'Tersedia' : 'Tidak ada' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform translate-x-full transition-transform z-50">
    <span id="toastMessage">Koordinat berhasil disalin!</span>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Koordinat berhasil disalin: ' + text);
    }, function(err) {
        console.error('Could not copy text: ', err);
        showToast('Gagal menyalin koordinat', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toastMessage.textContent = message;
    
    // Change color based on type
    if (type === 'error') {
        toast.className = toast.className.replace('bg-green-500', 'bg-red-500');
    } else {
        toast.className = toast.className.replace('bg-red-500', 'bg-green-500');
    }
    
    // Show toast
    toast.classList.remove('translate-x-full');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}
</script>

@php
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
        return 0;
    }

    $earthRadius = 6371000; // Earth radius in meters

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return round($earthRadius * $c);
}
@endphp
