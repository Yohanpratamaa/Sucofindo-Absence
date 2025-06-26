<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Export Cards -->
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    🚀 Export Cepat
                </x-slot>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Quick Export Tim Excel -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">📗 Rekap Tim</h3>
                                <p class="text-sm opacity-90">Format Excel</p>
                            </div>
                            <svg class="h-8 w-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs opacity-75">Gunakan tombol "Export Rekap Tim (Excel)" di atas</p>
                    </div>

                    <!-- Quick Export Tim PDF -->
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">📄 Rekap Tim</h3>
                                <p class="text-sm opacity-90">Format PDF</p>
                            </div>
                            <svg class="h-8 w-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs opacity-75">Gunakan tombol "Export Rekap Tim (PDF)" di atas</p>
                    </div>

                    <!-- Quick Export Individual Excel -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">📊 Detail</h3>
                                <p class="text-sm opacity-90">Excel Individu</p>
                            </div>
                            <svg class="h-8 w-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs opacity-75">Gunakan tombol "Export Detail Karyawan (Excel)" di atas</p>
                    </div>

                    <!-- Quick Export Individual PDF -->
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">📋 Detail</h3>
                                <p class="text-sm opacity-90">PDF Individu</p>
                            </div>
                            <svg class="h-8 w-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-xs opacity-75">Gunakan tombol "Export Detail Karyawan (PDF)" di atas</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Export Instructions -->
        <x-filament::section>
            <x-slot name="heading">
                📋 Panduan Export
            </x-slot>
            
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800 mb-2">📗 Export Rekap Tim</h4>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Rekap kehadiran semua anggota tim</li>
                        <li>• Total hadir, terlambat, lembur per karyawan</li>
                        <li>• Format Excel untuk analisis data</li>
                        <li>• Format PDF untuk laporan formal</li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">📊 Export Detail Individual</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Detail absensi harian per karyawan</li>
                        <li>• Jam masuk, keluar, durasi kerja</li>
                        <li>• Status kehadiran dan lokasi</li>
                        <li>• Pilih karyawan dan periode</li>
                    </ul>
                </div>
            </div>
        </x-filament::section>

        <!-- Export Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                📈 Tips Export
            </x-slot>
            
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">💡 Rekomendasi Periode</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• <strong>Harian:</strong> 1 hari untuk monitoring daily</li>
                        <li>• <strong>Mingguan:</strong> Senin-Jumat untuk evaluasi</li>
                        <li>• <strong>Bulanan:</strong> 1-30/31 untuk laporan resmi</li>
                        <li>• <strong>Custom:</strong> Sesuai kebutuhan analisis</li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="font-semibold text-purple-800 mb-2">🎯 Kapan Menggunakan</h4>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>• <strong>Excel:</strong> Untuk analisis mendalam & perhitungan</li>
                        <li>• <strong>PDF:</strong> Untuk dokumentasi & presentasi</li>
                        <li>• <strong>Rekap Tim:</strong> Overview performa tim</li>
                        <li>• <strong>Detail Individual:</strong> Evaluasi karyawan</li>
                    </ul>
                </div>
            </div>
        </x-filament::section>
    </div>

    <!-- Quick Stats -->
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">
                📊 Statistik Tim Bulan Ini
            </x-slot>
            
            @php
                $currentMonth = now()->format('Y-m');
                $teamMembers = \App\Models\Pegawai::where('role_user', 'employee')->where('status', 'active')->count();
                $totalAttendance = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->count();
                $lateAttendance = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->whereRaw("TIME(check_in) > '08:00:00'")->count();
                $workDays = now()->day; // Simplified calculation
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $teamMembers }}</div>
                    <div class="text-sm text-gray-600">Total Anggota Tim</div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $totalAttendance }}</div>
                    <div class="text-sm text-gray-600">Total Kehadiran</div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $lateAttendance }}</div>
                    <div class="text-sm text-gray-600">Total Terlambat</div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ round($totalAttendance / max($teamMembers * $workDays, 1) * 100, 1) }}%</div>
                    <div class="text-sm text-gray-600">Tingkat Kehadiran</div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
