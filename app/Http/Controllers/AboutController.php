<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class AboutController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::grouped([
            'about_hero_title' => 'About Global Harmony Initiative',
            'about_hero_subtitle' => 'Bridging Global Compassion with Local Action',
            'about_hero_image' => '',
            'about_background_heading' => 'Background',
            'about_background_content' => '<p>Poverty and inequality continue to challenge the aspirations of millions of people in East Africa. Limited access to education, healthcare, and economic opportunity undermines human potential and slows sustainable growth. Yet, within these communities lies tremendous resilience, creativity, and capacity for transformation.</p><p>Global Harmony Initiative Inc. (GHI) was founded to bridge global compassion with local action. Based in the United States, GHI exists to connect individuals, organizations, and resources across continents — fostering harmony through humanitarian collaboration and community-driven development.</p><p>We believe that meaningful change happens when people work together beyond borders — empowering communities to lift themselves out of poverty with dignity and hope.</p>',
            'about_methodology_heading' => 'Methodology',
            'about_methodology_content' => '<p>Global Harmony Initiative Inc. will operate as a U.S.-registered 501(c)(3) public charity with a hybrid implementation model:</p>',
            'about_methodology_image' => '',
        ]);

        return inertia('About', [
            'settings' => $settings,
        ]);
    }
}
