<?php

/**
 * Image Processing Service using Intervention Image
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use GHI\Services\FileService;

class ImageService
{
    private readonly ImageManager $manager;

    private readonly string $basePath;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
    }

    /**
     * Resize image
     *
     * @param string $imagePath Path to image (relative to base path)
     * @param int $width Target width
     * @param int $height Target height
     * @param string|null $outputPath Output path (optional, if null overwrites original)
     * @param bool $maintainAspectRatio Whether to maintain aspect ratio
     * @return bool Success status
     */
    public function resize(string $imagePath, int $width, int $height, ?string $outputPath = null, bool $maintainAspectRatio = true): bool
    {
        try {
            $fullPath = $this->basePath . '/' . ltrim($imagePath, '/');

            if (!FileService::exists($imagePath)) {
                throw new \Exception('Image not found: ' . $imagePath);
            }

            $image = $this->manager->read($fullPath);

            if ($maintainAspectRatio) {
                $image->scale($width, $height);
            } else {
                $image->resize($width, $height);
            }

            $outputFullPath = $outputPath !== null && $outputPath !== '' && $outputPath !== '0' ? $this->basePath . '/' . ltrim($outputPath, '/') : $fullPath;
            $image->save($outputFullPath);

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Image resize failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Create thumbnail
     *
     * @param string $imagePath Path to image
     * @param string $outputPath Output path
     * @param int $size Thumbnail size (square)
     * @return bool Success status
     */
    public function createThumbnail(string $imagePath, string $outputPath, int $size = 300): bool
    {
        try {
            $fullPath = $this->basePath . '/' . ltrim($imagePath, '/');

            if (!FileService::exists($imagePath)) {
                throw new \Exception('Image not found: ' . $imagePath);
            }

            $image = $this->manager->read($fullPath);
            $image->cover($size, $size);

            $outputFullPath = $this->basePath . '/' . ltrim($outputPath, '/');

            // Ensure output directory exists
            $outputDir = dirname($outputFullPath);
            if (!is_dir($outputDir)) {
                FileService::createDirectory(str_replace($this->basePath . '/', '', $outputDir));
            }

            $image->save($outputFullPath);

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Thumbnail creation failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Optimize image (reduce quality)
     *
     * @param string $imagePath Path to image
     * @param int $quality Quality (1-100)
     * @param string|null $outputPath Output path (optional)
     * @return bool Success status
     */
    public function optimize(string $imagePath, int $quality = 85, ?string $outputPath = null): bool
    {
        try {
            $fullPath = $this->basePath . '/' . ltrim($imagePath, '/');

            if (!FileService::exists($imagePath)) {
                throw new \Exception('Image not found: ' . $imagePath);
            }

            $image = $this->manager->read($fullPath);

            $outputFullPath = $outputPath !== null && $outputPath !== '' && $outputPath !== '0' ? $this->basePath . '/' . ltrim($outputPath, '/') : $fullPath;
            $image->save($outputFullPath, quality: $quality);

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Image optimization failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Process uploaded image (resize and optimize)
     *
     * @param string $imagePath Path to uploaded image
     * @param array $options Processing options
     * @return array Result with paths and sizes
     */
    public function processUploadedImage(string $imagePath, array $options = []): array
    {
        $defaultOptions = [
            'maxWidth' => 1080,
            'maxHeight' => 1080,
            'quality' => 85,
            'createThumbnail' => true,
            'thumbnailSize' => 300,
        ];

        $options = array_merge($defaultOptions, $options);
        $result = [
            'success' => false,
            'original' => $imagePath,
            'processed' => null,
            'thumbnail' => null,
            'originalSize' => 0,
            'processedSize' => 0,
            'thumbnailSize' => 0,
        ];

        try {
            if (!FileService::exists($imagePath)) {
                throw new \Exception('Image not found: ' . $imagePath);
            }

            $originalSize = FileService::getSize($imagePath);
            $result['originalSize'] = $originalSize;

            // Resize if needed
            $image = $this->manager->read($this->basePath . '/' . ltrim($imagePath, '/'));
            $currentWidth = $image->width();
            $currentHeight = $image->height();

            if ($currentWidth > $options['maxWidth'] || $currentHeight > $options['maxHeight']) {
                $image->scale($options['maxWidth'], $options['maxHeight']);
            }

            // Optimize
            $processedPath = $imagePath; // Overwrite original
            $image->save($this->basePath . '/' . ltrim($processedPath, '/'), quality: $options['quality']);
            $result['processed'] = $processedPath;
            $result['processedSize'] = FileService::getSize($processedPath);

            // Create thumbnail
            if ($options['createThumbnail']) {
                $pathInfo = pathinfo($imagePath);
                $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

                if ($this->createThumbnail($imagePath, $thumbnailPath, $options['thumbnailSize'])) {
                    $result['thumbnail'] = $thumbnailPath;
                    $result['thumbnailSize'] = FileService::getSize($thumbnailPath);
                }
            }

            $result['success'] = true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Image processing failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }

            $result['error'] = $exception->getMessage();
        }

        return $result;
    }

    /**
     * Generate responsive image sizes (for srcset)
     * Automatically handles large images to prevent memory issues
     *
     * @param string $imagePath Path to image
     * @param array $sizes Array of widths (e.g., [400, 800, 1200])
     * @return array Array of generated image paths with widths
     */
    public function generateResponsiveSizes(string $imagePath, array $sizes = [400, 600, 800, 1080]): array
    {
        $results = [];

        try {
            if (!FileService::exists($imagePath)) {
                return $results;
            }

            $fullPath = $this->basePath . '/' . ltrim($imagePath, '/');

            // Check image dimensions first using lightweight getimagesize (doesn't load full image)
            $imageInfo = @getimagesize($fullPath);
            if ($imageInfo === false) {
                return $results;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            // Check if image is too large to process safely (prevents memory exhaustion)
            if ($this->isImageTooLarge($fullPath, 3000)) {
                // Image is too large - skip responsive size generation to prevent memory exhaustion
                if (function_exists('log_message')) {
                    log_message('warning', 'Image too large for responsive size generation, skipping', [
                        'path' => $imagePath,
                        'dimensions' => sprintf('%dx%d', $originalWidth, $originalHeight)
                    ]);
                }

                return []; // Return empty array to use original image
            }

            // If image exceeds maxDimension but is still processable, resize it first
            // Images are now pre-resized to 1080px max, so this is mainly for safety
            $maxDimension = 1080;
            if ($originalWidth > $maxDimension || $originalHeight > $maxDimension) {
                $resizeSuccess = $this->resizeIfNeeded($fullPath, $maxDimension);
                if (!$resizeSuccess) {
                    // Resize failed or image too large - skip optimization
                    return [];
                }

                // Re-read dimensions after resize
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo !== false) {
                    $originalWidth = $imageInfo[0];
                }
            }

            $pathInfo = pathinfo($imagePath);

            // Increase memory limit for image processing
            $originalMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '512M');

            foreach ($sizes as $width) {
                // Don't upscale
                if ($width > $originalWidth) {
                    continue;
                }

                $filename = $pathInfo['filename'] . '_' . $width . 'w.' . $pathInfo['extension'];
                $outputPath = $pathInfo['dirname'] . '/' . $filename;
                $outputFullPath = $this->basePath . '/' . ltrim($outputPath, '/');

                // Check if already exists
                if (file_exists($outputFullPath)) {
                    $results[] = [
                        'url' => str_replace(BASE_PATH, BASE_URL, $outputPath),
                        'width' => $width,
                        'path' => $outputPath,
                    ];
                    continue;
                }

                // Read image fresh for each size (prevents memory issues from cloning)
                // This is more memory-efficient than cloning large images
                $image = $this->manager->read($fullPath);
                $image->scale($width);
                $image->save($outputFullPath, quality: 85);

                // Free memory immediately
                unset($image);

                $results[] = [
                    'url' => str_replace(BASE_PATH, BASE_URL, $outputPath),
                    'width' => $width,
                    'path' => $outputPath,
                ];
            }

            // Restore original memory limit
            ini_set('memory_limit', $originalMemoryLimit);
        } catch (\Exception $exception) {
            // Restore original memory limit on error
            if (isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }

            if (function_exists('log_message')) {
                log_message('error', 'Responsive image generation failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Check if image is too large to process safely
     * Uses lightweight getimagesize to avoid loading full image into memory
     *
     * @param string $fullPath Full path to image
     * @param int $maxSafeDimension Maximum safe dimension (default 3000px)
     * @return bool True if image is too large to process safely
     */
    private function isImageTooLarge(string $fullPath, int $maxSafeDimension = 3000): bool
    {
        $imageInfo = @getimagesize($fullPath);
        if ($imageInfo === false) {
            return true; // Assume too large if we can't read dimensions
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Check if image exceeds safe dimensions
        // For very large images (e.g., 4688x7025), processing will exhaust memory
        return $width > $maxSafeDimension || $height > $maxSafeDimension;
    }

    /**
     * Resize image if it exceeds maximum dimensions
     * Only attempts resize if image is within safe processing limits
     *
     * @param string $fullPath Full path to image
     * @param int $maxDimension Maximum width or height
     * @return bool True if resize was successful or not needed
     */
    private function resizeIfNeeded(string $fullPath, int $maxDimension): bool
    {
        try {
            // First check if image is too large to process safely
            if ($this->isImageTooLarge($fullPath, 3000)) {
                // Image is too large - skip optimization to prevent memory exhaustion
                if (function_exists('log_message')) {
                    log_message('warning', 'Image too large for safe processing, skipping optimization', [
                        'path' => $fullPath
                    ]);
                }

                return false; // Indicate that optimization should be skipped
            }

            $imageInfo = @getimagesize($fullPath);
            if ($imageInfo === false) {
                return false;
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            if ($width <= $maxDimension && $height <= $maxDimension) {
                return true; // No resize needed
            }

            // Only attempt resize if image is within safe limits
            // Increase memory limit temporarily
            $originalMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '512M');

            try {
                // Resize to max dimension while maintaining aspect ratio
                $image = $this->manager->read($fullPath);
                $image->scale($maxDimension, $maxDimension);
                $image->save($fullPath, quality: 85);
                unset($image);

                // Restore memory limit
                ini_set('memory_limit', $originalMemoryLimit);
                return true;
            } catch (\Exception $e) {
                // Restore memory limit on error
                ini_set('memory_limit', $originalMemoryLimit);
                throw $e;
            }
        } catch (\Exception $exception) {
            // Silently fail - will skip optimization
            if (function_exists('log_message')) {
                log_message('warning', 'Pre-resize failed, skipping optimization', [
                    'path' => $fullPath,
                    'error' => $exception->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Convert image to WebP format
     * Automatically resizes large images to prevent memory exhaustion
     *
     * @param string $imagePath Path to image
     * @param int $quality WebP quality (1-100)
     * @param int $maxDimension Maximum width or height (prevents memory issues)
     * @return string|null WebP path or null on failure
     */
    public function convertToWebP(string $imagePath, int $quality = 85, int $maxDimension = 1080): ?string
    {
        try {
            if (!FileService::exists($imagePath)) {
                return null;
            }

            $fullPath = $this->basePath . '/' . ltrim($imagePath, '/');

            // Check image dimensions first using lightweight getimagesize
            $imageInfo = @getimagesize($fullPath);
            if ($imageInfo === false) {
                return null;
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Check if image is too large to process safely (prevents memory exhaustion)
            if ($this->isImageTooLarge($fullPath, 3000)) {
                // Image is too large - skip WebP conversion to prevent memory exhaustion
                if (function_exists('log_message')) {
                    log_message('warning', 'Image too large for WebP conversion, skipping', [
                        'path' => $imagePath,
                        'dimensions' => sprintf('%dx%d', $width, $height)
                    ]);
                }

                return null; // Return null to use original image
            }

            // If image exceeds maxDimension but is still processable, resize it first
            if ($width > $maxDimension || $height > $maxDimension) {
                $resizeSuccess = $this->resizeIfNeeded($fullPath, $maxDimension);
                if (!$resizeSuccess) {
                    // Resize failed or image too large - skip optimization
                    return null;
                }

                // Re-read dimensions after resize
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo !== false) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            $pathInfo = pathinfo($imagePath);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            $webpFullPath = $this->basePath . '/' . ltrim($webpPath, '/');

            // Check if WebP already exists
            if (file_exists($webpFullPath)) {
                return str_replace(BASE_PATH, BASE_URL, $webpPath);
            }

            // Increase memory limit for image processing
            $originalMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '512M');

            // Read and convert to WebP
            $image = $this->manager->read($fullPath);
            $image->toWebp($quality)->save($webpFullPath);

            // Free memory immediately
            unset($image);

            // Restore original memory limit
            ini_set('memory_limit', $originalMemoryLimit);

            return str_replace(BASE_PATH, BASE_URL, $webpPath);
        } catch (\Exception $exception) {
            // Restore original memory limit on error
            if (isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }

            if (function_exists('log_message')) {
                log_message('error', 'WebP conversion failed', [
                    'path' => $imagePath,
                    'error' => $exception->getMessage()
                ]);
            }

            return null;
        }
    }
}
