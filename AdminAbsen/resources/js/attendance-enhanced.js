/**
 * Enhanced Attendance Theme JavaScript
 * Provides interactive functionality for WFO and Dinas Luar attendance
 */

// Enhanced Theme Manager
class AttendanceThemeManager {
    constructor() {
        this.init();
        this.bindEvents();
    }

    init() {
        this.addEnhancedAnimations();
        this.initProgressBars();
        this.initTooltips();
        this.initCardAnimations();
    }

    bindEvents() {
        // Enhanced card hover effects
        document.addEventListener('DOMContentLoaded', () => {
            this.initIntersectionObserver();
            this.initSmoothScrolling();
            this.addRippleEffect();
        });

        // Theme toggle enhancement
        document.addEventListener('theme-changed', (e) => {
            this.handleThemeChange(e.detail.theme);
        });

        // Responsive breakpoint handling
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));
    }

    addEnhancedAnimations() {
        // Add staggered animations to cards
        const cards = document.querySelectorAll('.attendance-card, .attendance-widget, .wfo-header-card, .dinas-luar-widget-enhanced');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });

        // Add floating animation to important buttons
        const floatingButtons = document.querySelectorAll('.attendance-button-enhanced, .wfo-button-enhanced, .dinas-luar-button-enhanced');
        floatingButtons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-3px) scale(1.02)';
            });
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    initProgressBars() {
        const progressBars = document.querySelectorAll('.attendance-progress-bar, .dinas-luar-progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            // Animate to actual width after a short delay
            setTimeout(() => {
                bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
                bar.style.width = width;
            }, 300);
        });
    }

    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            this.addTooltip(element);
        });
    }

    addTooltip(element) {
        const tooltip = document.createElement('div');
        tooltip.className = 'attendance-tooltip';
        tooltip.textContent = element.getAttribute('data-tooltip');
        document.body.appendChild(tooltip);

        element.addEventListener('mouseenter', (e) => {
            const rect = e.target.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) + 'px';
            tooltip.style.top = (rect.top - 40) + 'px';
            tooltip.classList.add('show');
        });

        element.addEventListener('mouseleave', () => {
            tooltip.classList.remove('show');
        });
    }

    initCardAnimations() {
        const cards = document.querySelectorAll('.attendance-card-enhanced, .dinas-luar-card-enhanced');
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                this.createRipple(e, card);
            });
        });
    }

    createRipple(event, element) {
        const circle = document.createElement('span');
        const diameter = Math.max(element.clientWidth, element.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - element.offsetLeft - radius}px`;
        circle.style.top = `${event.clientY - element.offsetTop - radius}px`;
        circle.classList.add('ripple');

        const ripple = element.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        element.appendChild(circle);
    }

    initIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        const observeElements = document.querySelectorAll('.attendance-widget, .attendance-card, .stat-card-enhanced');
        observeElements.forEach(el => observer.observe(el));
    }

    initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    addRippleEffect() {
        const buttons = document.querySelectorAll('.attendance-btn, .wfo-button-enhanced, .dinas-luar-button-enhanced');
        buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.createButtonRipple(e, button);
            });
        });
    }

    createButtonRipple(event, button) {
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        `;
        
        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }

    handleThemeChange(theme) {
        document.body.classList.toggle('dark', theme === 'dark');
        
        // Update custom properties for theme
        const root = document.documentElement;
        if (theme === 'dark') {
            root.style.setProperty('--attendance-primary', '#60a5fa');
            root.style.setProperty('--attendance-bg-primary', '#0f172a');
        } else {
            root.style.setProperty('--attendance-primary', '#3b82f6');
            root.style.setProperty('--attendance-bg-primary', '#ffffff');
        }
    }

    handleResize() {
        // Recalculate responsive elements
        const widgets = document.querySelectorAll('.attendance-widget-enhanced, .dinas-luar-widget-enhanced');
        widgets.forEach(widget => {
            if (window.innerWidth < 768) {
                widget.classList.add('mobile-layout');
            } else {
                widget.classList.remove('mobile-layout');
            }
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Enhanced Camera Manager for WFO Attendance
class EnhancedCameraManager {
    constructor() {
        this.stream = null;
        this.capturedPhoto = null;
        this.constraints = {
            video: {
                facingMode: 'user',
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 }
            }
        };
        this.init();
    }

    async init() {
        this.bindEvents();
        this.setupStatusIndicators();
    }

    bindEvents() {
        const startBtn = document.getElementById('start-camera-btn');
        const stopBtn = document.getElementById('stop-camera-btn');
        const captureBtn = document.getElementById('capture-btn');
        const retakeBtn = document.getElementById('retake-photo');

        if (startBtn) startBtn.addEventListener('click', () => this.startCamera());
        if (stopBtn) stopBtn.addEventListener('click', () => this.stopCamera());
        if (captureBtn) captureBtn.addEventListener('click', () => this.capturePhoto());
        if (retakeBtn) retakeBtn.addEventListener('click', () => this.retakePhoto());
    }

    async startCamera() {
        try {
            this.showStatus('Mengakses kamera...', 'loading');
            this.stream = await navigator.mediaDevices.getUserMedia(this.constraints);
            
            const video = document.getElementById('camera');
            const placeholder = document.getElementById('camera-placeholder');
            
            if (video && placeholder) {
                video.srcObject = this.stream;
                video.style.display = 'block';
                placeholder.style.display = 'none';
                
                this.showCameraControls();
                this.showStatus('Kamera aktif', 'success');
                
                // Add camera frame effect
                video.classList.add('camera-active');
            }
        } catch (error) {
            console.error('Camera access error:', error);
            this.showStatus('Gagal mengakses kamera: ' + error.message, 'error');
        }
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        const video = document.getElementById('camera');
        const placeholder = document.getElementById('camera-placeholder');
        
        if (video && placeholder) {
            video.style.display = 'none';
            placeholder.style.display = 'flex';
            video.classList.remove('camera-active');
        }
        
        this.hideCameraControls();
        this.showStatus('Kamera dimatikan', 'info');
    }

    capturePhoto() {
        const video = document.getElementById('camera');
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        if (!video) return;
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
        this.showPhotoPreview();
        this.showStatus('Foto berhasil diambil', 'success');
    }

    retakePhoto() {
        const preview = document.getElementById('photo-preview');
        if (preview) {
            preview.style.display = 'none';
        }
        
        this.capturedPhoto = null;
        this.showCameraControls();
        this.showStatus('Siap mengambil foto ulang', 'info');
    }

    showPhotoPreview() {
        const preview = document.getElementById('photo-preview');
        const img = document.getElementById('captured-photo');
        
        if (preview && img && this.capturedPhoto) {
            img.src = this.capturedPhoto;
            preview.style.display = 'block';
            
            // Add photo preview animation
            preview.classList.add('photo-preview-animation');
            
            // Hide camera controls
            this.hideCameraControls();
            this.showSubmitButton();
        }
    }

    showCameraControls() {
        const elements = ['stop-camera-btn', 'capture-btn', 'test-photo-btn', 'test-size-btn'];
        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'inline-flex';
        });
        
        const startBtn = document.getElementById('start-camera-btn');
        if (startBtn) startBtn.style.display = 'none';
    }

    hideCameraControls() {
        const elements = ['stop-camera-btn', 'capture-btn', 'test-photo-btn', 'test-size-btn'];
        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }

    showSubmitButton() {
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.style.display = 'inline-flex';
            submitBtn.classList.add('pulse-animation');
        }
    }

    showStatus(message, type = 'info') {
        // Create or update status notification
        let notification = document.getElementById('camera-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'camera-notification';
            notification.className = 'fixed top-4 right-4 z-50 max-w-sm';
            document.body.appendChild(notification);
        }
        
        const colors = {
            loading: 'bg-blue-100 border-blue-500 text-blue-700',
            success: 'bg-green-100 border-green-500 text-green-700',
            error: 'bg-red-100 border-red-500 text-red-700',
            info: 'bg-gray-100 border-gray-500 text-gray-700'
        };
        
        notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg border-l-4 shadow-lg ${colors[type] || colors.info}`;
        notification.innerHTML = `
            <div class="flex items-center">
                ${type === 'loading' ? '<div class="animate-spin h-4 w-4 mr-2 border-2 border-current border-t-transparent rounded-full"></div>' : ''}
                <span class="font-medium">${message}</span>
            </div>
        `;
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            if (notification && type !== 'loading') {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
    }

    setupStatusIndicators() {
        // Add custom CSS for camera status
        const style = document.createElement('style');
        style.textContent = `
            .camera-active {
                border: 3px solid #10b981;
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
                animation: camera-pulse 2s infinite;
            }
            
            @keyframes camera-pulse {
                0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
                50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
            }
            
            .photo-preview-animation {
                animation: photo-fade-in 0.5s ease-out;
            }
            
            @keyframes photo-fade-in {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
            
            .pulse-animation {
                animation: pulse 2s infinite;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Enhanced Location Manager
class EnhancedLocationManager {
    constructor() {
        this.currentLocation = null;
        this.offices = [];
        this.watchId = null;
        this.init();
    }

    init() {
        this.setupGeolocation();
        this.loadOfficeLocations();
    }

    async loadOfficeLocations() {
        // This would typically load from the backend
        // For now, we'll use the existing office data
        try {
            if (window.offices) {
                this.offices = window.offices;
            }
        } catch (error) {
            console.error('Failed to load office locations:', error);
        }
    }

    setupGeolocation() {
        if (!navigator.geolocation) {
            this.showLocationError('Geolocation tidak didukung oleh browser ini');
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        };

        this.watchId = navigator.geolocation.watchPosition(
            (position) => this.handleLocationSuccess(position),
            (error) => this.handleLocationError(error),
            options
        );
    }

    handleLocationSuccess(position) {
        this.currentLocation = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy
        };

        this.updateLocationDisplay();
        this.checkOfficeProximity();
    }

    handleLocationError(error) {
        let message = 'Gagal mendapatkan lokasi: ';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                message += 'Akses lokasi ditolak';
                break;
            case error.POSITION_UNAVAILABLE:
                message += 'Informasi lokasi tidak tersedia';
                break;
            case error.TIMEOUT:
                message += 'Timeout mendapatkan lokasi';
                break;
            default:
                message += 'Error tidak diketahui';
                break;
        }
        this.showLocationError(message);
    }

    updateLocationDisplay() {
        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');
        
        if (locationStatus && locationInfo && this.currentLocation) {
            locationStatus.style.display = 'block';
            locationInfo.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Lokasi berhasil dideteksi
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>Latitude: ${this.currentLocation.latitude.toFixed(6)}</p>
                        <p>Longitude: ${this.currentLocation.longitude.toFixed(6)}</p>
                        <p>Akurasi: ±${Math.round(this.currentLocation.accuracy)} meter</p>
                    </div>
                </div>
            `;
        }
    }

    checkOfficeProximity() {
        if (!this.currentLocation || !this.offices.length) return;

        const nearbyOffices = this.offices.filter(office => {
            const distance = this.calculateDistance(
                this.currentLocation.latitude,
                this.currentLocation.longitude,
                office.latitude,
                office.longitude
            );
            return distance <= office.radius;
        });

        this.updateProximityStatus(nearbyOffices);
    }

    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth's radius in meters
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    updateProximityStatus(nearbyOffices) {
        const locationInfo = document.getElementById('location-info');
        if (!locationInfo) return;

        if (nearbyOffices.length > 0) {
            const office = nearbyOffices[0];
            const distance = this.calculateDistance(
                this.currentLocation.latitude,
                this.currentLocation.longitude,
                office.latitude,
                office.longitude
            );

            locationInfo.innerHTML += `
                <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                    <div class="flex items-center text-green-700 dark:text-green-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Dalam jangkauan kantor
                    </div>
                    <div class="text-sm text-green-600 dark:text-green-400 mt-1">
                        <p>${office.name}</p>
                        <p>Jarak: ${Math.round(distance)} meter</p>
                    </div>
                </div>
            `;
        } else {
            locationInfo.innerHTML += `
                <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                    <div class="flex items-center text-red-700 dark:text-red-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        Di luar jangkauan kantor
                    </div>
                    <div class="text-sm text-red-600 dark:text-red-400 mt-1">
                        Anda berada di luar radius kantor yang diizinkan untuk absensi
                    </div>
                </div>
            `;
        }
    }

    showLocationError(message) {
        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');
        
        if (locationStatus && locationInfo) {
            locationStatus.style.display = 'block';
            locationInfo.innerHTML = `
                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                    <div class="flex items-center text-red-700 dark:text-red-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Error Lokasi
                    </div>
                    <div class="text-sm text-red-600 dark:text-red-400 mt-1">
                        ${message}
                    </div>
                </div>
            `;
        }
    }

    destroy() {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme manager
    window.attendanceTheme = new AttendanceThemeManager();
    
    // Initialize camera manager if on attendance page
    if (document.getElementById('camera')) {
        window.cameraManager = new EnhancedCameraManager();
    }
    
    // Initialize location manager if location features are present
    if (document.getElementById('location-status')) {
        window.locationManager = new EnhancedLocationManager();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.locationManager) {
        window.locationManager.destroy();
    }
    
    if (window.cameraManager && window.cameraManager.stream) {
        window.cameraManager.stopCamera();
    }
});

// Export for use in other scripts
window.AttendanceEnhanced = {
    ThemeManager: AttendanceThemeManager,
    CameraManager: EnhancedCameraManager,
    LocationManager: EnhancedLocationManager
};
