<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Custom CSS Injection -->
        <style>
            .attendance-widget-enhanced {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }
            
            .dark .attendance-widget-enhanced {
                background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }
            
            .attendance-widget-enhanced:hover {
                transform: translateY(-5px);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            
            .attendance-header-enhanced {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                padding: 1.5rem;
            }
            
            .attendance-card-enhanced {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 16px;
                padding: 1.25rem;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .dark .attendance-card-enhanced {
                background: rgba(45, 55, 72, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .attendance-card-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
            }
            
            .attendance-status-enhanced {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 1.5rem;
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            
            .dark .attendance-status-enhanced {
                background: linear-gradient(135deg, rgba(45, 55, 72, 0.9), rgba(45, 55, 72, 0.7));
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .attendance-button-enhanced {
                background: linear-gradient(135deg, #4ade80, #22c55e);
                border: none;
                border-radius: 12px;
                padding: 0.875rem 1.75rem;
                color: white;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            }
            
            .attendance-button-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
                color: white;
            }
            
            .attendance-button-warning {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            }
            
            .attendance-button-warning:hover {
                box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            }
            
            .stat-card-enhanced {
                text-align: center;
                padding: 1rem;
            }
            
            .stat-number {
                width: 3rem;
                height: 3rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                font-weight: 700;
                color: white;
                margin: 0 auto 0.5rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
            
            .stat-number.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }
            
            .stat-number.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }
            
            .stat-number.red {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }
            
            @media (max-width: 768px) {
                .attendance-header-enhanced {
                    padding: 1rem;
                }
                
                .attendance-card-enhanced {
                    padding: 1rem;
                }
            }
        </style>

        <div class="attendance-widget-enhanced">
            <!-- Enhanced Header -->
            <div class="attendance-header-enhanced">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">
                                Absensi WFO Hari Ini
                            </h3>
                            <p class="text-white/80 text-sm">
                                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        @if($this->getCanCheckIn())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Belum Check In
                            </span>
                        @elseif($this->getCanCheckOut())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Belum Check Out
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Selesai
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                @php
                    $attendanceStatus = $this->getAttendanceStatus();
                    $todayAttendance = $this->getTodayAttendance();
                @endphp

                <!-- Status Overview -->
                @if($attendanceStatus['status'] !== 'Belum Absen')
                    <div class="attendance-status-enhanced">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Status Kehadiran Hari Ini</h4>
                                <div class="flex items-center gap-2">
                                    <x-filament::badge :color="$attendanceStatus['color']">
                                        {{ $attendanceStatus['status'] }}
                                    </x-filament::badge>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $attendanceStatus['type'] ?? 'WFO' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                    @if($attendanceStatus['check_in'])
                                        <p class="flex items-center justify-end">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Check In: {{ $attendanceStatus['check_in'] }}
                                        </p>
                                    @endif
                                    @if($attendanceStatus['check_out'])
                                        <p class="flex items-center justify-end">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Check Out: {{ $attendanceStatus['check_out'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Time Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Check In Card -->
                    <div class="attendance-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $attendanceStatus['check_in'] ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Check In</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $attendanceStatus['check_in'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Check Out Card -->
                    <div class="attendance-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $attendanceStatus['check_out'] ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Check Out</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $attendanceStatus['check_out'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Duration Card -->
                    <div class="attendance-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $todayAttendance && $todayAttendance->durasi_kerja !== '-' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Durasi Kerja</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $todayAttendance ? $todayAttendance->durasi_kerja : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center space-y-4">
                    @if($this->getCanCheckIn())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Anda belum melakukan check in hari ini. Gunakan kamera untuk selfie dan pastikan lokasi dalam radius kantor.
                        </p>
                        <a href="{{ \App\Filament\Pegawai\Pages\WfoAttendance::getUrl() }}" class="attendance-button-enhanced">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Check In WFO
                        </a>
                    @elseif($this->getCanCheckOut())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Anda sudah check in. Jangan lupa check out saat selesai bekerja.
                        </p>
                        <a href="{{ \App\Filament\Pegawai\Pages\WfoAttendance::getUrl() }}" class="attendance-button-enhanced attendance-button-warning">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Check Out WFO
                        </a>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Absensi hari ini sudah selesai. Terima kasih!
                        </p>
                        <div class="flex justify-center gap-4">
                            <x-filament::button
                                tag="a"
                                href="{{ \App\Filament\Pegawai\Resources\MyAttendanceResource::getUrl() }}"
                                color="gray"
                                outlined
                            >
                                Lihat Riwayat Absensi
                            </x-filament::button>
                        </div>
                    @endif
                </div>

                <!-- Monthly Statistics -->
                @php
                    $stats = $this->getAttendanceStats();
                @endphp
                <div class="border-t border-white/20 pt-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Statistik Bulan Ini
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="stat-card-enhanced">
                            <div class="stat-number green">{{ $stats['total_hadir'] }}</div>
                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Total Hadir</div>
                        </div>
                        <div class="stat-card-enhanced">
                            <div class="stat-number blue">{{ $stats['total_wfo'] }}</div>
                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">WFO</div>
                        </div>
                        <div class="stat-card-enhanced">
                            <div class="stat-number red">{{ $stats['total_terlambat'] }}</div>
                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Terlambat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
