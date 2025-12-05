<?php
/**
 * Image Optimization Script
 * Compresses images using Intervention Image
 * 
 * Usage: php scripts/optimize-images.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());

// Directories to optimize
$directories = [
    __DIR__ . '/../Banners-and-portraits',
    __DIR__ . '/../img',
];

// Image quality settings
$quality = 85; // JPEG quality (0-100)
$maxWidth = 1920; // Maximum width for images
$maxHeight = 1080; // Maximum height for images

$totalProcessed = 0;
$totalSaved = 0;
$errors = [];

echo "Starting image optimization...\n\n";

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "Directory not found: $dir\n";
        continue;
    }

    echo "Processing directory: $dir\n";
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $path = $file->getPathname();
        $extension = strtolower($file->getExtension());

        // Only process image files
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            continue;
        }

        try {
            $originalSize = filesize($path);
            
            // Read image
            $image = $manager->read($path);
            
            // Get original dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Resize if too large
            if ($width > $maxWidth || $height > $maxHeight) {
                $image->scaleDown($maxWidth, $maxHeight);
            }
            
            // Optimize based on format
            if ($extension === 'png') {
                // PNG: reduce colors and optimize
                $image->toPng()->save($path);
            } else {
                // JPEG: save with quality setting
                $image->toJpeg($quality)->save($path);
            }
            
            $newSize = filesize($path);
            $saved = $originalSize - $newSize;
            $savedPercent = round(($saved / $originalSize) * 100, 2);
            
            $totalProcessed++;
            $totalSaved += $saved;
            
            echo "  ✓ " . basename($path) . " - Saved " . formatBytes($saved) . " ({$savedPercent}%)\n";
            
        } catch (Exception $e) {
            $errors[] = $path . ': ' . $e->getMessage();
            echo "  ✗ " . basename($path) . " - Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n";
echo "Optimization complete!\n";
echo "Total images processed: $totalProcessed\n";
echo "Total space saved: " . formatBytes($totalSaved) . "\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

