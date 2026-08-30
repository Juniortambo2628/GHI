<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackfillMediaAssets extends Command
{
    protected $signature = 'media:backfill';

    protected $description = 'Scan public storage and create MediaAsset records for existing images';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $directories = ['images', 'Banners-and-portraits', 'Logo', 'Developer'];
        $count = 0;

        foreach ($directories as $dir) {
            if (! $disk->exists($dir)) {
                continue;
            }

            $files = $disk->allFiles($dir);

            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (! in_array($ext, $imageTypes)) {
                    continue;
                }

                $exists = MediaAsset::where('path', $file)->exists();

                if ($exists) {
                    continue;
                }

                $fullPath = storage_path("app/public/{$file}");

                $width = null;
                $height = null;
                $fileSize = file_exists($fullPath) ? filesize($fullPath) : null;

                if (function_exists('getimagesize') && file_exists($fullPath)) {
                    $size = @getimagesize($fullPath);
                    if ($size) {
                        $width = $size[0];
                        $height = $size[1];
                    }
                }

                $mimeMap = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                ];

                MediaAsset::create([
                    'path' => $file,
                    'original_name' => basename($file),
                    'file_size' => $fileSize,
                    'mime_type' => $mimeMap[$ext] ?? 'image/'.$ext,
                    'width' => $width,
                    'height' => $height,
                ]);

                $count++;
            }
        }

        $this->info("Backfilled {$count} media assets.");

        return Command::SUCCESS;
    }
}
