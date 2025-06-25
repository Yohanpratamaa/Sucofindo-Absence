<x-filament-panels::page>
    {{-- Custom CSS to hide Filament branding --}}
    <style>
        /* Hide Filament info widget and branding */
        .fi-widget[data-widget="filament-widgets-filament-info-widget"],
        .filament-widgets-filament-info-widget,
        .fi-wi-info,
        [class*="filament-info"] {
            display: none !important;
        }

        /* Hide version text and links */
        .fi-topbar-item:has(a[href*="filamentphp.com"]),
        .fi-topbar-item:has(a[href*="github.com/filamentphp"]),
        a[href*="filamentphp.com"],
        a[href*="github.com/filamentphp"],
        *:contains("v3.3.28"),
        *:contains("filament") {
            display: none !important;
        }

        /* Hide footer */
        .fi-footer,
        .fi-simple-footer {
            display: none !important;
        }

        /* Ensure our widgets are visible */
        .dashboard-widgets .fi-widget {
            display: block !important;
        }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        <!-- Welcome Card -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Selamat Datang</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->nama ?? 'Admin' }}</p>
                </div>
            </div>
        </div>

        <!-- Total Karyawan -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Total Karyawan</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Pegawai::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Absensi Hari Ini -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Absensi Hari Ini</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Attendance::today()->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Absensi Bulan Ini -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Absensi Bulan Ini</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Attendance::thisMonth()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Attendance -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Absensi Terbaru</h3>
            </div>
            <div class="px-6 py-4">
                @php
                    $recentAttendances = \App\Models\Attendance::with('user')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                @endphp

                @if($recentAttendances->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentAttendances as $attendance)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ substr($attendance->user->nama ?? 'N/A', 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $attendance->user->nama ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $attendance->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $attendance->status_kehadiran === 'Tepat Waktu' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $attendance->status_kehadiran === 'Terlambat' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $attendance->status_kehadiran === 'Tidak Hadir' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ $attendance->status_kehadiran }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ \App\Filament\Resources\AttendanceResource::getUrl() }}"
                           class="text-sm text-blue-600 hover:text-blue-500">
                            Lihat semua absensi →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada data absensi.</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Menu Utama</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    <a href="{{ \App\Filament\Resources\PegawaiResource::getUrl() }}"
                       class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Manajemen Pegawai
                    </a>

                    <a href="{{ \App\Filament\Resources\AttendanceResource::getUrl() }}"
                       class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Manajemen Absensi
                    </a>

                    @if(\App\Filament\Resources\IzinResource::class)
                    <a href="{{ \App\Filament\Resources\IzinResource::getUrl() }}"
                       class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Manajemen Izin
                    </a>
                    @endif

                    @if(\App\Filament\Resources\OvertimeAssignmentResource::class)
                    <a href="{{ \App\Filament\Resources\OvertimeAssignmentResource::getUrl() }}"
                       class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Penugasan Lembur
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Info (Hidden Filament branding) -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Informasi Sistem</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="font-medium text-gray-900">Sistem Absensi Sucofindo</p>
                    <p class="text-gray-500">Versi 1.0.0</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Laravel</p>
                    <p class="text-gray-500">{{ app()->version() }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900">PHP</p>
                    <p class="text-gray-500">{{ PHP_VERSION }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
