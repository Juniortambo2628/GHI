<?php

/**
 * File Service using League Flysystem
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;

class FileService
{
    private static ?Filesystem $instance = null;

    private static string $basePath;

    /**
     * Get filesystem instance (Singleton)
     */
    public static function getInstance(): Filesystem
    {
        if (!self::$instance instanceof \League\Flysystem\Filesystem) {
            self::$instance = self::createFilesystem();
        }

        return self::$instance;
    }

    /**
     * Create and configure filesystem
     */
    private static function createFilesystem(): Filesystem
    {
        self::$basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $adapter = new LocalFilesystemAdapter(self::$basePath);

        return new Filesystem($adapter);
    }

    /**
     * Read file contents
     */
    public static function read(string $path): string
    {
        try {
            return self::getInstance()->read($path);
        } catch (UnableToReadFile $unableToReadFile) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to read file', ['path' => $path, 'error' => $unableToReadFile->getMessage()]);
            }

            throw $unableToReadFile;
        }
    }

    /**
     * Write file contents
     */
    public static function write(string $path, string $contents): bool
    {
        try {
            self::getInstance()->write($path, $contents);
            return true;
        } catch (UnableToWriteFile $unableToWriteFile) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to write file', ['path' => $path, 'error' => $unableToWriteFile->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Check if file exists
     */
    public static function exists(string $path): bool
    {
        return self::getInstance()->fileExists($path);
    }

    /**
     * Delete file
     */
    public static function delete(string $path): bool
    {
        try {
            self::getInstance()->delete($path);
            return true;
        } catch (UnableToDeleteFile $unableToDeleteFile) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to delete file', ['path' => $path, 'error' => $unableToDeleteFile->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Create directory
     */
    public static function createDirectory(string $path): bool
    {
        try {
            self::getInstance()->createDirectory($path);
            return true;
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to create directory', ['path' => $path, 'error' => $filesystemException->getMessage()]);
            }

            return false;
        }
    }

    /**
     * List directory contents
     */
    public static function listContents(string $path, bool $recursive = false): array
    {
        try {
            return self::getInstance()->listContents($path, $recursive)->toArray();
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to list directory', ['path' => $path, 'error' => $filesystemException->getMessage()]);
            }

            return [];
        }
    }

    /**
     * Get file size
     */
    public static function getSize(string $path): int
    {
        try {
            return self::getInstance()->fileSize($path);
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to get file size', ['path' => $path, 'error' => $filesystemException->getMessage()]);
            }

            return 0;
        }
    }

    /**
     * Get file MIME type
     */
    public static function getMimeType(string $path): string
    {
        try {
            return self::getInstance()->mimeType($path);
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to get MIME type', ['path' => $path, 'error' => $filesystemException->getMessage()]);
            }

            return 'application/octet-stream';
        }
    }

    /**
     * Copy file
     */
    public static function copy(string $source, string $destination): bool
    {
        try {
            self::getInstance()->copy($source, $destination);
            return true;
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to copy file', [
                    'source' => $source,
                    'destination' => $destination,
                    'error' => $filesystemException->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Move file
     */
    public static function move(string $source, string $destination): bool
    {
        try {
            self::getInstance()->move($source, $destination);
            return true;
        } catch (FilesystemException $filesystemException) {
            if (function_exists('log_message')) {
                log_message('error', 'Unable to move file', [
                    'source' => $source,
                    'destination' => $destination,
                    'error' => $filesystemException->getMessage()
                ]);
            }

            return false;
        }
    }

    /**
     * Upload file (from $_FILES)
     */
    public static function upload(array $file, string $destinationPath, array $allowedTypes = []): array
    {
        $result = [
            'success' => false,
            'filename' => null,
            'error' => null,
        ];

        // Validate file upload
        if (! isset($file['tmp_name']) || ! is_uploaded_file($file['tmp_name'])) {
            $result['error'] = 'Invalid file upload';
            return $result;
        }

        // Check file size
        $maxSize = defined('UPLOADS_MAX_SIZE') ? UPLOADS_MAX_SIZE : 10485760; // 10MB default
        if ($file['size'] > $maxSize) {
            $result['error'] = 'File size exceeds maximum allowed size';
            return $result;
        }

        // Check file type
        if ($allowedTypes !== []) {
            $fileExtension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = array_map('strtolower', $allowedTypes);
            if (! in_array($fileExtension, $allowedExtensions, true)) {
                $result['error'] = 'File type not allowed';
                return $result;
            }
        }

        // Generate unique filename
        $originalName = pathinfo((string) $file['name'], PATHINFO_FILENAME);
        $extension = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
        $filename = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;
        $fullPath = $destinationPath . '/' . $filename;

        // Ensure destination directory exists
        if (! self::exists($destinationPath)) {
            self::createDirectory($destinationPath);
        }

        // Read uploaded file
        $contents = file_get_contents($file['tmp_name']);

        // Write to destination
        if (self::write($fullPath, $contents)) {
            $result['success'] = true;
            $result['filename'] = $filename;
            $result['path'] = $fullPath;
            $result['size'] = $file['size'];
            $result['mime_type'] = $file['type'];

            if (function_exists('log_message')) {
                log_message('info', 'File uploaded successfully', [
                    'filename' => $filename,
                    'path' => $fullPath,
                    'size' => $file['size'],
                ]);
            }
        } else {
            $result['error'] = 'Failed to save file';
        }

        return $result;
    }
}
