<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InitiativeController extends Controller
{
    public function index(Request $request)
    {
        $query = Initiative::published()->withCount(['events as event_count' => function ($q) {
            $q->where('status', 'published');
        }]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $initiatives = $query->latest()->paginate(12);

        $hero = SiteSetting::grouped([
            'hero_initiatives_title' => 'Our Initiatives',
            'hero_initiatives_subtitle' => '',
            'hero_initiatives_image' => '',
            'hero_initiatives_button_text' => '',
            'hero_initiatives_button_url' => '',
        ]);

        return inertia('Initiatives', compact('initiatives', 'hero'));
    }

    public function show(Initiative $initiative)
    {
        $events = $initiative->events()->upcoming()->get();

        return inertia('InitiativeShow', compact('initiative', 'events'));
    }
}
