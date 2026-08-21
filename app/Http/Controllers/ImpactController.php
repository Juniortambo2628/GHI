<?php

namespace App\Http\Controllers;

use App\Models\ImpactActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImpactController extends Controller
{
    public function index(Request $request)
    {
        $query = ImpactActivity::published();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->input('featured')) {
            $query->featured();
        }

        $impactActivities = $query->latest()->paginate(12);

        return inertia('Impact', compact('impactActivities'));
    }

    public function show(ImpactActivity $impactActivity)
    {
        return inertia('ImpactShow', compact('impactActivity'));
    }
}
