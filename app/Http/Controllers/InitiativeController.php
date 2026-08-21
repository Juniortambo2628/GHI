<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InitiativeController extends Controller
{
    public function index(Request $request)
    {
        $query = Initiative::published();

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

        return inertia('Initiatives', compact('initiatives'));
    }

    public function show(Initiative $initiative)
    {
        $events = $initiative->events()->upcoming()->get();

        return inertia('InitiativeShow', compact('initiative', 'events'));
    }
}
