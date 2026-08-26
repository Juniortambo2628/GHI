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

    <!-- Font Awesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom GHI Styles -->
    @php
    $criticalCssPath = public_path('css/critical.css');
    @endphp
    @if(file_exists($criticalCssPath))
    <style>{!! file_get_contents($criticalCssPath) !!}</style>
    @endif

    <link href="{{ asset('css/style.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('css/style.css') }}" rel="stylesheet"></noscript>

    @php
    $themeCssPath = public_path('css/site-theme.css');
    @endphp
    @if(file_exists($themeCssPath))
    <link href="{{ asset('css/site-theme.css') }}?v={{ filemtime($themeCssPath) }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('css/site-theme.css') }}?v={{ filemtime($themeCssPath) }}" rel="stylesheet"></noscript>
    @endif

    <!-- Spinner Hide Script (Fallback) -->
    <script>
        (function() {
            function hideSpinner() {
                var spinner = document.getElementById('spinner');
                if (spinner) {
                    spinner.style.display = 'none';
                }
            }
            if (document.readyState === 'complete') {
                setTimeout(hideSpinner, 100);
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(hideSpinner, 200);
                });
                window.addEventListener('load', function() {
                    setTimeout(hideSpinner, 100);
                });
            }
            setTimeout(hideSpinner, 3000);
        })();
    </script>

    @stack('styles')
</head>

<body>
    @include('partials.spinner')
    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vanilla Lightbox -->
    <style>
        .vl-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
        .vl-overlay.active { opacity: 1; }
        .vl-overlay img { max-width: 90vw; max-height: 90vh; border-radius: 4px; }
        .vl-close { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 2rem; cursor: pointer; background: none; border: none; z-index: 10000; }
        .vl-caption { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: #fff; font-size: 0.9rem; text-align: center; max-width: 80%; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var overlay = document.createElement('div');
            overlay.className = 'vl-overlay';
            overlay.innerHTML = '<button class="vl-close" aria-label="Close">&times;</button><img src="" alt=""><div class="vl-caption"></div>';
            document.body.appendChild(overlay);

            var img = overlay.querySelector('img');
            var caption = overlay.querySelector('.vl-caption');
            var closeBtn = overlay.querySelector('.vl-close');

            function openLightbox(src, alt) {
                img.src = src;
                img.alt = alt || '';
                caption.textContent = alt || '';
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(function() { img.src = ''; }, 300);
            }

            closeBtn.addEventListener('click', closeLightbox);
            overlay.addEventListener('click', function(e) { if (e.target === overlay) closeLightbox(); });
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });

            document.addEventListener('click', function(e) {
                var link = e.target.closest('[data-lightbox]');
                if (link) {
                    e.preventDefault();
                    var src = link.getAttribute('href') || link.querySelector('img')?.src || '';
                    var alt = link.getAttribute('data-lightbox-caption') || link.querySelector('img')?.alt || link.getAttribute('data-lightbox') || '';
                    openLightbox(src, alt);
                }
            });
        });
    </script>

    <!-- Modern JavaScript -->
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>

</html>
