<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormDraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormDraftController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_key' => 'required|string',
            'data' => 'required|string',
        ]);

        FormDraft::updateOrCreate(
            ['user_id' => $request->user()->id, 'form_key' => $validated['form_key']],
            ['data' => $validated['data']]
        );

        return response()->json(['message' => 'Draft saved successfully.']);
    }

    public function load(Request $request): JsonResponse
    {
        $request->validate([
            'form_key' => 'required|string',
        ]);

        $draft = FormDraft::where('user_id', $request->user()->id)
            ->where('form_key', $request->input('form_key'))
            ->first();

        return response()->json([
            'data' => $draft?->data,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'form_key' => 'required|string',
        ]);

        FormDraft::where('user_id', $request->user()->id)
            ->where('form_key', $request->input('form_key'))
            ->delete();

        return response()->json(['message' => 'Draft deleted successfully.']);
    }
}
