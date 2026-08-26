<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchRequest;
use App\Models\Cause;
use App\Models\Initiative;
use App\Models\Event;
use App\Models\Story;
use App\Models\ImpactActivity;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function search(SearchRequest $request): JsonResponse
    {
        $q = $request->validated('q');

        $results = [
            'causes' => Cause::where('title', 'LIKE', "%{$q}%")
                ->select('id', 'title', 'slug', 'status')
                ->limit(5)
                ->get(),

            'initiatives' => Initiative::where('title', 'LIKE', "%{$q}%")
                ->select('id', 'title', 'slug', 'status')
                ->limit(5)
                ->get(),

            'events' => Event::where('title', 'LIKE', "%{$q}%")
                ->select('id', 'title', 'slug', 'status')
                ->limit(5)
                ->get(),

            'stories' => Story::where('title', 'LIKE', "%{$q}%")
                ->select('id', 'title', 'slug', 'status')
                ->limit(5)
                ->get(),

            'impact' => ImpactActivity::where('title', 'LIKE', "%{$q}%")
                ->select('id', 'title', 'slug', 'status')
                ->limit(5)
                ->get(),

            'contacts' => ContactSubmission::where('firstname', 'LIKE', "%{$q}%")
                ->orWhere('lastname', 'LIKE', "%{$q}%")
                ->orWhere('email', 'LIKE', "%{$q}%")
                ->select('id', 'firstname', 'lastname', 'email', 'status')
                ->limit(5)
                ->get(),

            'subscribers' => NewsletterSubscriber::where('email', 'LIKE', "%{$q}%")
                ->select('id', 'email', 'status')
                ->limit(5)
                ->get(),
        ];

        return response()->json($results);
    }
}
