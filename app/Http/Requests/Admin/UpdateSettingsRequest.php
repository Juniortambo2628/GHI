<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
        'hero_causes_title' => '',
        'hero_causes_subtitle' => '',
        'hero_causes_image' => '',
        'hero_causes_button_text' => '',
        'hero_causes_button_url' => '',
        'hero_initiatives_title' => '',
        'hero_initiatives_subtitle' => '',
        'hero_initiatives_image' => '',
        'hero_initiatives_button_text' => '',
        'hero_initiatives_button_url' => '',
        'hero_events_title' => '',
        'hero_events_subtitle' => '',
        'hero_events_image' => '',
        'hero_events_button_text' => '',
        'hero_events_button_url' => '',
        'hero_impact_title' => '',
        'hero_impact_subtitle' => '',
        'hero_impact_image' => '',
        'hero_impact_button_text' => '',
        'hero_impact_button_url' => '',
        'hero_stories_title' => '',
        'hero_stories_subtitle' => '',
        'hero_stories_image' => '',
        'hero_stories_button_text' => '',
        'hero_stories_button_url' => '',
        'hero_contact_title' => '',
        'hero_contact_subtitle' => '',
        'hero_contact_image' => '',
        'hero_contact_button_text' => '',
        'hero_contact_button_url' => '',
        'hero_get_involved_title' => '',
        'hero_get_involved_subtitle' => '',
        'hero_get_involved_image' => '',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_fill_keys(array_keys($this->defaults), ['nullable', 'string', 'max:5000']);
    }
}
