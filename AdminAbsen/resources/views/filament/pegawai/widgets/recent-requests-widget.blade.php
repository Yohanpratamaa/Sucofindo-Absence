<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Pengajuan Terbaru
        </x-slot>

        <div class="grid gap-4 md:grid-cols-2">
            <!-- Recent Overtime Requests -->
            <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <x-heroicon-o-clock class="inline w-5 h-5 mr-2 text-primary-500" />
                    Pengajuan Lembur Terbaru
                </h3>

                @if($recentOvertimes->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentOvertimes as $overtime)
                            <div class="p-3 border rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $overtime->overtime_id }}
                                            </span>
                                            <x-filament::badge
                                                :color="match($overtime->status) {
                                                    'Assigned' => 'warning',
                                                    'Accepted' => 'success',
                                                    'Rejected' => 'danger',
                                                    default => 'gray'
                                                }"
                                            >
                                                {{ match($overtime->status) {
                                                    'Assigned' => 'Menunggu',
                                                    'Accepted' => 'Disetujui',
                                                    'Rejected' => 'Ditolak',
                                                    default => $overtime->status
                                                } }}
                                            </x-filament::badge>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $overtime->assigned_at ? $overtime->assigned_at->format('d M Y H:i') : '-' }}
                                        </p>
                                        @if($overtime->keterangan)
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 truncate">
                                                {{ Str::limit($overtime->keterangan, 60) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Pegawai\Resources\MyOvertimeRequestResource::getUrl('index') }}"
                            size="sm"
                            color="primary"
                            outlined
                        >
                            Lihat Semua Lembur
                        </x-filament::button>
                    </div>
                @else
                    <div class="text-center py-6">
                        <x-heroicon-o-clock class="mx-auto h-12 w-12 text-gray-400" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Belum ada pengajuan lembur
                        </p>
                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Pegawai\Resources\MyOvertimeRequestResource::getUrl('create') }}"
                            size="sm"
                            color="success"
                            class="mt-2"
                        >
                            Ajukan Lembur
                        </x-filament::button>
                    </div>
                @endif
            </div>

            <!-- Recent Leave Requests -->
            <div class="space-y-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <x-heroicon-o-document-text class="inline w-5 h-5 mr-2 text-success-500" />
                    Pengajuan Izin Terbaru
                </h3>

                @if($recentIzins->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentIzins as $izin)
                            <div class="p-3 border rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ ucfirst($izin->jenis_izin) }}
                                            </span>
                                            <x-filament::badge
                                                :color="match(true) {
                                                    is_null($izin->approved_by) => 'warning',
                                                    !is_null($izin->approved_at) => 'success',
                                                    default => 'danger'
                                                }"
                                            >
                                                {{ match(true) {
                                                    is_null($izin->approved_by) => 'Menunggu',
                                                    !is_null($izin->approved_at) => 'Disetujui',
                                                    default => 'Ditolak'
                                                } }}
                                            </x-filament::badge>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $izin->tanggal_mulai->format('d M Y') }} - {{ $izin->tanggal_akhir->format('d M Y') }}
                                        </p>
                                        @if($izin->keterangan)
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 truncate">
                                                {{ Str::limit($izin->keterangan, 60) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Pegawai\Resources\MyIzinResource::getUrl('index') }}"
                            size="sm"
                            color="primary"
                            outlined
                        >
                            Lihat Semua Izin
                        </x-filament::button>
                    </div>
                @else
                    <div class="text-center py-6">
                        <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Belum ada pengajuan izin
                        </p>
                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Pegawai\Resources\MyIzinResource::getUrl('create') }}"
                            size="sm"
                            color="success"
                            class="mt-2"
                        >
                            Ajukan Izin
                        </x-filament::button>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
