<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private array $defaults = [
        'site_name' => 'Global Harmony Initiative',
        'site_tagline' => 'Bridging Global Compassion with Local Action',
        'site_description' => 'Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.',
        'site_logo' => '/Logo/Square-White-BG.png',
        'site_favicon' => '/Logo/Square-White-BG.png',
        'contact_email' => '',
        'contact_phone' => '',
        'facebook_url' => '',
        'instagram_url' => '',
        'twitter_url' => '',
        'linkedin_url' => '',
        'footer_text' => '',
        'homepage_hero' => '[]',
        'homepage_sections' => '{"about":true,"quote":true,"foundation":true,"objectives":true,"counters":true,"initiatives":true,"events":true,"stories":true,"gallery":true,"volunteer":true}',
        'homepage_section_order' => '["hero","about","quote","foundation","objectives","counters","initiatives","events","stories","gallery","volunteer"]',
        'hero_causes_title' => 'Our Causes',
        'hero_causes_subtitle' => '',
        'hero_causes_image' => '',
        'hero_causes_button_text' => '',
        'hero_causes_button_url' => '',
        'hero_initiatives_title' => 'Our Initiatives',
        'hero_initiatives_subtitle' => '',
        'hero_initiatives_image' => '',
        'hero_initiatives_button_text' => '',
        'hero_initiatives_button_url' => '',
        'hero_events_title' => 'Events & Activities',
        'hero_events_subtitle' => '',
        'hero_events_image' => '',
        'hero_events_button_text' => '',
        'hero_events_button_url' => '',
        'hero_impact_title' => 'Our Impact',
        'hero_impact_subtitle' => '',
        'hero_impact_image' => '',
        'hero_impact_button_text' => '',
        'hero_impact_button_url' => '',
        'hero_stories_title' => 'Our Stories',
        'hero_stories_subtitle' => '',
        'hero_stories_image' => '',
        'hero_stories_button_text' => '',
        'hero_stories_button_url' => '',
        'hero_contact_title' => 'Contact Us',
        'hero_contact_subtitle' => '',
        'hero_contact_image' => '',
        'hero_contact_button_text' => '',
        'hero_contact_button_url' => '',
        'hero_get_involved_title' => 'Get Involved',
        'hero_get_involved_subtitle' => 'Join us in making a difference',
        'hero_get_involved_image' => '',
        'about_hero_title' => 'About Global Harmony Initiative',
        'about_hero_subtitle' => 'Bridging Global Compassion with Local Action',
        'about_hero_image' => '',
        'about_background_heading' => 'Background',
        'about_background_content' => '<p>Poverty and inequality continue to challenge the aspirations of millions of people in East Africa. Limited access to education, healthcare, and economic opportunity undermines human potential and slows sustainable growth. Yet, within these communities lies tremendous resilience, creativity, and capacity for transformation.</p><p>Global Harmony Initiative Inc. (GHI) was founded to bridge global compassion with local action. Based in the United States, GHI exists to connect individuals, organizations, and resources across continents — fostering harmony through humanitarian collaboration and community-driven development.</p><p>We believe that meaningful change happens when people work together beyond borders — empowering communities to lift themselves out of poverty with dignity and hope.</p>',
        'about_methodology_heading' => 'Methodology',
        'about_methodology_content' => '<p>Global Harmony Initiative Inc. will operate as a U.S.-registered 501(c)(3) public charity with a hybrid implementation model:</p>',
        'about_methodology_image' => '',
        // Homepage About Section
        'home_about_subtitle' => 'About Us',
        'home_about_title' => 'Bridging Global Compassion with Local Action',
        'home_about_description' => '<p>At Global Harmony Initiative Inc., we believe that harmony begins when humanity comes together — across borders, beliefs, and backgrounds — to create lasting change. From classrooms in Kenya to community wells in Zanzibar, we connect people and resources to nurture sustainable growth and empower local leaders to build a brighter tomorrow.</p>',
        'home_about_who_we_are_heading' => 'Who We Are',
        'home_about_who_we_are_text_1' => 'Global Harmony Initiative Inc. (GHI) is a U.S.-registered 501(c)(3) nonprofit organization working hand in hand with communities across East Africa to alleviate poverty and promote sustainable development.',
        'home_about_who_we_are_text_2' => 'We connect compassion with action — linking donors, volunteers, and local leaders to transform lives through education, health, and economic empowerment.',
        'home_about_who_we_are_text_3' => 'Founded on the belief that every person deserves opportunity and dignity, GHI stands as a bridge between global goodwill and community-driven impact.',
        'home_about_button_text' => 'Read More',
        // Homepage Foundation Section
        'home_foundation_subtitle' => 'About Us',
        'home_foundation_title' => 'Our Foundation',
        'home_foundation_mission_heading' => 'Our Mission',
        'home_foundation_mission_text' => 'To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.',
        'home_foundation_vision_heading' => 'Our Vision',
        'home_foundation_vision_text' => 'A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.',
        'home_foundation_values_heading' => 'Our Values',
        // Homepage Objectives Section
        'home_objectives_subtitle' => 'What we do',
        'home_objectives_title' => 'Our Core Objectives',
        // Homepage Counters Section
        'home_counters_subtitle' => 'Our Impact',
        'home_counters_title' => 'Making a Measurable Difference',
        'home_counters_description' => 'Through our collective efforts, we\'ve made significant progress in empowering communities across East Africa. Every number represents lives touched and futures transformed.',
        'home_counters_button_text' => 'Join With Us',
        // Homepage Initiatives Section
        'home_initiatives_subtitle' => 'Our Initiatives',
        'home_initiatives_title' => 'Strategic Initiatives for Lasting Impact',
        'home_initiatives_description' => 'We work across multiple areas to address critical needs and create lasting positive change in East African communities.',
            'home_initiatives_empty_message' => 'No initiatives configured.',
            'home_initiatives_empty_button_text' => '',
        'home_initiatives_all_button_text' => 'See All Initiatives',
        // Homepage Events Section
        'home_events_subtitle' => 'Events & Activities',
        'home_events_title' => 'Each step brings us closer to our vision of a brighter future for All. Join us in making a difference!',
        'home_events_empty_message' => 'No upcoming events at this time. Please check back later.',
        // Homepage Stories Section
        'home_stories_subtitle' => 'Our Impact',
        'home_stories_title' => 'Stories of Transformation and Positive Outcomes',
        'home_stories_description' => 'Discover how our activities are creating lasting change in communities across East Africa. Read, engage, and share these inspiring stories.',
        'home_stories_empty_message' => 'No stories available at this time. Please check back later.',
        // Homepage Gallery Section
        'home_gallery_subtitle' => 'Our work',
        'home_gallery_title' => 'Recent Activities Gallery',
        'home_gallery_description' => 'See the impact of our programs through images from our most recent activities across East Africa.',
        // Homepage Volunteer Section
        'home_volunteer_subtitle' => 'Become a Volunteer?',
        'home_volunteer_title' => 'Join your hand with us for a better life and beautiful future.',
        'home_volunteer_description' => 'We welcome dedicated individuals who share our passion for creating positive change. As a volunteer with Global Harmony Initiative, you\'ll have the opportunity to make a real difference in the lives of communities across East Africa.',
        'home_volunteer_bullet_1' => 'We are friendly to each other.',
        'home_volunteer_bullet_2' => 'If you join with us, we will give you free training.',
        'home_volunteer_bullet_3' => 'It\'s an opportunity to help communities in need.',
        'home_volunteer_bullet_4' => 'No goal requirements.',
        'home_volunteer_bullet_5' => 'Joining is totally free. We don\'t need any money from you.',
        'home_volunteer_button_text' => 'Join With Us',
    ];

    public function edit()
    {
        return inertia('Admin/Settings/Index', ['settings' => SiteSetting::grouped($this->defaults)]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => str_contains($key, 'contact') ? 'contact' : 'general']);
        }

        return back()->with('success', 'Settings saved.');
    }
}
