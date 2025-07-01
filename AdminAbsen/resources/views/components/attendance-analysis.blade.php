@php
use Carbon\Carbon;
@endphp

<div class="space-y-6">
    <!-- Header Summary -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-3">👤 Informasi Pegawai</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium">{{ $record->user->nama }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">NPP:</span>
                        <span class="font-medium">{{ $record->user->npp }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jabatan:</span>
                        <span class="font-medium">{{ $record->user->jabatan ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Detail Absensi</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="font-medium">{{ $record->created_at->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Hari:</span>
                        <span class="font-medium">{{ $record->created_at->format('l') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipe:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $record->attendance_type === 'WFO' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $record->attendance_type }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Analysis -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Working Hours Analysis -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⏰ Analisis Waktu</h3>

            <div class="space-y-4">
                @php
                    $jamMasukStandar = $record->jam_masuk_standar ?? '08:00:00';
                    $jamKeluarStandar = $record->jam_keluar_standar ?? '17:00:00';
                    $checkIn = $record->check_in ? \Carbon\Carbon::parse($record->check_in) : null;
                    $checkOut = $record->check_out ? \Carbon\Carbon::parse($record->check_out) : null;
                    $standardIn = \Carbon\Carbon::parse($jamMasukStandar);
                    $standardOut = \Carbon\Carbon::parse($jamKeluarStandar);
                @endphp

                <!-- Check In Analysis -->
                <div class="p-3 rounded-lg {{ $checkIn && $checkIn->gt($standardIn) ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Check In</span>
                        <span class="text-sm {{ $checkIn && $checkIn->gt($standardIn) ? 'text-red-600' : 'text-green-600' }}">
                            {{ $checkIn ? $checkIn->format('H:i:s') : 'Belum check in' }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        Standard: {{ $standardIn->format('H:i:s') }}
                        @if($checkIn && $checkIn->gt($standardIn))
                            | Terlambat: {{ $standardIn->diff($checkIn)->format('%H:%I:%S') }}
                        @elseif($checkIn)
                            | {{ $checkIn->lt($standardIn) ? 'Lebih awal: ' . $checkIn->diff($standardIn)->format('%H:%I:%S') : 'Tepat waktu' }}
                        @endif
                    </div>
                </div>

                <!-- Check Out Analysis -->
                @if($checkOut)
                <div class="p-3 rounded-lg {{ $checkOut->lt($standardOut) ? 'bg-yellow-50 border border-yellow-200' : 'bg-green-50 border border-green-200' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Check Out</span>
                        <span class="text-sm {{ $checkOut->lt($standardOut) ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $checkOut->format('H:i:s') }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        Standard: {{ $standardOut->format('H:i:s') }}
                        @if($checkOut->lt($standardOut))
                            | Pulang lebih awal: {{ $checkOut->diff($standardOut)->format('%H:%I:%S') }}
                        @else
                            | {{ $checkOut->gt($standardOut) ? 'Lembur: ' . $standardOut->diff($checkOut)->format('%H:%I:%S') : 'Sesuai jadwal' }}
                        @endif
                    </div>
                </div>
                @endif

                <!-- Work Duration -->
                @if($checkIn && $checkOut)
                @php
                    $workDuration = $checkIn->diff($checkOut);
                    $standardDuration = $standardIn->diff($standardOut);
                @endphp
                <div class="p-3 rounded-lg bg-blue-50 border border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Durasi Kerja</span>
                        <span class="text-sm text-blue-600">
                            {{ $workDuration->format('%H:%I:%S') }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        Standard: {{ $standardDuration->format('%H:%I:%S') }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Status & Compliance -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Status & Kelengkapan</h3>

            <div class="space-y-4">
                <!-- Attendance Status -->
                <div class="p-3 rounded-lg border">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Status Kehadiran</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $record->status_kehadiran === 'Tepat Waktu' ? 'bg-green-100 text-green-800' :
                               ($record->status_kehadiran === 'Terlambat' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $record->status_kehadiran }}
                        </span>
                    </div>
                    @if($record->keterlambatan_detail)
                    <div class="text-xs text-gray-600 mt-1">
                        {{ $record->keterlambatan_detail }}
                    </div>
                    @endif
                </div>

                <!-- Attendance Completeness -->
                @php
                    $kelengkapan = $record->kelengkapan_absensi;
                @endphp
                <div class="p-3 rounded-lg border">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Kelengkapan Absensi</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $kelengkapan['status'] === 'Lengkap' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $kelengkapan['completed'] }}/{{ $kelengkapan['total'] }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        {{ $kelengkapan['status'] }}
                    </div>
                </div>

                <!-- Requirements Check -->
                <div class="p-3 rounded-lg border">
                    <div class="font-medium mb-2">Requirement Checklist</div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->check_in ? '✅' : '❌' }}</span>
                            <span>Check In {{ $record->check_in ? '(' . \Carbon\Carbon::parse($record->check_in)->format('H:i:s') . ')' : '(Belum)' }}</span>
                        </div>

                        @if($record->attendance_type === 'Dinas Luar')
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->absen_siang ? '✅' : '❌' }}</span>
                            <span>Absen Siang {{ $record->absen_siang ? '(' . \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s') . ')' : '(Belum)' }}</span>
                        </div>
                        @endif

                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->check_out ? '✅' : '❌' }}</span>
                            <span>Check Out {{ $record->check_out ? '(' . \Carbon\Carbon::parse($record->check_out)->format('H:i:s') . ')' : '(Belum)' }}</span>
                        </div>

                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->picture_absen_masuk ? '✅' : '❌' }}</span>
                            <span>Foto Check In</span>
                        </div>

                        @if($record->attendance_type === 'Dinas Luar')
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->picture_absen_siang ? '✅' : '❌' }}</span>
                            <span>Foto Absen Siang</span>
                        </div>
                        @endif

                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2">{{ $record->picture_absen_pulang ? '✅' : '❌' }}</span>
                            <span>Foto Check Out</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Analysis (if available) -->
    @if($record->latitude_absen_masuk || $record->latitude_absen_siang || $record->latitude_absen_pulang)
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Analisis Lokasi</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($record->latitude_absen_masuk)
            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">🌅 Lokasi Check In</h4>
                <div class="text-xs space-y-1">
                    <div>Lat: {{ number_format($record->latitude_absen_masuk, 6) }}</div>
                    <div>Lng: {{ number_format($record->longitude_absen_masuk, 6) }}</div>
                    @if($record->attendance_type === 'WFO' && $record->officeSchedule?->office)
                        @php
                            $office = $record->officeSchedule->office;
                            $distance = \App\Filament\Resources\AttendanceResource::calculateDistance(
                                $office->latitude,
                                $office->longitude,
                                $record->latitude_absen_masuk,
                                $record->longitude_absen_masuk
                            );
                        @endphp
                        <div class="mt-1 text-{{ $distance <= 100 ? 'green' : 'red' }}-600">
                            Jarak dari kantor: {{ $distance }}m
                        </div>
                    @endif
                </div>
            </div>
            @endif

            @if($record->latitude_absen_siang && $record->attendance_type === 'Dinas Luar')
            <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">☀️ Lokasi Absen Siang</h4>
                <div class="text-xs space-y-1">
                    <div>Lat: {{ number_format($record->latitude_absen_siang, 6) }}</div>
                    <div>Lng: {{ number_format($record->longitude_absen_siang, 6) }}</div>
                </div>
            </div>
            @endif

            @if($record->latitude_absen_pulang)
            <div class="p-3 rounded-lg bg-red-50 border border-red-200">
                <h4 class="font-medium text-red-800 mb-2">🌇 Lokasi Check Out</h4>
                <div class="text-xs space-y-1">
                    <div>Lat: {{ number_format($record->latitude_absen_pulang, 6) }}</div>
                    <div>Lng: {{ number_format($record->longitude_absen_pulang, 6) }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Performance Metrics -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Metrik Performa</h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 rounded-lg bg-blue-50">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $record->attendance_type === 'WFO' ? '2' : '3' }}
                </div>
                <div class="text-xs text-gray-600">Absensi Required</div>
            </div>

            <div class="text-center p-3 rounded-lg bg-green-50">
                <div class="text-2xl font-bold text-green-600">
                    {{ $kelengkapan['completed'] }}
                </div>
                <div class="text-xs text-gray-600">Absensi Completed</div>
            </div>

            <div class="text-center p-3 rounded-lg bg-yellow-50">
                <div class="text-2xl font-bold text-yellow-600">
                    {{ number_format(($kelengkapan['completed'] / $kelengkapan['total']) * 100, 0) }}%
                </div>
                <div class="text-xs text-gray-600">Completion Rate</div>
            </div>

            <div class="text-center p-3 rounded-lg bg-purple-50">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $record->overtime ?? 0 }}
                </div>
                <div class="text-xs text-gray-600">Overtime (min)</div>
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    @if($record->absensi_requirement)
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h4 class="font-medium text-gray-800 mb-2">📝 Requirement Details</h4>
        <p class="text-sm text-gray-600">{{ $record->absensi_requirement }}</p>
    </div>
    @endif
</div>
