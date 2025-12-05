<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo e($pageTitle); ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="nonprofit, charity, East Africa, education, healthcare, community development" name="keywords">
    <meta content="<?php echo e($pageDescription); ?>" name="description">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/Logo/Square-White-BG.png">

    <?php if (defined('SENTRY_DSN') && SENTRY_DSN) : ?>
    <meta name="sentry-dsn" content="<?php echo e(SENTRY_DSN); ?>">
    <?php endif;
    ?>
    <meta name="app-env" content="<?php echo e(ENVIRONMENT); ?>">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- Preload only the first critical hero image (the one immediately visible) -->
    <?php if ($preloadImages !== [] && isset($preloadImages[0])) : ?>
    <link rel="preload" as="image" href="<?php echo e($preloadImages[0]); ?>" fetchpriority="high">
    <?php endif;
    ?>

    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://ajax.googleapis.com">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet" media="print" onload="this.media='all'" crossorigin>
    <noscript><link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet" crossorigin></noscript> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/lib/fontawesome/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/lib/bootstrap-icons/bootstrap-icons.min.css">

    <!-- Libraries Stylesheet -->
    <link href="<?php echo BASE_URL; ?>/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?php echo BASE_URL; ?>/css/bootstrap.min.css" rel="stylesheet">

    <!-- Critical CSS - Inline for faster initial render -->
    <style>
        <?php
        $criticalCssFile = BASE_PATH . '/css/critical.css';
        if (file_exists($criticalCssFile)) {
            echo file_get_contents($criticalCssFile);
        }
        ?>
    </style>

    <!-- Full CSS - Load asynchronously -->
    <link href="<?php echo BASE_URL; ?>/css/style.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="<?php echo BASE_URL; ?>/css/style.css" rel="stylesheet"></noscript>

    <?php if (file_exists($themeCssFile)) : ?>
    <link href="<?php echo BASE_URL; ?>/css/site-theme.css?v=<?php echo filemtime($themeCssFile); ?>" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="<?php echo BASE_URL; ?>/css/site-theme.css?v=<?php echo filemtime($themeCssFile); ?>" rel="stylesheet"></noscript>
    <?php endif;
    ?>

    <!-- Spinner Hide Script (Fallback - works even if main.js fails) -->
    <script>
        // Hide spinner when page is fully loaded (vanilla JS, no dependencies)
        (function() {
            function hideSpinner() {
                var spinner = document.getElementById('spinner');
                if (spinner) {
                    spinner.classList.remove('show');
                    // Also set display none as fallback
                    setTimeout(function() {
                        if (spinner.classList.contains('show')) {
                            spinner.style.display = 'none';
                        }
                    }, 1000);
                }
            }

            // Multiple strategies to ensure spinner hides
            // Strategy 1: If page is already loaded
            if (document.readyState === 'complete') {
                setTimeout(hideSpinner, 100);
            } else {
                // Strategy 2: Wait for DOMContentLoaded
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(hideSpinner, 200);
                });

                // Strategy 3: Wait for window load event (all resources)
                window.addEventListener('load', function() {
                    setTimeout(hideSpinner, 100);
                });
            }

            // Strategy 4: Fallback timeout (max 3 seconds)
            setTimeout(function() {
                hideSpinner();
            }, 3000);
        })();
    </script>
</head>

<body>
