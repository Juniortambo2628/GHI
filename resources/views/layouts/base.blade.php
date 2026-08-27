<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name') . ' - ' . config('app.site_tagline', 'Bridging Global Compassion with Local Action'))</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="nonprofit, charity, East Africa, education, healthcare, community development" name="keywords">
    <meta content="@yield('description', 'Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.')" name="description">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('Logo/Square-White-BG.png') }}">

    @if(config('app.sentry_dsn'))
    <meta name="sentry-dsn" content="{{ config('app.sentry_dsn') }}">
    @endif
    <meta name="app-env" content="{{ config('app.env') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('preload_images')
    @foreach(explode(',', trim($__env->yieldContent('preload_images'), ',')) as $index => $image)
    @if($index === 0)
    <link rel="preload" as="image" href="{{ trim($image) }}" fetchpriority="high">
    @break
    @endif
    @endforeach
    @endif

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet" media="print" onload="this.media='all'" crossorigin>
    <noscript><link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet" crossorigin></noscript>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Critical CSS (inline) -->
    @php
    $criticalCssPath = public_path('css/critical.css');
    @endphp
    @if(file_exists($criticalCssPath))
    <style>{!! file_get_contents($criticalCssPath) !!}</style>
    @endif

    <!-- Custom GHI Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('css/style.css') }}" rel="stylesheet"></noscript>

    @php
    $themeCssPath = public_path('css/site-theme.css');
    @endphp
    @if(file_exists($themeCssPath))
    <link href="{{ asset('css/site-theme.css') }}?v={{ filemtime($themeCssPath) }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('css/site-theme.css') }}?v={{ filemtime($themeCssPath) }}" rel="stylesheet"></noscript>
    @endif

    @stack('styles')
</head>

<body>
    @yield('body')

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
