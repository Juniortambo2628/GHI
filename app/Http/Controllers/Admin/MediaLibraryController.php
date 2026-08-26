<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteMediaRequest;
use App\Http\Requests\Admin\RenameMediaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        $files = collect($disk->allFiles())
            ->filter(fn ($path) => ! $request->type || str_starts_with($path, $request->type . '/'))
            ->filter(fn ($path) => ! $request->search || str_contains(strtolower($path), strtolower($request->search)))
            ->sortDesc()
            ->map(fn ($path) => [
                'path' => $path,
                'url' => $disk->url($path),
                'name' => basename($path),
                'size' => $disk->size($path),
                'last_modified' => $disk->lastModified($path),
                'type' => explode('/', $path, 2)[0],
            ])->values();

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 24;
        $items = $files->forPage(($page - 1) * $perPage, $perPage)->values();

        return inertia('Admin/Media/Index', [
            'media' => [
                'data' => $items,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($files->count() / $perPage)),
                'path' => '/admin/media',
                'prev_page_url' => $page > 1 ? '/admin/media?page=' . ($page - 1) : null,
                'next_page_url' => $page < ceil($files->count() / $perPage) ? '/admin/media?page=' . ($page + 1) : null,
            ],
            'filters' => $request->only('type', 'search'),
        ]);
    }

    public function download(string $path): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $path = (string) $request->input('path');
        abort_unless($path && ! str_contains($path, '..') && Storage::disk('public')->exists($path), 404);
        Storage::disk('public')->delete($path);

        return back()->with('success', 'Media deleted.');
    }

    public function bulkDestroy(BulkDeleteMediaRequest $request): RedirectResponse
    {
        $paths = $request->validated('paths');
        $disk = Storage::disk('public');
        $deleted = 0;

        foreach ($paths as $path) {
            if (! str_contains($path, '..') && $disk->exists($path)) {
                $disk->delete($path);
                $deleted++;
            }
        }

        return back()->with('success', "{$deleted} file(s) deleted.");
    }

    public function rename(RenameMediaRequest $request): RedirectResponse
    {
        $path = $request->validated('path');
        $newName = $request->validated('new_name');
        $disk = Storage::disk('public');

        abort_unless(! str_contains($path, '..') && $disk->exists($path), 404);

        $dir = dirname($path);
        $newPath = $dir === '.' ? $newName : $dir . '/' . $newName;

        if ($newPath !== $path && $disk->exists($newPath)) {
            return back()->withErrors(['new_name' => 'A file with that name already exists.']);
        }

        $disk->move($path, $newPath);

        return back()->with('success', 'File renamed.');
    }
}
