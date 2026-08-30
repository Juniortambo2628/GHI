<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaPickerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MediaAsset::images()->latest();

        $query->search($request->input('search'));
        $query->ofGroup($request->input('group'));

        $assets = $query->paginate($request->input('per_page', 24));

        return response()->json($assets);
    }

    public function update(Request $request, MediaAsset $mediaAsset): JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'group' => 'nullable|string|max:50',
        ]);

        $mediaAsset->update($validated);

        return response()->json(['success' => true, 'asset' => $mediaAsset]);
    }

    public function destroy(MediaAsset $mediaAsset): JsonResponse
    {
        $mediaAsset->delete();

        return response()->json(['success' => true]);
    }
}
