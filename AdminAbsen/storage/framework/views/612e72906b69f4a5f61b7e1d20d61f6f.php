<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <!-- Status Absensi Dinas Luar Hari Ini -->
        <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('heading', null, []); ?> 
                Status Absensi Dinas Luar Hari Ini
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('description', null, []); ?> 
                <?php echo e(Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y')); ?>

             <?php $__env->endSlot(); ?>

            <div class="space-y-4">
                <!--[if BLOCK]><![endif]--><?php if($todayAttendance): ?>
                    <?php
                        $progress = $this->getAttendanceProgress();
                    ?>

                    <!-- Progress Badge -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">Progress Absensi</span>
                        <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => $progress['percentage'] == 100 ? 'success' : 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($progress['percentage'] == 100 ? 'success' : 'warning')]); ?>
                            <?php echo e($progress['percentage']); ?>% Selesai
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                    </div>

                    <!-- Waktu Absensi -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-xl font-semibold text-primary-600">
                                <?php echo e($todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '-'); ?>

                            </div>
                            <div class="text-sm text-gray-500">Absen Pagi</div>
                            <!--[if BLOCK]><![endif]--><?php if($progress['pagi']): ?>
                                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => 'success','size' => 'sm','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success','size' => 'sm','class' => 'mt-1']); ?>✓ <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-semibold text-primary-600">
                                <?php echo e($todayAttendance->absen_siang ? $todayAttendance->absen_siang->format('H:i') : '-'); ?>

                            </div>
                            <div class="text-sm text-gray-500">Absen Siang</div>
                            <!--[if BLOCK]><![endif]--><?php if($progress['siang']): ?>
                                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => 'warning','size' => 'sm','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'warning','size' => 'sm','class' => 'mt-1']); ?>✓ <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-semibold text-primary-600">
                                <?php echo e($todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '-'); ?>

                            </div>
                            <div class="text-sm text-gray-500">Absen Sore</div>
                            <!--[if BLOCK]><![endif]--><?php if($progress['sore']): ?>
                                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => 'info','size' => 'sm','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'info','size' => 'sm','class' => 'mt-1']); ?>✓ <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="text-center">
                            <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => match($todayAttendance->status_kehadiran ?? '') {
                                    'Tepat Waktu' => 'success',
                                    'Terlambat' => 'warning',
                                    'Tidak Hadir' => 'danger',
                                    default => 'gray'
                                }]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(match($todayAttendance->status_kehadiran ?? '') {
                                    'Tepat Waktu' => 'success',
                                    'Terlambat' => 'warning',
                                    'Tidak Hadir' => 'danger',
                                    default => 'gray'
                                })]); ?>
                                <?php echo e($todayAttendance->status_kehadiran ?? 'Belum Diketahui'); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                            <div class="text-sm text-gray-500">Status</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                                style="width: <?php echo e($progress['percentage']); ?>%"
                            ></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span class="<?php echo e($progress['pagi'] ? 'text-success-600 font-medium' : ''); ?>">Pagi</span>
                            <span class="<?php echo e($progress['siang'] ? 'text-warning-600 font-medium' : ''); ?>">Siang</span>
                            <span class="<?php echo e($progress['sore'] ? 'text-info-600 font-medium' : ''); ?>">Sore</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg bg-info-50 border border-info-200 p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-info-800">Belum Ada Absensi</h4>
                                <p class="text-sm text-info-700">Anda belum melakukan absensi dinas luar hari ini. Silakan lakukan absensi pagi terlebih dahulu.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

        <!-- Status Lokasi -->
        <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['id' => 'location-status','style' => 'display: none;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'location-status','style' => 'display: none;']); ?>
             <?php $__env->slot('heading', null, []); ?> 
                Status Lokasi Saat Ini
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('description', null, []); ?> 
                Lokasi Anda akan dicatat secara otomatis untuk absensi dinas luar
             <?php $__env->endSlot(); ?>

            <div id="location-info">
                <!-- Location info will be populated by JavaScript -->
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

        <!-- Absensi Dinas Luar -->
        <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php
                $currentAction = null;
                $actionTitle = 'Tidak Ada Aksi Tersedia';

                if ($canCheckInPagi) {
                    $currentAction = 'pagi';
                    $actionTitle = 'Absensi Pagi - Dinas Luar';
                } elseif ($canCheckInSiang) {
                    $currentAction = 'siang';
                    $actionTitle = 'Absensi Siang - Dinas Luar';
                } elseif ($canCheckOut) {
                    $currentAction = 'sore';
                    $actionTitle = 'Absensi Sore - Dinas Luar';
                }
            ?>

             <?php $__env->slot('heading', null, []); ?> 
                <?php echo e($actionTitle); ?>

             <?php $__env->endSlot(); ?>

             <?php $__env->slot('description', null, []); ?> 
                <!--[if BLOCK]><![endif]--><?php if($currentAction === 'pagi'): ?>
                    Ambil foto selfie untuk memulai hari kerja dinas luar
                <?php elseif($currentAction === 'siang'): ?>
                    Ambil foto selfie untuk absensi siang
                <?php elseif($currentAction === 'sore'): ?>
                    Ambil foto selfie untuk mengakhiri hari kerja dinas luar
                <?php else: ?>
                    Tidak ada aksi absensi yang tersedia saat ini
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <?php $__env->endSlot(); ?>

            <!--[if BLOCK]><![endif]--><?php if($currentAction): ?>
                <!-- Information Alert -->
                <div class="rounded-lg bg-info-50 border border-info-200 p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-info-800">Informasi Absensi <?php echo e(ucfirst($currentAction)); ?></h4>
                            <p class="text-sm text-info-700">
                                <!--[if BLOCK]><![endif]--><?php if($currentAction === 'pagi'): ?>
                                    Lakukan absensi pagi untuk memulai hari kerja dinas luar. Lokasi Anda akan dicatat secara otomatis.
                                <?php elseif($currentAction === 'siang'): ?>
                                    Waktu absensi siang. Pastikan Anda berada di lokasi tugas yang tepat.
                                <?php else: ?>
                                    Absensi sore untuk mengakhiri hari kerja dinas luar. Terima kasih atas kerja keras Anda.
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Camera Status Alert -->
                <div id="camera-status" class="rounded-lg bg-info-50 border border-info-200 p-4" style="display: none;">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-info-800">Status Kamera</h4>
                            <span id="camera-status-text" class="text-info-700">Mengakses kamera...</span>
                        </div>
                    </div>
                </div>

                <!-- Camera Preview Area -->
                <div class="space-y-4">
                    <video
                        id="camera"
                        class="w-full h-80 object-cover rounded-lg border"
                        autoplay
                        playsinline
                        muted
                        style="display: none;"
                    ></video>

                    <div id="camera-placeholder" class="flex flex-col items-center justify-center h-80 border-2 border-dashed border-gray-300 rounded-lg">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-camera','class' => 'w-16 h-16 text-gray-400 mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-camera','class' => 'w-16 h-16 text-gray-400 mb-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Kamera Belum Aktif</h4>
                        <p class="text-sm text-gray-600 text-center mb-2">
                            Klik tombol "Aktifkan Kamera" untuk memulai proses absensi <?php echo e($currentAction); ?>

                        </p>
                        <p class="text-xs text-gray-500 flex items-center">
                            <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-information-circle','class' => 'w-4 h-4 mr-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-information-circle','class' => 'w-4 h-4 mr-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                            Pastikan izin kamera sudah diaktifkan
                        </p>
                    </div>

                    <div id="camera-overlay" class="relative" style="display: none;">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-black bg-opacity-60 text-white px-3 py-2 rounded text-sm">
                                Posisikan wajah dalam frame
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Camera Controls -->
                <div class="flex flex-wrap gap-3">
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'start-camera-btn','type' => 'button','color' => 'primary','icon' => 'heroicon-m-camera']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'start-camera-btn','type' => 'button','color' => 'primary','icon' => 'heroicon-m-camera']); ?>
                        Aktifkan Kamera
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'stop-camera-btn','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-stop','style' => 'display: none;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'stop-camera-btn','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-stop','style' => 'display: none;']); ?>
                        Matikan Kamera
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                </div>

                <!-- Photo Preview -->
                <div id="photo-preview" style="display: none;">
                    <div class="rounded-lg bg-success-50 border border-success-200 p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-success-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <h4 class="font-medium text-success-800">Foto Berhasil Diambil</h4>
                                <p class="text-sm text-success-700">Preview foto yang akan digunakan untuk absensi <?php echo e($currentAction); ?>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-4">
                        <img
                            id="captured-photo"
                            class="w-full h-80 object-cover rounded-lg border"
                            alt="Preview foto absensi"
                        >
                        <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => 'success','class' => 'absolute top-2 right-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success','class' => 'absolute top-2 right-2']); ?>
                            ✓ Foto Siap
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'retake-photo','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-arrow-path']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'retake-photo','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-arrow-path']); ?>
                            Ambil Ulang
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'capture-btn','type' => 'button','color' => 'info','icon' => 'heroicon-m-camera','style' => 'display: none;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'capture-btn','type' => 'button','color' => 'info','icon' => 'heroicon-m-camera','style' => 'display: none;']); ?>
                        Ambil Foto
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'test-photo-btn','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-beaker','style' => 'display: none;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'test-photo-btn','type' => 'button','outlined' => true,'color' => 'gray','icon' => 'heroicon-m-beaker','style' => 'display: none;']); ?>
                        Test Foto
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['id' => 'submit-btn','type' => 'button','color' => 'success','icon' => 'heroicon-m-check-circle','style' => 'display: none;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'submit-btn','type' => 'button','color' => 'success','icon' => 'heroicon-m-check-circle','style' => 'display: none;']); ?>
                        Absen <?php echo e(ucfirst($currentAction)); ?> Sekarang
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                </div>
            <?php else: ?>
                <!-- No Action Available -->
                <div class="text-center py-8">
                    <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-clock','class' => 'w-16 h-16 text-gray-400 mx-auto mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-clock','class' => 'w-16 h-16 text-gray-400 mx-auto mb-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>

                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tidak Ada Aksi Tersedia</h3>

                    <!--[if BLOCK]><![endif]--><?php if($todayAttendance && $todayAttendance->check_out): ?>
                        <div class="rounded-lg bg-success-50 border border-success-200 p-4 max-w-md mx-auto mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-success-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-success-800">Absensi Dinas Luar Selesai</h4>
                                    <p class="text-sm text-success-700">Anda telah menyelesaikan semua absensi dinas luar untuk hari ini.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="rounded-lg bg-info-50 border border-info-200 p-4 max-w-md mx-auto mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-info-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-info-800">Menunggu Waktu Absensi</h4>
                                    <p class="text-sm text-info-700">Silakan tunggu hingga waktu yang tepat untuk melakukan absensi.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <p class="text-gray-600 mb-4">
                        Lihat riwayat absensi dinas luar Anda atau hubungi administrator jika ada pertanyaan.
                    </p>

                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['tag' => 'a','href' => url('/pegawai/my-dinas-luar-attendances'),'outlined' => true,'color' => 'primary','icon' => 'heroicon-m-clock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/pegawai/my-dinas-luar-attendances')),'outlined' => true,'color' => 'primary','icon' => 'heroicon-m-clock']); ?>
                        Lihat Riwayat Dinas Luar
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let stream;
    let currentLocation = null;
    let capturedPhoto = null;

    const currentAction = <?php echo json_encode($currentAction, 15, 512) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();

        <!--[if BLOCK]><![endif]--><?php if($currentAction): ?>
            // Auto-start location detection
            getCurrentLocation();

            // Hide camera status initially
            hideCameraStatus();
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    });

    function hideCameraStatus() {
        const cameraStatus = document.getElementById('camera-status');
        if (cameraStatus) {
            cameraStatus.style.display = 'none';
        }
    }

    function showCameraStatus(message, isLoading = false) {
        const cameraStatus = document.getElementById('camera-status');
        const statusText = document.getElementById('camera-status-text');

        if (cameraStatus) {
            cameraStatus.style.display = 'block';
        }
        if (statusText) {
            statusText.textContent = message;
        }
    }

    function setupEventListeners() {
        const startCameraBtn = document.getElementById('start-camera-btn');
        const stopCameraBtn = document.getElementById('stop-camera-btn');
        const captureBtn = document.getElementById('capture-btn');
        const retakeBtn = document.getElementById('retake-photo');
        const submitBtn = document.getElementById('submit-btn');
        const testPhotoBtn = document.getElementById('test-photo-btn');

        if (startCameraBtn) {
            startCameraBtn.addEventListener('click', startCamera);
        }

        if (stopCameraBtn) {
            stopCameraBtn.addEventListener('click', stopCamera);
        }

        if (captureBtn) {
            captureBtn.addEventListener('click', capturePhoto);
        }

        if (retakeBtn) {
            retakeBtn.addEventListener('click', retakePhoto);
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', submitAttendance);
        }

        if (testPhotoBtn) {
            testPhotoBtn.addEventListener('click', testPhoto);
        }
    }

    function startCamera() {
        console.log('Starting camera...');
        showCameraStatus('Mengakses kamera...', true);
        initializeCamera();
    }

    function stopCamera() {
        console.log('Stopping camera...');
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        // Hide camera and show placeholder
        document.getElementById('camera').style.display = 'none';
        document.getElementById('camera-placeholder').style.display = 'flex';
        document.getElementById('camera-overlay').style.display = 'none';

        // Hide buttons
        document.getElementById('stop-camera-btn').style.display = 'none';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('test-photo-btn').style.display = 'none';

        // Show start button
        document.getElementById('start-camera-btn').style.display = 'inline-flex';

        hideCameraStatus();
        showNotification('Kamera dimatikan', 'info');
    }

    async function initializeCamera() {
        try {
            console.log('Requesting camera access...');

            // Check if browser supports getUserMedia
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Browser tidak mendukung akses kamera');
            }

            // Request camera access with fallback options
            const constraints = {
                video: {
                    facingMode: 'user', // Front camera for selfie
                    width: { ideal: 640, max: 1280 },
                    height: { ideal: 480, max: 720 }
                },
                audio: false
            };

            console.log('Camera constraints:', constraints);
            stream = await navigator.mediaDevices.getUserMedia(constraints);

            const videoElement = document.getElementById('camera');
            if (videoElement) {
                videoElement.srcObject = stream;

                // Wait for video to be ready
                videoElement.onloadedmetadata = function() {
                    console.log('Camera ready, video dimensions:', videoElement.videoWidth, 'x', videoElement.videoHeight);

                    // Show camera and hide placeholder
                    videoElement.style.display = 'block';
                    document.getElementById('camera-placeholder').style.display = 'none';
                    document.getElementById('camera-overlay').style.display = 'block';

                    // Show camera controls
                    document.getElementById('start-camera-btn').style.display = 'none';
                    document.getElementById('stop-camera-btn').style.display = 'inline-flex';
                    document.getElementById('capture-btn').style.display = 'inline-flex';
                    document.getElementById('test-photo-btn').style.display = 'inline-flex';

                    hideCameraStatus();
                    showNotification('Kamera berhasil diaktifkan! Posisikan wajah dalam frame dan ambil foto.', 'success');
                };

                videoElement.onerror = function(error) {
                    console.error('Video element error:', error);
                    throw new Error('Gagal menampilkan video dari kamera');
                };
            }

        } catch (err) {
            console.error('Error accessing camera:', err);
            hideCameraStatus();

            let errorMessage = 'Error mengakses kamera: ';
            if (err.name === 'NotAllowedError') {
                errorMessage += 'Akses kamera ditolak. Silakan izinkan akses kamera pada browser.';
            } else if (err.name === 'NotFoundError') {
                errorMessage += 'Kamera tidak ditemukan pada perangkat ini.';
            } else if (err.name === 'NotSupportedError') {
                errorMessage += 'Browser tidak mendukung fitur kamera.';
            } else {
                errorMessage += err.message;
            }

            showNotification(errorMessage, 'danger');
            showCameraStatus('❌ ' + errorMessage);
        }
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    updateLocationStatus();
                },
                function(error) {
                    console.error('Error getting location:', error);
                    showNotification('Error mendapatkan lokasi: ' + error.message, 'danger');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            showNotification('Geolocation tidak didukung oleh browser ini.', 'danger');
        }
    }

    function updateLocationStatus() {
        if (!currentLocation) return;

        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');

        locationStatus.style.display = 'block';

        locationInfo.innerHTML = `
            <div class="rounded-lg border border-success-200 bg-success-50 p-4">
                <div class="flex items-center mb-3">
                    <span class="text-xl mr-3">📍</span>
                    <div>
                        <h4 class="font-semibold text-success-900">Lokasi Terdeteksi</h4>
                        <p class="text-sm text-gray-600">Lokasi untuk absensi dinas luar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-3">
                    <div>
                        <span class="text-gray-700"><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</span>
                    </div>
                    <div>
                        <span class="text-gray-700"><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</span>
                    </div>
                </div>

                <div class="bg-success-100 border border-success-200 rounded p-3">
                    <div class="flex items-center">
                        <span class="text-success-700 font-medium text-sm">✓ Lokasi siap untuk absensi dinas luar</span>
                    </div>
                </div>
            </div>
        `;
    }

    function capturePhoto() {
        console.log('Capturing photo...');

        const video = document.getElementById('camera');
        if (!video || !stream) {
            showNotification('Kamera belum diaktifkan. Silakan aktifkan kamera terlebih dahulu.', 'danger');
            return;
        }

        if (video.videoWidth === 0 || video.videoHeight === 0) {
            showNotification('Video belum siap. Silakan tunggu sebentar dan coba lagi.', 'danger');
            return;
        }

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        // Set smaller canvas dimensions to reduce file size
        const maxWidth = 640;
        const maxHeight = 480;
        const videoAspectRatio = video.videoWidth / video.videoHeight;

        let canvasWidth, canvasHeight;

        if (video.videoWidth > video.videoHeight) {
            canvasWidth = Math.min(maxWidth, video.videoWidth);
            canvasHeight = canvasWidth / videoAspectRatio;
        } else {
            canvasHeight = Math.min(maxHeight, video.videoHeight);
            canvasWidth = canvasHeight * videoAspectRatio;
        }

        canvas.width = canvasWidth;
        canvas.height = canvasHeight;

        console.log('Original video dimensions:', video.videoWidth, 'x', video.videoHeight);
        console.log('Canvas dimensions:', canvasWidth, 'x', canvasHeight);

        // Draw the video frame to canvas
        context.drawImage(video, 0, 0, canvasWidth, canvasHeight);

        // Convert to base64 with compression
        capturedPhoto = canvas.toDataURL('image/jpeg', 0.6);

        console.log('Captured photo data length:', capturedPhoto.length);
        console.log('Captured photo size MB:', (capturedPhoto.length / 1024 / 1024).toFixed(2));

        // Show preview
        const capturedPhotoImg = document.getElementById('captured-photo');
        if (capturedPhotoImg) {
            capturedPhotoImg.src = capturedPhoto;
        }

        // Hide camera and show photo preview
        document.getElementById('camera').style.display = 'none';
        document.getElementById('photo-preview').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'none';
        document.getElementById('submit-btn').style.display = 'inline-flex';

        showNotification('Foto berhasil diambil!', 'success');
    }

    function retakePhoto() {
        console.log('Retaking photo...');

        capturedPhoto = null;

        // Hide photo preview and show camera
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('camera').style.display = 'block';
        document.getElementById('capture-btn').style.display = 'inline-flex';
        document.getElementById('submit-btn').style.display = 'none';

        showNotification('Siap untuk mengambil foto lagi', 'info');
    }

    function testPhoto() {
        if (!capturedPhoto) {
            showNotification('Silakan ambil foto terlebih dahulu dengan tombol "Ambil Foto".', 'danger');
            return;
        }

        console.log('Testing photo save...');
        const testBtn = document.getElementById('test-photo-btn');
        testBtn.disabled = true;
        testBtn.textContent = 'Testing...';

        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('testPhotoSave', capturedPhoto)
            .then((result) => {
                console.log('Test photo result:', result);
                testBtn.disabled = false;
                testBtn.textContent = 'Test Foto';
            })
            .catch((error) => {
                console.error('Test photo error:', error);
                testBtn.disabled = false;
                testBtn.textContent = 'Test Foto';
                showNotification('Error testing photo: ' + error.message, 'danger');
            });
    }

    function submitAttendance() {
        if (!capturedPhoto) {
            showNotification('Silakan ambil foto terlebih dahulu.', 'danger');
            return;
        }

        if (!currentLocation) {
            showNotification('Lokasi belum terdeteksi. Silakan coba lagi.', 'danger');
            getCurrentLocation();
            return;
        }

        console.log('Submitting attendance with photo length:', capturedPhoto.length);
        console.log('Location:', currentLocation);
        console.log('Current action:', currentAction);

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        // Call appropriate Livewire method based on current action
        let livewireMethod = '';
        if (currentAction === 'pagi') {
            livewireMethod = 'processCheckInPagi';
        } else if (currentAction === 'siang') {
            livewireMethod = 'processCheckInSiang';
        } else if (currentAction === 'sore') {
            livewireMethod = 'processCheckOut';
        }

        if (livewireMethod) {
            console.log('Calling ' + livewireMethod + '...');
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call(livewireMethod, capturedPhoto, currentLocation.latitude, currentLocation.longitude)
                .then((result) => {
                    console.log(livewireMethod + ' success:', result);
                    showNotification('Absensi ' + currentAction + ' berhasil! Halaman akan dimuat ulang...', 'success');
                    setTimeout(() => location.reload(), 2000);
                })
                .catch((error) => {
                    console.error(livewireMethod + ' error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Absen ' + currentAction.charAt(0).toUpperCase() + currentAction.slice(1) + ' Sekarang';
                    showNotification('Error: ' + error.message, 'danger');
                });
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Absen Sekarang';
            showNotification('Error: Aksi tidak dikenali', 'danger');
        }
    }

    function showNotification(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);

        // Use Filament's native notification system when available
        if (window.Livewire) {
            try {
                window.Livewire.find('<?php echo e($this->getId()); ?>').dispatch('notify', {
                    message: message,
                    type: type
                });
                return;
            } catch (e) {
                console.log('Livewire notification not available, using fallback');
            }
        }

        // Simple fallback notification
        const toast = document.createElement('div');
        const colorClass = {
            'success': 'bg-success-50 border-success-500 text-success-900',
            'danger': 'bg-danger-50 border-danger-500 text-danger-900',
            'warning': 'bg-warning-50 border-warning-500 text-warning-900',
            'info': 'bg-info-50 border-info-500 text-info-900'
        };

        toast.className = `fixed top-4 right-4 max-w-sm w-full ${colorClass[type] || colorClass.info} border-l-4 p-4 rounded shadow-lg z-50 transition-transform duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex justify-between items-start">
                <p class="text-sm font-medium">${message}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600 ml-2">
                    ✕
                </button>
            </div>
        `;

        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Cleanup camera stream when page unloads
    window.addEventListener('beforeunload', function() {
        if (stream) {
            console.log('Cleaning up camera stream...');
            stream.getTracks().forEach(track => track.stop());
        }
    });

    // Also cleanup when user navigates away
    window.addEventListener('pagehide', function() {
        if (stream) {
            console.log('Page hidden, cleaning up camera stream...');
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\Sucofindo-Absen\AdminAbsen\resources\views/filament/pegawai/pages/dinas-luar-attendance.blade.php ENDPATH**/ ?>