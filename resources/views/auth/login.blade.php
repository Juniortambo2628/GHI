<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Global Harmony Initiative') }} | {{ __('Sign in') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
</head>
<body class="admin-auth-page">
    <main class="admin-auth-shell">
        <section class="admin-auth-panel" aria-labelledby="login-heading">
            <a class="admin-auth-brand" href="{{ url('/') }}" aria-label="{{ config('app.name') }} home">
                <img src="{{ asset('Logo/Square-White-BG.png') }}" alt="{{ config('app.name') }} logo">
            </a>

            <div class="admin-auth-content">
                <p class="admin-auth-kicker">{{ __('Admin portal') }}</p>
                <h1 id="login-heading">{{ __('Welcome Back') }}</h1>
                <p class="admin-auth-intro">{{ __('Today is a new day. It is your day. You shape it. Sign in to start managing your projects.') }}</p>

                @if (session('status'))
                    <p class="admin-auth-status" role="status">{{ session('status') }}</p>
                @endif

                <form class="admin-auth-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="admin-auth-field">
                        <label for="email">{{ __('Email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Example@email.com" required autofocus autocomplete="username">
                        @if ($errors->get('email'))
                            <ul class="admin-auth-errors" role="alert">
                                @foreach ($errors->get('email') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="admin-auth-field">
                        <div class="admin-auth-label-row">
                            <label for="password">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" placeholder="At least 8 characters" required autocomplete="current-password">
                        @if ($errors->get('password'))
                            <ul class="admin-auth-errors" role="alert">
                                @foreach ($errors->get('password') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <label class="admin-auth-remember" for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    <button class="admin-auth-submit" type="submit">{{ __('Sign in') }}</button>
                </form>
            </div>

            <p class="admin-auth-footer">{{ __('© :year All rights reserved', ['year' => now()->year]) }}</p>
        </section>

        <aside class="admin-auth-image" aria-label="{{ __('Global Harmony Initiative community') }}"></aside>
    </main>
</body>
</html>
