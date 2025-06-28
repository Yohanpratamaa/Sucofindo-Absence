@php
use Carbon\Carbon;
@endphp

<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h4 class="font-semibold text-gray-800">👤 Informasi Pegawai</h4>
                <p class="text-sm text-gray-600">{{ $record->user->nama }}</p>
                <p class="text-xs text-gray-500">NPP: {{ $record->user->npp }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">📅 Tanggal & Tipe</h4>
                <p class="text-sm text-gray-600">{{ $record->created_at->format('d F Y') }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $record->attendance_type === 'WFO' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $record->attendance_type }}
                </span>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">⏰ Status Kehadiran</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $record->status_kehadiran === 'Tepat Waktu' ? 'bg-green-100 text-green-800' :
                       ($record->status_kehadiran === 'Terlambat' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ $record->status_kehadiran }}
                </span>
            </div>
        </div>
    </div>

    <!-- Photos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Photo Check In -->
        <div class="space-y-3">
            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                <h3 class="font-semibold text-green-800 flex items-center">
                    🌅 Check In
                    @if($record->check_in)
                        <span class="ml-2 text-sm font-normal">{{ \Carbon\Carbon::parse($record->check_in)->format('H:i:s') }}</span>
                    @endif
                </h3>
                @if($record->check_in)
                    @php
                        $deadline = \Carbon\Carbon::parse($record->jam_masuk_standar ?? '08:00:00');
                        $checkInTime = \Carbon\Carbon::parse($record->check_in);
                        $isLate = $checkInTime->gt($deadline);
                    @endphp
                    <div class="flex items-center mt-1">
                        <span class="text-xs px-2 py-1 rounded-full {{ $isLate ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $isLate ? '🔴 Terlambat' : '🟢 Tepat Waktu' }}
                        </span>
                    </div>
                @else
                    <span class="text-xs text-gray-500">Belum check in</span>
                @endif
            </div>

            @if($record->picture_absen_masuk)
                <div class="relative group">
                    <img src="{{ $record->picture_absen_masuk_url }}"
                         alt="Foto Check In"
                         class="w-full h-64 object-cover rounded-lg shadow-md border-2 border-green-200 cursor-pointer hover:shadow-lg transition-shadow"
                         onclick="openImageModal('{{ $record->picture_absen_masuk_url }}', 'Foto Check In - {{ $record->user->nama }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                        <span class="text-white opacity-0 group-hover:opacity-100 font-medium">🔍 Klik untuk memperbesar</span>
                    </div>
                </div>

                @if($record->latitude_absen_masuk && $record->longitude_absen_masuk)
                    <div class="text-xs text-gray-600 space-y-1">
                        <p>📍 <strong>Lokasi:</strong></p>
                        <p>Lat: {{ number_format($record->latitude_absen_masuk, 6) }}</p>
                        <p>Lng: {{ number_format($record->longitude_absen_masuk, 6) }}</p>
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
                            <p class="text-blue-600">🏢 Jarak dari kantor: {{ $distance }}m</p>
                        @endif
                    </div>
                @endif
            @else
                <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <p class="mt-2 text-sm">Tidak ada foto</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Photo Absen Siang -->
        <div class="space-y-3">
            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                <h3 class="font-semibold text-yellow-800 flex items-center">
                    ☀️ Absen Siang
                    @if($record->absen_siang)
                        <span class="ml-2 text-sm font-normal">{{ \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s') }}</span>
                    @endif
                </h3>
                @if($record->attendance_type === 'WFO')
                    <span class="text-xs text-gray-500">Tidak diperlukan untuk WFO</span>
                @elseif($record->absen_siang)
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">🟡 Selesai</span>
                @else
                    <span class="text-xs text-red-500">⚠️ Belum absen siang</span>
                @endif
            </div>

            @if($record->attendance_type === 'Dinas Luar')
                @if($record->picture_absen_siang)
                    <div class="relative group">
                        <img src="{{ $record->picture_absen_siang_url }}"
                             alt="Foto Absen Siang"
                             class="w-full h-64 object-cover rounded-lg shadow-md border-2 border-yellow-200 cursor-pointer hover:shadow-lg transition-shadow"
                             onclick="openImageModal('{{ $record->picture_absen_siang_url }}', 'Foto Absen Siang - {{ $record->user->nama }}')"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                            <span class="text-white opacity-0 group-hover:opacity-100 font-medium">🔍 Klik untuk memperbesar</span>
                        </div>
                    </div>

                    @if($record->latitude_absen_siang && $record->longitude_absen_siang)
                        <div class="text-xs text-gray-600 space-y-1">
                            <p>📍 <strong>Lokasi:</strong></p>
                            <p>Lat: {{ number_format($record->latitude_absen_siang, 6) }}</p>
                            <p>Lng: {{ number_format($record->longitude_absen_siang, 6) }}</p>
                            <p class="text-yellow-600">🌍 Lokasi Dinas Luar</p>
                        </div>
                    @endif
                @else
                    <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <p class="mt-2 text-sm">Tidak ada foto</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="w-full h-64 bg-gray-50 rounded-lg border-2 border-gray-200 flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="mt-2 text-sm">WFO - Tidak perlu absen siang</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Photo Check Out -->
        <div class="space-y-3">
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-blue-800 flex items-center">
                    🌆 Check Out
                    @if($record->check_out)
                        <span class="ml-2 text-sm font-normal">{{ \Carbon\Carbon::parse($record->check_out)->format('H:i:s') }}</span>
                    @endif
                </h3>
                @if($record->check_out)
                    @php
                        $standardOut = \Carbon\Carbon::parse($record->jam_keluar_standar ?? '17:00:00');
                        $checkOutTime = \Carbon\Carbon::parse($record->check_out);
                        $isEarly = $checkOutTime->lt($standardOut);
                    @endphp
                    <div class="flex items-center mt-1">
                        <span class="text-xs px-2 py-1 rounded-full {{ $isEarly ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $isEarly ? '🟡 Pulang Cepat' : '🔵 Normal' }}
                        </span>
                    </div>
                @else
                    <span class="text-xs text-gray-500">Belum check out</span>
                @endif
            </div>

            @if($record->picture_absen_pulang)
                <div class="relative group">
                    <img src="{{ $record->picture_absen_pulang_url }}"
                         alt="Foto Check Out"
                         class="w-full h-64 object-cover rounded-lg shadow-md border-2 border-blue-200 cursor-pointer hover:shadow-lg transition-shadow"
                         onclick="openImageModal('{{ $record->picture_absen_pulang_url }}', 'Foto Check Out - {{ $record->user->nama }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                        <span class="text-white opacity-0 group-hover:opacity-100 font-medium">🔍 Klik untuk memperbesar</span>
                    </div>
                </div>

                @if($record->latitude_absen_pulang && $record->longitude_absen_pulang)
                    <div class="text-xs text-gray-600 space-y-1">
                        <p>📍 <strong>Lokasi:</strong></p>
                        <p>Lat: {{ number_format($record->latitude_absen_pulang, 6) }}</p>
                        <p>Lng: {{ number_format($record->longitude_absen_pulang, 6) }}</p>
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
                            <p class="text-blue-600">🏢 Jarak dari kantor: {{ $distance }}m</p>
                        @endif
                    </div>
                @endif
            @else
                <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <p class="mt-2 text-sm">Tidak ada foto</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Info -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-800 mb-3">📊 Ringkasan Absensi</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Durasi Kerja:</span>
                <p class="font-medium">{{ $record->durasi_kerja ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-600">Overtime:</span>
                <p class="font-medium">{{ $record->overtime_formatted ?? 'Tidak ada' }}</p>
            </div>
            <div>
                <span class="text-gray-600">Kelengkapan:</span>
                @php $kelengkapan = $record->kelengkapan_absensi; @endphp
                <p class="font-medium">{{ $kelengkapan['completed'] }}/{{ $kelengkapan['total'] }} - {{ $kelengkapan['status'] }}</p>
            </div>
            <div>
                <span class="text-gray-600">Detail Keterlambatan:</span>
                <p class="font-medium">{{ $record->keterlambatan_detail ?? 'Tidak ada' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
    <div class="relative max-w-4xl max-h-full p-4">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-6 right-6 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div id="modalTitle" class="absolute bottom-6 left-6 text-white bg-black bg-opacity-50 px-4 py-2 rounded-lg"></div>
    </div>
</div>

<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal on backdrop click
document.getElementById('imageModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeImageModal();
    }
});
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
