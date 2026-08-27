@extends('layouts.base')

@section('body')
    @include('partials.spinner')
    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

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
@endsection
