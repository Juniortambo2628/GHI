<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\OptimizeImageJob;
use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    private array $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    private array $videoTypes = ['mp4', 'webm', 'mov', 'avi'];

    private array $documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];

    private int $maxFileSize = 20971520;

    public function store(Request $request)
    {
        return $this->uploadFile($request, 'files');
    }

    public function image(Request $request)
    {
        return $this->uploadFile($request, 'images', $this->imageTypes, $this->maxFileSize);
    }

    public function document(Request $request)
    {
        return $this->uploadFile($request, 'documents', $this->documentTypes, $this->maxFileSize);
    }

    public function media(Request $request)
    {
        $allowedTypes = array_merge($this->imageTypes, $this->videoTypes);

        return $this->uploadFile($request, 'images', $allowedTypes, $this->maxFileSize);
    }

    public function batch(Request $request): Response
    {
        $request->validate([
            'files' => 'required|array|max:50',
            'files.*' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp',
            'group' => 'nullable|string|max:50',
        ]);

        $group = $request->input('group');
        $disk = Storage::disk('public');
        $results = [];

        foreach ($request->file('files') as $file) {
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $tempPath = 'temp/'.$filename;
            $file->storeAs('temp', $filename, 'public');

            OptimizeImageJob::dispatch(
                tempPath: $tempPath,
                originalName: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
                group: $group,
            );

            $results[] = [
                'temp_path' => $tempPath,
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return response()->json([
            'success' => true,
            'queued' => count($results),
            'message' => count($results).' image(s) queued for optimization.',
        ]);
    }

    private function getUploadedFile(Request $request): ?UploadedFile
    {
        foreach ($request->allFiles() as $file) {
            return $file;
        }

        return null;
    }

    private function uploadFile(Request $request, string $subdirectory, ?array $allowedTypes = null, ?int $maxSize = null): Response
    {
        $allowedTypes ??= array_merge($this->imageTypes, $this->documentTypes);
        $maxSize ??= $this->maxFileSize;

        $file = $this->getUploadedFile($request);

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded. Expected a file in the request.',
            ], 422);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'File type not allowed: '.$extension,
            ], 422);
        }

        if ($file->getSize() > $maxSize) {
            return response()->json([
                'success' => false,
                'message' => 'File is too large. Maximum size is '.($maxSize / 1048576).'MB.',
            ], 422);
        }

        $filename = Str::uuid().'.'.$extension;
        $isImage = in_array($extension, $this->imageTypes);

        if ($isImage && $subdirectory === 'images') {
            $path = $this->storeOptimizedImage($file, $filename);
        } else {
            $path = $file->storeAs($subdirectory, $filename, 'public');
        }

        if ($request->expectsJson()) {
            $assetData = [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'size' => $file->getSize(),
            ];

            if ($isImage) {
                $asset = MediaAsset::create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'group' => $request->input('group'),
                ]);

                $assetData['asset_id'] = $asset->id;
            }

            return response()->json($assetData);
        }

        return response('storage/'.$path, 200)->header('Content-Type', 'text/plain');
    }

    private function storeOptimizedImage($file, string $filename): string
    {
        $previousLimit = ini_get('memory_limit');
        @ini_set('memory_limit', '512M');

        try {
            $image = (new ImageManager(new Driver))->read($file->getRealPath());
            $image->scaleDown(width: 1800, height: 1200);
            $extension = strtolower($file->getClientOriginalExtension());
            $encoded = match ($extension) {
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                'webp' => $image->toWebp(82),
                default => $image->toJpeg(82),
            };
            $savedExtension = $extension === 'jpeg' ? 'jpg' : $extension;
            $path = 'images/'.pathinfo($filename, PATHINFO_FILENAME).'.'.$savedExtension;
            Storage::disk('public')->put($path, (string) $encoded);

            return $path;
        } catch (\Exception $e) {
            $path = 'images/'.$filename;
            $file->storeAs('images', $filename, 'public');

            return $path;
        } finally {
            @ini_set('memory_limit', $previousLimit);
        }
    }
}
