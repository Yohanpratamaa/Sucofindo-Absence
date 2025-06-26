<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Links to Export -->
        <div class="lg:col-span-3">
            <x-filament::section>
                <x-slot name="heading">
                    🔗 Akses Cepat Export
                </x-slot>
                
                <div class="flex flex-wrap gap-3">
                    <a href="/kepala-bidang/export-center" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pusat Export
                    </a>
                    
                    <a href="/kepala-bidang/attendance-reports" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414A1 1 0 0116 10v6a1 1 0 01-1 1z" />
                        </svg>
                        Data Laporan
                    </a>
                </div>
            </x-filament::section>
        </div>

        <!-- Attendance Trends -->
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    📈 Trend Kehadiran
                </x-slot>
                
                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <div class="text-gray-500 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Chart Kehadiran</h3>
                    <p class="text-gray-500">Fitur chart dan analytics akan dikembangkan di versi selanjutnya</p>
                    <p class="text-sm text-gray-400 mt-2">Untuk saat ini gunakan export Excel untuk analisis manual</p>
                </div>
            </x-filament::section>
        </div>

        <!-- Performance Insights -->
        <div>
            <x-filament::section>
                <x-slot name="heading">
                    🎯 Insights
                </x-slot>
                
                @php
                    $currentMonth = now()->format('Y-m');
                    $teamMembers = \App\Models\Pegawai::where('role_user', 'employee')->where('status', 'active')->count();
                    $totalAttendance = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->count();
                    $lateCount = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->whereRaw("TIME(check_in) > '08:00:00'")->count();
                    
                    $attendanceRate = $teamMembers > 0 ? round(($totalAttendance / ($teamMembers * now()->day)) * 100, 1) : 0;
                    $punctualityRate = $totalAttendance > 0 ? round((($totalAttendance - $lateCount) / $totalAttendance) * 100, 1) : 0;
                @endphp
                
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-lg font-semibold text-green-800">{{ $attendanceRate }}%</div>
                                <div class="text-sm text-green-600">Tingkat Kehadiran</div>
                            </div>
                            <div class="text-green-400">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-lg font-semibold text-blue-800">{{ $punctualityRate }}%</div>
                                <div class="text-sm text-blue-600">Tingkat Ketepatan Waktu</div>
                            </div>
                            <div class="text-blue-400">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-lg font-semibold text-purple-800">{{ $teamMembers }}</div>
                                <div class="text-sm text-purple-600">Anggota Tim Aktif</div>
                            </div>
                            <div class="text-purple-400">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>

    <!-- Export Recommendations -->
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">
                📋 Rekomendasi Export
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6">
                    <h3 class="font-semibold text-blue-800 mb-3">📊 Export untuk Analisis</h3>
                    <ul class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Export rekap tim bulanan untuk evaluasi performa
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Export detail karyawan yang sering terlambat
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Gunakan Excel untuk membuat pivot table
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6">
                    <h3 class="font-semibold text-green-800 mb-3">📄 Export untuk Laporan</h3>
                    <ul class="space-y-2 text-sm text-green-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            Export PDF untuk laporan ke atasan
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            Export detail individual untuk evaluasi karyawan
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            Format siap print untuk dokumentasi
                        </li>
                    </ul>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
