<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Custom CSS Injection for Dinas Luar -->
        <style>
            .dinas-luar-widget-enhanced {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }
            
            .dark .dinas-luar-widget-enhanced {
                background: linear-gradient(135deg, #744c57 0%, #5a3a4b 100%);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }
            
            .dinas-luar-widget-enhanced:hover {
                transform: translateY(-5px);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            
            .dinas-luar-header-enhanced {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                padding: 1.5rem;
            }
            
            .dinas-luar-card-enhanced {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 16px;
                padding: 1.25rem;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .dark .dinas-luar-card-enhanced {
                background: rgba(45, 55, 72, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .dinas-luar-card-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
            }
            
            .dinas-luar-progress {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                height: 10px;
                overflow: hidden;
                margin: 1rem 0;
            }
            
            .dinas-luar-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #10b981, #34d399);
                border-radius: 10px;
                transition: width 1s ease-in-out;
                position: relative;
            }
            
            .dinas-luar-progress-bar::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                animation: progress-shimmer 2s infinite;
            }
            
            @keyframes progress-shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            
            .dinas-luar-step {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .dinas-luar-step-icon {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 0.5rem;
                transition: all 0.3s ease;
                border: 2px solid;
            }
            
            .dinas-luar-step-icon.completed {
                background: linear-gradient(135deg, #10b981, #059669);
                border-color: #10b981;
                color: white;
            }
            
            .dinas-luar-step-icon.pending {
                background: rgba(156, 163, 175, 0.2);
                border-color: #9ca3af;
                color: #9ca3af;
            }
            
            .dinas-luar-button-enhanced {
                background: linear-gradient(135deg, #f59e0b, #d97706);
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
                box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            }
            
            .dinas-luar-button-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
                color: white;
            }
            
            .dinas-luar-button-success {
                background: linear-gradient(135deg, #10b981, #059669);
                box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            }
            
            .dinas-luar-button-success:hover {
                box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            }
            
            .dinas-luar-button-danger {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            }
            
            .dinas-luar-button-danger:hover {
                box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
            }
            
            @media (max-width: 768px) {
                .dinas-luar-header-enhanced {
                    padding: 1rem;
                }
                
                .dinas-luar-card-enhanced {
                    padding: 1rem;
                }
            }
        </style>

        <div class="dinas-luar-widget-enhanced">
            <!-- Enhanced Header -->
            <div class="dinas-luar-header-enhanced">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">
                                Absensi Dinas Luar Hari Ini
                            </h3>
                            <p class="text-white/80 text-sm">
                                {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        @php
                            $progress = $this->getAttendanceProgress();
                        @endphp
                        @if($progress['percentage'] == 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Belum Absen
                            </span>
                        @elseif($progress['percentage'] == 100)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Selesai ({{ $progress['percentage'] }}%)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Progress: {{ $progress['percentage'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                @php
                    $attendanceStatus = $this->getAttendanceStatus();
                @endphp

                <!-- Status Overview -->
                @if($attendanceStatus['status'] !== 'Belum Absen')
                    <div class="dinas-luar-card-enhanced">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Status Kehadiran Hari Ini</h4>
                                <div class="flex items-center gap-2">
                                    <x-filament::badge :color="$attendanceStatus['color']">
                                        {{ $attendanceStatus['status'] }}
                                    </x-filament::badge>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $attendanceStatus['type'] ?? 'Dinas Luar' }}
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
                                            Pagi: {{ $attendanceStatus['check_in'] }}
                                        </p>
                                    @endif
                                    @if($attendanceStatus['absen_siang'])
                                        <p class="flex items-center justify-end">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Siang: {{ $attendanceStatus['absen_siang'] }}
                                        </p>
                                    @endif
                                    @if($attendanceStatus['check_out'])
                                        <p class="flex items-center justify-end">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Sore: {{ $attendanceStatus['check_out'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Progress Bar untuk Dinas Luar -->
                @if($progress['percentage'] > 0)
                    <div class="dinas-luar-card-enhanced">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span class="font-medium">Progress Absensi Dinas Luar</span>
                            <span class="font-bold">{{ $progress['percentage'] }}%</span>
                        </div>
                        <div class="dinas-luar-progress">
                            <div class="dinas-luar-progress-bar" style="width: {{ $progress['percentage'] }}%"></div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div class="dinas-luar-step">
                                <div class="dinas-luar-step-icon {{ $progress['pagi'] ? 'completed' : 'pending' }}">
                                    @if($progress['pagi'])
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">1</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $progress['pagi'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Pagi</span>
                            </div>
                            <div class="dinas-luar-step">
                                <div class="dinas-luar-step-icon {{ $progress['siang'] ? 'completed' : 'pending' }}">
                                    @if($progress['siang'])
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">2</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $progress['siang'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Siang</span>
                            </div>
                            <div class="dinas-luar-step">
                                <div class="dinas-luar-step-icon {{ $progress['sore'] ? 'completed' : 'pending' }}">
                                    @if($progress['sore'])
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">3</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $progress['sore'] ? 'text-green-600 font-medium' : 'text-gray-500' }}">Sore</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Time Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Absen Pagi Status -->
                    <div class="dinas-luar-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $attendanceStatus['check_in'] ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Pagi</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $attendanceStatus['check_in'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Absen Siang Status -->
                    <div class="dinas-luar-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $attendanceStatus['absen_siang'] ? 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Siang</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $attendanceStatus['absen_siang'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Absen Sore Status -->
                    <div class="dinas-luar-card-enhanced">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg {{ $attendanceStatus['check_out'] ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500' }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Absen Sore</p>
                                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $attendanceStatus['check_out'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center space-y-4">
                    @if($this->getCanCheckIn())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Saatnya absen pagi untuk dinas luar. Pastikan lokasi GPS aktif dan ambil foto selfie.
                        </p>
                        <a href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}" class="dinas-luar-button-enhanced dinas-luar-button-success">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Absen Pagi
                        </a>
                    @elseif($this->getCanCheckInSiang())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Waktu absen siang telah tiba. Lakukan absensi siang sekarang.
                        </p>
                        <a href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}" class="dinas-luar-button-enhanced">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Absen Siang
                        </a>
                    @elseif($this->getCanCheckOut())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Saatnya absen sore untuk mengakhiri dinas luar hari ini.
                        </p>
                        <a href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}" class="dinas-luar-button-enhanced dinas-luar-button-danger">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            Absen Sore
                        </a>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            @if($progress['percentage'] == 100)
                                Semua absensi dinas luar hari ini telah selesai. Terima kasih!
                            @else
                                Belum waktunya untuk absensi atau semua absensi sudah selesai.
                            @endif
                        </p>
                        <div class="flex justify-center gap-4">
                            <a href="{{ \App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl() }}" class="dinas-luar-button-enhanced">
                                Lihat Detail
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
