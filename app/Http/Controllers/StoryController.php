<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\SiteSetting;
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

        $hero = SiteSetting::grouped([
            'hero_stories_title' => 'Our Stories',
            'hero_stories_subtitle' => '',
            'hero_stories_image' => '',
            'hero_stories_button_text' => '',
            'hero_stories_button_url' => '',
        ]);

        return inertia('Stories', compact('stories', 'hero'));
    }

    public function show(Story $story)
    {
        return inertia('StoryShow', compact('story'));
    }
}
