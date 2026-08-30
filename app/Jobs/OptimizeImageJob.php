<?php

namespace App\Jobs;

use App\Models\MediaAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public int $memory = 512;

    public function __construct(
        public string $tempPath,
        public string $originalName,
        public string $mimeType,
        public ?string $group = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {
        $this->onQueue('images');
    }

    public function handle(): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($this->tempPath)) {
            return null;
        }

        $contents = $disk->get($this->tempPath);
        $tempFile = tempnam(sys_get_temp_dir(), 'ghi_img_');
        file_put_contents($tempFile, $contents);
        $disk->delete($this->tempPath);

        $previousLimit = ini_get('memory_limit');
        @ini_set('memory_limit', '512M');

        try {
            $image = (new ImageManager(new Driver))->read($tempFile);
            $image->scaleDown(width: 1800, height: 1200);

            $ext = strtolower(pathinfo($this->originalName, PATHINFO_EXTENSION));
            $encoded = match ($ext) {
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                'webp' => $image->toWebp(82),
                default => $image->toJpeg(82),
            };

            $savedExt = $ext === 'jpeg' ? 'jpg' : $ext;
            $filename = pathinfo($this->originalName, PATHINFO_FILENAME).'_'.time().'.'.$savedExt;
            $path = 'images/'.$filename;

            $disk->put($path, (string) $encoded);

            $asset = MediaAsset::updateOrCreate(
                ['path' => $path],
                [
                    'original_name' => $this->originalName,
                    'file_size' => $disk->size($path),
                    'mime_type' => $disk->mimeType($path),
                    'group' => $this->group,
                    'width' => $image->width(),
                    'height' => $image->height(),
                ]
            );

            return $path;
        } catch (\Exception $e) {
            $path = 'images/'.pathinfo($this->originalName, PATHINFO_FILENAME).'_'.time().'.'.pathinfo($this->originalName, PATHINFO_EXTENSION);
            $disk->put($path, $contents);

            MediaAsset::create([
                'path' => $path,
                'original_name' => $this->originalName,
                'file_size' => strlen($contents),
                'mime_type' => $this->mimeType,
                'group' => $this->group,
            ]);

            return $path;
        } finally {
            @unlink($tempFile);
            @ini_set('memory_limit', $previousLimit);
        }
    }
}
