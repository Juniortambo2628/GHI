<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>404 - Page Not Found | {{ config('app.name', 'Global Harmony Initiative') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" type="image/png" href="{{ asset('Logo/Square-White-BG.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/critical.css') }}" rel="stylesheet">
    <link href="{{ asset('css/site-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.spinner')

    <section class="hero-section error-hero-bg">
        <div class="container text-center text-white">
            <div class="error-code">404</div>
            <h1 class="display-3 fw-bold mb-4">Page Not Found</h1>
            <p class="lead mb-5 error-lead-text">
                Oops! The page you're looking for seems to have wandered off. Don't worry, even the best explorers get lost sometimes.
            </p>
            <div>
                <a href="{{ route('home') }}" class="btn btn-light btn-lg me-3"><i class="bi bi-house-door me-2"></i>Go Home</a>
                <a href="{{ url('/about') }}" class="btn btn-outline-light btn-lg"><i class="bi bi-info-circle me-2"></i>About Us</a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="text-center mb-5">Here's Where You Might Want to Go</h2>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                                <div class="card-body p-4">
                                    <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-purple"><i class="bi bi-house-door fs-3 text-white"></i></div>
                                    <h5 class="card-title">Home</h5>
                                    <p class="card-text text-muted small">Return to our homepage</p>
                                    <a href="{{ route('home') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                                <div class="card-body p-4">
                                    <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-pink"><i class="bi bi-people fs-3 text-white"></i></div>
                                    <h5 class="card-title">About Us</h5>
                                    <p class="card-text text-muted small">Learn about our mission</p>
                                    <a href="{{ url('/about') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                                <div class="card-body p-4">
                                    <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-blue"><i class="bi bi-briefcase fs-3 text-white"></i></div>
                                    <h5 class="card-title">Our Work</h5>
                                    <p class="card-text text-muted small">Explore our initiatives</p>
                                    <a href="{{ route('initiatives.index') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                                <div class="card-body p-4">
                                    <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-green"><i class="bi bi-envelope fs-3 text-white"></i></div>
                                    <h5 class="card-title">Contact</h5>
                                    <p class="card-text text-muted small">Get in touch with us</p>
                                    <a href="{{ route('contact') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-4">Looking for Something Specific?</h3>
                            <p class="text-muted mb-4">Try searching for what you need</p>
                            <form action="{{ url('/search') }}" method="GET" class="d-flex gap-2">
                                <input type="text" name="q" class="form-control form-control-lg" placeholder="Search our website..." required>
                                <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
