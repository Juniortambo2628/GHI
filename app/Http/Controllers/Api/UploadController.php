<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    private array $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private array $documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
    private int $maxFileSize = 10485760; // 10MB

    public function store(Request $request)
    {
        return $this->uploadFile($request, 'files');
    }

    public function image(Request $request)
    {
        return $this->uploadFile($request, 'images', $this->imageTypes, 5242880);
    }

    public function document(Request $request)
    {
        return $this->uploadFile($request, 'documents', $this->documentTypes, $this->maxFileSize);
    }

    private function uploadFile(Request $request, string $subdirectory, ?array $allowedTypes = null, ?int $maxSize = null): \Symfony\Component\HttpFoundation\Response
    {
        $allowedTypes ??= array_merge($this->imageTypes, $this->documentTypes);
        $maxSize ??= $this->maxFileSize;

        $request->validate([
            'file' => 'required|file|max:' . ($maxSize / 1024),
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedTypes)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File type not allowed: ' . $extension,
                ], 422);
            }
            return response('File type not allowed', 422);
        }

        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($subdirectory, $filename, 'public');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'path' => 'storage/' . $path,
                'size' => $file->getSize(),
            ]);
        }

        // FilePond expects plain text server ID on success
        return response('storage/' . $path, 200)->header('Content-Type', 'text/plain');
    }
}
