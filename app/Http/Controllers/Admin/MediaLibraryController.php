<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaAsset::query()
            ->search($request->input('search'))
            ->ofGroup($request->input('group'));

        if ($request->input('type') === 'images') {
            $query->images();
        } elseif ($request->input('type') === 'videos') {
            $query->where('mime_type', 'like', 'video/%');
        }

        $media = $query->latest()->paginate(24)->withQueryString();

        return inertia('Admin/Media/Index', [
            'media' => $media,
            'filters' => $request->only('search', 'type', 'group'),
            'groups' => MediaAsset::distinct()->whereNotNull('group')->pluck('group')->filter()->values(),
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $file = $request->file('file');
        $filename = \Illuminate\Support\Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('images', $filename, 'public');

        $asset = MediaAsset::create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'group' => $request->input('group'),
        ]);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'asset' => $asset,
        ]);
    }

    public function update(Request $request, MediaAsset $mediaAsset): RedirectResponse
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'group' => 'nullable|string|max:50',
        ]);

        $mediaAsset->update($validated);

        return back()->with('success', 'Media updated.');
    }

    public function destroy(MediaAsset $mediaAsset): RedirectResponse
    {
        $disk = Storage::disk('public');
        if ($disk->exists($mediaAsset->path)) {
            $disk->delete($mediaAsset->path);
        }
        $mediaAsset->delete();

        return back()->with('success', 'Media deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $ids = $request->input('ids');
        $assets = MediaAsset::whereIn('id', $ids)->get();
        $disk = Storage::disk('public');

        foreach ($assets as $asset) {
            if ($disk->exists($asset->path)) {
                $disk->delete($asset->path);
            }
            $asset->delete();
        }

        return back()->with('success', count($assets).' file(s) deleted.');
    }
}
