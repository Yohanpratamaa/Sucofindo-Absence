@extends(filament()->getLayout())

@section('content')
    <style>
        /* Hide Filament branding immediately */
        .fi-widget[data-widget="filament-widgets-filament-info-widget"],
        .filament-widgets-filament-info-widget,
        .fi-wi-info {
            display: none !important;
        }

        /* Hide version text */
        .fi-main *:contains("v3.3.28"),
        *[class*="version"]:contains("v3") {
            display: none !important;
        }

        /* Hide GitHub link */
        a[href*="github.com/filamentphp"] {
            display: none !important;
        }

        /* Hide Documentation link */
        a[href*="filamentphp.com"] {
            display: none !important;
        }

        /* Hide footer branding */
        .fi-footer,
        .fi-simple-footer,
        footer {
            display: none !important;
        }
    </style>

    @parent
@endsection
