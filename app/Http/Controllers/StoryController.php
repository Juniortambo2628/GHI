<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Story::published();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $stories = $query->latest()->paginate(12);

        return inertia('Stories', compact('stories'));
    }

    public function show(Story $story)
    {
        return inertia('StoryShow', compact('story'));
    }
}
