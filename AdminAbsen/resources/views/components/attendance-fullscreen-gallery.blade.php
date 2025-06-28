@php
use Carbon\Carbon;
@endphp

@if($record)
<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <h4 class="font-semibold text-purple-800">👤 Pegawai</h4>
                <p class="text-sm text-gray-700">{{ $record->user->nama ?? '-' }}</p>
                <p class="text-xs text-gray-600">NPP: {{ $record->user->npp ?? '-' }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-purple-800">📅 Tanggal</h4>
                <p class="text-sm text-gray-700">{{ $record->created_at ? $record->created_at->format('d F Y') : '-' }}</p>
                <p class="text-xs text-gray-600">{{ $record->created_at ? $record->created_at->format('l') : '-' }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-purple-800">🏢 Tipe Absensi</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ ($record->attendance_type ?? '') === 'WFO' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $record->attendance_type ?? '-' }}
                </span>
            </div>
            <div>
                <h4 class="font-semibold text-purple-800">📊 Status</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ ($record->status_kehadiran ?? '') === 'Tepat Waktu' ? 'bg-green-100 text-green-800' :
                       (($record->status_kehadiran ?? '') === 'Terlambat' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ $record->status_kehadiran ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Photo Gallery -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Check In Photo -->
        @if($record->picture_absen_masuk)
        <div class="space-y-4">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-green-800 flex items-center">
                        🌅 Foto Check In
                    </h3>
                    @if($record->check_in)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ \Carbon\Carbon::parse($record->check_in)->format('H:i:s') }}
                        </span>
                    @endif
                </div>

                <div class="relative group">
                    <img
                        src="{{ asset('storage/' . $record->picture_absen_masuk) }}"
                        alt="Foto Check In"
                        class="w-full h-80 object-cover rounded-lg shadow-lg cursor-pointer transition-transform hover:scale-105"
                        onclick="openImageModal('{{ asset('storage/' . $record->picture_absen_masuk) }}', 'Foto Check In - {{ $record->user->nama }}', '{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i:s, d F Y') : '' }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                        <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                @if($record->latitude_absen_masuk && $record->longitude_absen_masuk)
                <div class="mt-3 p-2 bg-white rounded border">
                    <p class="text-xs text-gray-600">
                        📍 {{ number_format($record->latitude_absen_masuk, 6) }}, {{ number_format($record->longitude_absen_masuk, 6) }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Absen Siang Photo (Only for Dinas Luar) -->
        @if($record->picture_absen_siang && $record->attendance_type === 'Dinas Luar')
        <div class="space-y-4">
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-yellow-800 flex items-center">
                        ☀️ Foto Absen Siang
                    </h3>
                    @if($record->absen_siang)
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s') }}
                        </span>
                    @endif
                </div>

                <div class="relative group">
                    <img
                        src="{{ asset('storage/' . $record->picture_absen_siang) }}"
                        alt="Foto Absen Siang"
                        class="w-full h-80 object-cover rounded-lg shadow-lg cursor-pointer transition-transform hover:scale-105"
                        onclick="openImageModal('{{ asset('storage/' . $record->picture_absen_siang) }}', 'Foto Absen Siang - {{ $record->user->nama }}', '{{ $record->absen_siang ? \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s, d F Y') : '' }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                        <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                @if($record->latitude_absen_siang && $record->longitude_absen_siang)
                <div class="mt-3 p-2 bg-white rounded border">
                    <p class="text-xs text-gray-600">
                        📍 {{ number_format($record->latitude_absen_siang, 6) }}, {{ number_format($record->longitude_absen_siang, 6) }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Check Out Photo -->
        @if($record->picture_absen_pulang)
        <div class="space-y-4">
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-red-800 flex items-center">
                        🌇 Foto Check Out
                    </h3>
                    @if($record->check_out)
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ \Carbon\Carbon::parse($record->check_out)->format('H:i:s') }}
                        </span>
                    @endif
                </div>

                <div class="relative group">
                    <img
                        src="{{ asset('storage/' . $record->picture_absen_pulang) }}"
                        alt="Foto Check Out"
                        class="w-full h-80 object-cover rounded-lg shadow-lg cursor-pointer transition-transform hover:scale-105"
                        onclick="openImageModal('{{ asset('storage/' . $record->picture_absen_pulang) }}', 'Foto Check Out - {{ $record->user->nama }}', '{{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('H:i:s, d F Y') : '' }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                        <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                @if($record->latitude_absen_pulang && $record->longitude_absen_pulang)
                <div class="mt-3 p-2 bg-white rounded border">
                    <p class="text-xs text-gray-600">
                        📍 {{ number_format($record->latitude_absen_pulang, 6) }}, {{ number_format($record->longitude_absen_pulang, 6) }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Summary Info -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-3">📊 Ringkasan Absensi</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Check In:</span>
                <p class="font-medium">{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i:s') : '-' }}</p>
            </div>
            @if($record->attendance_type === 'Dinas Luar')
            <div>
                <span class="text-gray-600">Absen Siang:</span>
                <p class="font-medium">{{ $record->absen_siang ? \Carbon\Carbon::parse($record->absen_siang)->format('H:i:s') : '-' }}</p>
            </div>
            @endif
            <div>
                <span class="text-gray-600">Check Out:</span>
                <p class="font-medium">{{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('H:i:s') : '-' }}</p>
            </div>
            <div>
                <span class="text-gray-600">Durasi:</span>
                <p class="font-medium">{{ $record->durasi_kerja ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- No Photos Message -->
    @if(!$record->picture_absen_masuk && !$record->picture_absen_siang && !$record->picture_absen_pulang)
    <div class="text-center py-12">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Foto</h3>
        <p class="text-gray-600">Tidak ada foto absensi yang tersedia untuk ditampilkan.</p>
    </div>
    @endif
</div>

<!-- Image Modal for Fullscreen View -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-full max-h-full">
        <button
            onclick="closeImageModal()"
            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition-all"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        <div class="absolute bottom-4 left-4 right-4 text-center">
            <div class="bg-black bg-opacity-70 text-white p-3 rounded-lg">
                <h3 id="modalTitle" class="font-semibold"></h3>
                <p id="modalTime" class="text-sm opacity-90"></p>
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc, title, time) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalTime = document.getElementById('modalTime');

    modalImage.src = imageSrc;
    modalTitle.textContent = title;
    modalTime.textContent = time;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<style>
.modal-backdrop {
    backdrop-filter: blur(8px);
}
</style>
