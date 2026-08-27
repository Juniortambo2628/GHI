<?php

namespace App\Http\Controllers;

use App\Models\ImpactActivity;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

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

        $hero = SiteSetting::grouped([
            'hero_impact_title' => 'Our Impact',
            'hero_impact_subtitle' => '',
            'hero_impact_image' => '',
            'hero_impact_button_text' => '',
            'hero_impact_button_url' => '',
        ]);

        return inertia('Impact', compact('impactActivities', 'hero'));
    }

    public function show(ImpactActivity $impactActivity)
    {
        return inertia('ImpactShow', compact('impactActivity'));
    }
}
