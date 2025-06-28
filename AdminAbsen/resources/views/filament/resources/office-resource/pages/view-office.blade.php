@php
    $record = $this->getRecord();
@endphp

<x-filament-panels::page>
    <style>
        /* Responsive maps container */
        .fi-section-content-ctn {
            overflow: visible !important;
        }
        
        /* Maps specific responsive fixes */
        .map-picker-container {
            position: relative !important;
            z-index: 10 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }
        
        .leaflet-container {
            position: relative !important;
            z-index: 10 !important;
            width: 100% !important;
            height: 400px !important;
            max-width: 100% !important;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .leaflet-container {
                height: 300px !important;
                z-index: 10 !important;
            }
            
            .map-picker-container {
                margin: 0 !important;
                padding: 0 !important;
                border-radius: 8px !important;
                overflow: hidden !important;
            }
            
            /* Ensure maps dont overlap sidebar */
            .fi-sidebar-open .leaflet-container {
                z-index: 10 !important;
                position: relative !important;
            }
            
            /* Fix for mobile sidebar interaction */
            .fi-sidebar-open .map-picker-container {
                pointer-events: auto !important;
                z-index: 10 !important;
            }
            
            /* Prevent maps from covering sidebar when overlay is open */
            .fi-sidebar-overlay-open .leaflet-container {
                pointer-events: none !important;
                opacity: 0.5 !important;
            }
            
            .fi-sidebar-overlay-open .map-picker-container {
                pointer-events: none !important;
                filter: blur(1px) !important;
            }
            
            /* When sidebar is opened, reduce map interaction */
            .fi-main[data-sidebar-open="true"] .leaflet-container {
                pointer-events: none !important;
            }
            
            .fi-main[data-sidebar-open="true"] .map-picker-container {
                pointer-events: none !important;
            }
        }
        
        @media (max-width: 480px) {
            .leaflet-container {
                height: 250px !important;
            }
            
            /* Smaller controls on mobile */
            .leaflet-control-zoom {
                transform: scale(0.8) !important;
            }
            
            .leaflet-control-fullscreen {
                transform: scale(0.8) !important;
            }
        }
        
        /* Section responsive improvements */
        @media (max-width: 768px) {
            .fi-section {
                padding: 1rem !important;
            }
            
            .fi-section-header {
                margin-bottom: 1rem !important;
            }
            
            .fi-section-content {
                gap: 1rem !important;
            }
            
            /* Grid adjustments for mobile */
            .grid.grid-cols-2 {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            .grid.grid-cols-3 {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }
        }
        
        /* Ensure proper stacking context */
        .fi-main {
            position: relative !important;
            z-index: 1 !important;
        }
        
        .fi-sidebar {
            z-index: 50 !important;
        }
        
        /* Sidebar overlay fix */
        .fi-sidebar-overlay {
            z-index: 45 !important;
        }
        
        /* Office details responsive */
        @media (max-width: 640px) {
            .fi-in-text-entry-content {
                font-size: 0.875rem !important;
            }
            
            .fi-in-text-entry-label {
                font-size: 0.8rem !important;
                font-weight: 600 !important;
            }
            
            /* Responsive badges */
            .fi-badge {
                font-size: 0.75rem !important;
                padding: 0.25rem 0.5rem !important;
            }
        }
        
        /* Map container specific fixes */
        .responsive-map-container {
            position: relative !important;
            overflow: hidden !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        
        @media (max-width: 768px) {
            .responsive-map-container {
                border-radius: 8px !important;
                margin: 0.5rem 0 !important;
            }
        }
        
        /* Schedule section responsive */
        @media (max-width: 768px) {
            .fi-in-repeatable-entry {
                padding: 0.75rem !important;
                margin-bottom: 0.5rem !important;
                border-radius: 8px !important;
                background: rgba(0, 0, 0, 0.02) !important;
            }
        }
        
        /* Header actions responsive */
        @media (max-width: 640px) {
            .fi-header-actions {
                gap: 0.5rem !important;
            }
            
            .fi-btn {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.875rem !important;
            }
        }
        
        /* Improve touch targets on mobile */
        @media (max-width: 768px) {
            .fi-btn, .fi-badge, .leaflet-control-zoom-in, .leaflet-control-zoom-out {
                min-height: 44px !important;
                min-width: 44px !important;
            }
        }
        
        /* Additional fixes for maps overlapping sidebar */
        @media (max-width: 768px) {
            /* When sidebar is transitioning or open, blur the main content */
            body:has(.fi-sidebar-open) .fi-main {
                filter: blur(0.5px) !important;
                pointer-events: none !important;
            }
            
            /* But keep the sidebar interactive */
            body:has(.fi-sidebar-open) .fi-sidebar {
                filter: none !important;
                pointer-events: auto !important;
            }
            
            /* Restore when sidebar is closed */
            body:not(:has(.fi-sidebar-open)) .fi-main {
                filter: none !important;
                pointer-events: auto !important;
            }
        }
    </style>

    <div>
        {{ $this->infolist }}
    </div>
</x-filament-panels::page>
