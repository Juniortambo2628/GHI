<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use App\Models\SiteSetting;
use Inertia\Inertia;

class GetInvolvedController extends Controller
{
    public function index()
    {
        $initiatives = Initiative::published()->orderBy('title')->get(['id', 'title']);

        $hero = SiteSetting::grouped([
            'hero_get_involved_title' => 'Get Involved',
            'hero_get_involved_subtitle' => 'Join us in making a difference',
            'hero_get_involved_image' => '',
        ]);

        return inertia('GetInvolved', compact('initiatives', 'hero'));
    }

    public function store(\App\Http\Requests\StoreGetInvolvedRequest $request)
    {
        \App\Models\GetInvolvedSubmission::create($request->validated());

        return redirect()->route('get-involved')
            ->with('success', 'Thank you for your interest! We will get back to you soon.');
    }
}
