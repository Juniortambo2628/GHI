<?php

namespace App\Http\Controllers;

use App\Models\Cause;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\Story;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        $results = collect();

        if ($term !== '') {
            $like = "%{$term}%";
            $sources = [
                ['type' => 'Cause', 'items' => Cause::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('description', 'like', $like))->limit(10)->get()],
                ['type' => 'Initiative', 'items' => Initiative::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('description', 'like', $like))->limit(10)->get()],
                ['type' => 'Event', 'items' => Event::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('description', 'like', $like))->limit(10)->get()],
                ['type' => 'Impact', 'items' => ImpactActivity::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('description', 'like', $like))->limit(10)->get()],
                ['type' => 'Story', 'items' => Story::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('content', 'like', $like))->limit(10)->get()],
            ];

            foreach ($sources as $source) {
                $results = $results->concat($source['items']->map(fn ($item) => [
                    'type' => $source['type'],
                    'title' => $item->title,
                    'description' => $item->description ?? $item->content,
                    'url' => '/'.strtolower($source['type']).'s/'.$item->slug,
                ]));
            }
        }

        return inertia('Search', ['term' => $term, 'results' => $results->values()]);
    }
}
