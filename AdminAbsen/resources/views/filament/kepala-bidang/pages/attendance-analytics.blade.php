<x-filament-panels::page>
    @php
        // Get data from controller methods
        $stats = $this->getAttendanceStats();
        $topPerformers = $this->getTopPerformers();
        $dailyTrends = $this->getDailyTrends();

        // Calculate additional metrics for insights
        $totalEmployees = $stats['total_employees'];
        $attendanceRate = $stats['on_time_percentage'];
        $punctualityRate = $stats['on_time_percentage'];
        $excellentPerformers = collect($topPerformers)->where('attendance_rate', '>=', 95)->count();
        $needsAttention = collect($topPerformers)->where('attendance_rate', '<', 75)->count();
        $currentMonthAttendance = $stats['total_attendance'];
        $currentMonthLate = $stats['late'];
    @endphp

    <!-- Filters Form -->
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-funnel" class="h-5 w-5" />
                Filter Data
            </div>
        </x-slot>

        {{ $this->filtersForm }}
    </x-filament::section>

    <!-- Statistics Overview -->
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5" />
                Statistik Absensi
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pegawai -->
            <x-filament::section
                :heading="false"
                :description="false"
                class="text-center"
            >
                <div class="space-y-2">
                    <div class="flex items-center justify-center">
                        <x-filament::icon
                            icon="heroicon-o-users"
                            class="h-12 w-12 text-primary-500"
                        />
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['total_employees'] }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Total Pegawai
                    </div>
                </div>
            </x-filament::section>

            <!-- Total Absensi -->
            <x-filament::section
                :heading="false"
                :description="false"
                class="text-center"
            >
                <div class="space-y-2">
                    <div class="flex items-center justify-center">
                        <x-filament::icon
                            icon="heroicon-o-calendar"
                            class="h-12 w-12 text-success-500"
                        />
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['total_attendance'] }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Total Absensi
                    </div>
                </div>
            </x-filament::section>

            <!-- Tepat Waktu -->
            <x-filament::section
                :heading="false"
                :description="false"
                class="text-center"
            >
                <div class="space-y-2">
                    <div class="flex items-center justify-center">
                        <x-filament::icon
                            icon="heroicon-o-check-circle"
                            class="h-12 w-12 text-success-500"
                        />
                    </div>
                    <div class="text-3xl font-bold text-success-600 dark:text-success-400">
                        {{ $stats['on_time_percentage'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Tepat Waktu
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $stats['on_time'] }} dari {{ $stats['total_attendance'] }}
                    </div>
                </div>
            </x-filament::section>

            <!-- Terlambat -->
            <x-filament::section
                :heading="false"
                :description="false"
                class="text-center"
            >
                <div class="space-y-2">
                    <div class="flex items-center justify-center">
                        <x-filament::icon
                            icon="heroicon-o-clock"
                            class="h-12 w-12 text-warning-500"
                        />
                    </div>
                    <div class="text-3xl font-bold text-warning-600 dark:text-warning-400">
                        {{ $stats['late_percentage'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Terlambat
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $stats['late'] }} dari {{ $stats['total_attendance'] }}
                    </div>
                </div>
            </x-filament::section>
        </div>
    </x-filament::section>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Daily Trends -->
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5" />
                        Tren Harian Absensi
                    </div>
                </x-slot>

                @if(count($dailyTrends) > 0)
                    <div class="space-y-4">
                        @foreach($dailyTrends as $date => $trend)
                            @php
                                $total = $trend['total'];
                                $onTime = $trend['on_time'];
                                $late = $trend['late'];
                                $onTimePercentage = $total > 0 ? round(($onTime / $total) * 100) : 0;
                                $latePercentage = $total > 0 ? round(($late / $total) * 100) : 0;
                            @endphp

                            <x-filament::section
                                :heading="false"
                                :description="false"
                                class="py-3"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-16 text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($date)->format('d/m') }}
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <!-- On Time Progress -->
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-success-600 dark:text-success-400 font-medium">
                                                    {{ $onTime }} tepat waktu
                                                </span>
                                                <span class="text-gray-500">
                                                    {{ $onTimePercentage }}%
                                                </span>
                                            </div>
                                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div
                                                    class="bg-success-500 h-2 rounded-full transition-all duration-500"
                                                    style="width: {{ min($onTimePercentage, 100) }}%"
                                                ></div>
                                            </div>
                                        </div>

                                        <!-- Late Progress -->
                                        @if($latePercentage > 0)
                                            <div class="space-y-1">
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-warning-600 dark:text-warning-400 font-medium">
                                                        {{ $late }} terlambat
                                                    </span>
                                                    <span class="text-gray-500">
                                                        {{ $latePercentage }}%
                                                    </span>
                                                </div>
                                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                    <div
                                                        class="bg-warning-500 h-1.5 rounded-full transition-all duration-500"
                                                        style="width: {{ min($latePercentage, 100) }}%"
                                                    ></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </x-filament::section>
                        @endforeach

                        <!-- Legend -->
                        <x-filament::section
                            :heading="false"
                            :description="false"
                        >
                            <div class="flex justify-center gap-6 pt-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-success-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Tepat Waktu</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-warning-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Terlambat</span>
                                </div>
                            </div>
                        </x-filament::section>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-filament::icon
                            icon="heroicon-o-chart-bar"
                            class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4"
                        />
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data absensi untuk periode ini</p>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <!-- Top Performers Summary -->
        <div>
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-trophy" class="h-5 w-5" />
                        Ringkasan Performa
                    </div>
                </x-slot>

                @if(count($topPerformers) > 0)
                    @php $topPerformer = $topPerformers[0] ?? null; @endphp

                    <!-- Top Performer Highlight -->
                    @if($topPerformer)
                        <x-filament::section
                            :heading="false"
                            :description="false"
                            class="text-center mb-4"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-center gap-2">
                                    <x-filament::icon
                                        icon="heroicon-o-trophy"
                                        class="h-8 w-8 text-warning-500"
                                    />
                                    <span class="font-bold text-warning-600 dark:text-warning-400 uppercase tracking-wide text-sm">
                                        Top Performer
                                    </span>
                                </div>
                                <div class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $topPerformer['name'] }}
                                </div>
                                <x-filament::badge size="lg" color="success">
                                    {{ $topPerformer['attendance_rate'] }}% kehadiran
                                </x-filament::badge>
                            </div>
                        </x-filament::section>
                    @else
                        <div class="text-center py-6">
                            <x-filament::icon
                                icon="heroicon-o-trophy"
                                class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4"
                            />
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data performer</p>
                        </div>
                    @endif

                    <!-- Performance Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-filament::section
                            :heading="false"
                            :description="false"
                            class="text-center"
                        >
                            <div class="space-y-2">
                                <div class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide">
                                    Excellent
                                </div>
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $excellentPerformers }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Pegawai
                                </div>
                            </div>
                        </x-filament::section>

                        <x-filament::section
                            :heading="false"
                            :description="false"
                            class="text-center"
                        >
                            <div class="space-y-2">
                                <div class="text-xs font-semibold text-warning-600 dark:text-warning-400 uppercase tracking-wide">
                                    Perlu Perhatian
                                </div>
                                <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                    {{ $needsAttention }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Pegawai
                                </div>
                            </div>
                        </x-filament::section>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-filament::icon
                            icon="heroicon-o-users"
                            class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4"
                        />
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data pegawai</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>

    <!-- Top Performers Ranking -->
    @if(count($topPerformers) > 0)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-star" class="h-5 w-5" />
                    Ranking Top Performers
                </div>
            </x-slot>

            <div class="space-y-3">
                @foreach($topPerformers as $index => $performer)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                    >
                        <div class="flex items-center gap-4 py-2">
                            <!-- Rank Badge -->
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <div class="w-10 h-10 bg-warning-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                        1st
                                    </div>
                                @elseif($index === 1)
                                    <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                        2nd
                                    </div>
                                @elseif($index === 2)
                                    <div class="w-10 h-10 bg-orange-400 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                        3rd
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                        {{ $index + 1 }}
                                    </div>
                                @endif
                            </div>

                            <!-- Employee Info -->
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $performer['name'] }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $performer['total_attendance'] }} absensi • {{ $performer['on_time_attendance'] }} tepat waktu
                                </div>
                            </div>

                            <!-- Performance Badge -->
                            <div class="flex-shrink-0">
                                @if($performer['attendance_rate'] >= 95)
                                    <x-filament::badge color="success" size="lg">
                                        {{ $performer['attendance_rate'] }}% EXCELLENT
                                    </x-filament::badge>
                                @elseif($performer['attendance_rate'] >= 85)
                                    <x-filament::badge color="primary" size="lg">
                                        {{ $performer['attendance_rate'] }}% GOOD
                                    </x-filament::badge>
                                @elseif($performer['attendance_rate'] >= 75)
                                    <x-filament::badge color="warning" size="lg">
                                        {{ $performer['attendance_rate'] }}% AVERAGE
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="danger" size="lg">
                                        {{ $performer['attendance_rate'] }}% ATTENTION
                                    </x-filament::badge>
                                @endif
                            </div>
                        </div>
                    </x-filament::section>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <!-- Insights & Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Performance Insights -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-light-bulb" class="h-5 w-5 text-primary-500" />
                    <span class="text-primary-600 dark:text-primary-400">Insights Performa</span>
                </div>
            </x-slot>

            <div class="space-y-3">
                @if($attendanceRate >= 90)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 text-success-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Tim menunjukkan performa kehadiran yang sangat baik ({{ $attendanceRate }}%)
                            </span>
                        </div>
                    </x-filament::section>
                @elseif($attendanceRate >= 75)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-arrow-trending-up" class="h-5 w-5 text-warning-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Performa kehadiran cukup baik ({{ $attendanceRate }}%), ada ruang untuk peningkatan
                            </span>
                        </div>
                    </x-filament::section>
                @else
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-danger-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Performa kehadiran perlu ditingkatkan ({{ $attendanceRate }}%)
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                @if($excellentPerformers > $totalEmployees * 0.7)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-star" class="h-5 w-5 text-warning-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Mayoritas tim ({{ $excellentPerformers }}/{{ $totalEmployees }}) memiliki performa excellent
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                @if($needsAttention > 0)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-users" class="h-5 w-5 text-primary-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $needsAttention }} karyawan memerlukan perhatian khusus
                            </span>
                        </div>
                    </x-filament::section>
                @endif
            </div>
        </x-filament::section>

        <!-- Punctuality Analysis -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 text-warning-500" />
                    <span class="text-warning-600 dark:text-warning-400">Analisis Ketepatan Waktu</span>
                </div>
            </x-slot>

            <div class="space-y-3">
                @if($punctualityRate >= 90)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 text-success-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Ketepatan waktu sangat baik ({{ $punctualityRate }}%)
                            </span>
                        </div>
                    </x-filament::section>
                @elseif($punctualityRate >= 75)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-warning-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Ketepatan waktu cukup ({{ $punctualityRate }}%)
                            </span>
                        </div>
                    </x-filament::section>
                @else
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-x-circle" class="h-5 w-5 text-danger-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Ketepatan waktu perlu diperbaiki ({{ $punctualityRate }}%)
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                <x-filament::section
                    :heading="false"
                    :description="false"
                    class="py-3"
                >
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-info-500 mt-0.5" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $currentMonthLate }} dari {{ $currentMonthAttendance }} kehadiran terlambat
                        </span>
                    </div>
                </x-filament::section>
            </div>
        </x-filament::section>

        <!-- Action Recommendations -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-clipboard-document" class="h-5 w-5 text-success-500" />
                    <span class="text-success-600 dark:text-success-400">Rekomendasi Tindakan</span>
                </div>
            </x-slot>

            <div class="space-y-3">
                @if($needsAttention > 0)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-users" class="h-5 w-5 text-primary-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Lakukan one-on-one meeting dengan {{ $needsAttention }} karyawan yang perlu perhatian
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                @if($currentMonthLate > $totalEmployees * 0.3)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-megaphone" class="h-5 w-5 text-warning-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Adakan briefing tentang pentingnya ketepatan waktu
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                @if($excellentPerformers > 0)
                    <x-filament::section
                        :heading="false"
                        :description="false"
                        class="py-3"
                    >
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-trophy" class="h-5 w-5 text-warning-500 mt-0.5" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Berikan apresiasi kepada {{ $excellentPerformers }} top performer
                            </span>
                        </div>
                    </x-filament::section>
                @endif

                <x-filament::section
                    :heading="false"
                    :description="false"
                    class="py-3"
                >
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-info-500 mt-0.5" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Gunakan data ini untuk evaluasi kinerja bulanan
                        </span>
                    </div>
                </x-filament::section>

                <x-filament::section
                    :heading="false"
                    :description="false"
                    class="py-3"
                >
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-document-arrow-down" class="h-5 w-5 text-info-500 mt-0.5" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Export detail per karyawan untuk review mendalam
                        </span>
                    </div>
                </x-filament::section>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
