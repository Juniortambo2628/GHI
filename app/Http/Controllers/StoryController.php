<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Story;
use Illuminate\Http\Request;

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
        $story->load(['event' => function ($q) {
            $q->with(['initiative.causes', 'impactActivities']);
        }]);

        return inertia('StoryShow', [
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'slug' => $story->slug,
                'content' => $story->content,
                'author' => $story->author,
                'image' => $story->image,
                'featured_image' => $story->featured_image,
                'category' => $story->category,
                'status' => $story->status,
                'created_at' => $story->created_at,
                'event' => $story->event ? [
                    'id' => $story->event->id,
                    'title' => $story->event->title,
                    'slug' => $story->event->slug,
                    'event_date' => $story->event->event_date,
                    'location' => $story->event->location,
                    'image' => $story->event->image,
                    'status' => $story->event->status,
                    'initiative' => $story->event->initiative ? [
                        'id' => $story->event->initiative->id,
                        'title' => $story->event->initiative->title,
                        'slug' => $story->event->initiative->slug,
                        'causes' => $story->event->initiative->causes->map(fn ($c) => [
                            'id' => $c->id,
                            'title' => $c->title,
                            'slug' => $c->slug,
                        ]),
                    ] : null,
                    'impact_activities' => $story->event->impactActivities->map(fn ($a) => [
                        'id' => $a->id,
                        'title' => $a->title,
                        'slug' => $a->slug,
                        'people_affected' => $a->people_affected,
                        'metric_type' => $a->metric_type,
                    ]),
                ] : null,
            ],
        ]);
    }
}
