<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use App\Models\Event;
use App\Models\Story;
use App\Models\ImpactActivity;
use App\Models\Cause;
use Inertia\Inertia;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $initiatives = Initiative::published()->latest()->limit(3)->get();
        $events = Event::upcoming()->limit(3)->get();
        $stories = Story::published()->latest()->limit(3)->get();
        $impactStories = ImpactActivity::published()->limit(3)->get();
        $recentEvents = Event::published()->latest()->limit(5)->get();
        $causes = Cause::published()->orderBy('display_order')->get();

        // Enrich initiatives with event counts
        $initiativeIds = $initiatives->pluck('id')->toArray();
        $eventCounts = Event::whereIn('initiative_id', $initiativeIds)
            ->where('status', 'published')
            ->selectRaw('initiative_id, COUNT(*) as total')
            ->groupBy('initiative_id')
            ->pluck('total', 'initiative_id')
            ->toArray();

        $enrichedInitiatives = $initiatives->map(function ($initiative) use ($eventCounts) {
            $obj = $initiative->toArray();
            $obj['event_count'] = $eventCounts[$initiative->id] ?? 0;
            $obj['objective'] = config('site.category_to_objective.' . $initiative->category, 'Community Development');
            return $obj;
        });

        // Enrich events with initiative names
        $allInitiatives = Initiative::published()->pluck('title', 'id')->toArray();
        $enrichedEvents = $events->map(function ($event) use ($allInitiatives) {
            $obj = $event->toArray();
            $obj['initiative'] = $allInitiatives[$event->initiative_id] ?? 'General';
            $obj['date'] = $event->event_date;
            return $obj;
        });

        // Build gallery: collect all images from recent published events
        $galleryImages = collect();
        $enrichedActivities = $recentEvents->map(function ($event) use ($allInitiatives, $galleryImages) {
            $obj = $event->toArray();
            $obj['initiative'] = $allInitiatives[$event->initiative_id] ?? 'N/A';
            $initiative = Initiative::find($event->initiative_id);
            $obj['objective'] = $initiative ? config('site.category_to_objective.' . $initiative->category, 'Community Development') : 'Community Development';
            $obj['location'] = $event->location;
            $obj['gallery_images'] = $event->images()->orderBy('sort_order')->get()->map(fn($img) => ['id' => $img->id, 'path' => $img->path]);
            return $obj;
        });

        // Flatten all event gallery images for the gallery section
        $allGalleryImages = $recentEvents->flatMap(function ($event) {
            return $event->images()->orderBy('sort_order')->get()->map(fn($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'event_title' => $event->title,
                'initiative' => Initiative::find($event->initiative_id)?->title ?? 'N/A',
                'location' => $event->location,
                'event_date' => $event->event_date,
            ]);
        })->take(12);

        // Enrich stories
        $enrichedStories = $stories->map(function ($story) {
            $obj = $story->toArray();
            $obj['objective'] = config('site.category_to_objective.' . $story->category, 'Community Development');
            $obj['slug'] = $story->slug ?? 'story-' . $story->id;
            return $obj;
        });

        // Counters
        $stats = [
            'initiatives' => Initiative::published()->count(),
            'events' => Event::published()->count(),
            'communities' => ImpactActivity::published()->distinct('event_id')->count('event_id'),
            'lives_impacted' => ImpactActivity::published()->sum('people_affected'),
        ];

        // Random quote
        $quotes = config('site.quotes', []);
        $randomQuote = $quotes[array_rand($quotes)] ?? ['quote' => '', 'author' => ''];
        $cmsSettings = SiteSetting::grouped(array_merge(
            ['homepage_hero' => '[]', 'homepage_sections' => '{}', 'homepage_section_order' => '[]'],
            collect($this->homeSettingsDefaults())->only(array_keys($this->homeSettingsDefaults()))->toArray()
        ));

        return inertia('Home', [
            'initiatives' => $enrichedInitiatives,
            'events' => $enrichedEvents,
            'stories' => $enrichedStories,
            'impactStories' => $impactStories,
            'recentActivities' => $enrichedActivities,
            'galleryImages' => $allGalleryImages->values(),
            'causes' => $causes,
            'stats' => $stats,
            'counters' => $stats,
            'randomQuote' => $randomQuote,
            'objectives' => config('site.objectives', []),
            'coreValues' => config('site.core_values', []),
            'categoryToObjective' => config('site.category_to_objective', []),
            'heroSlides' => json_decode($cmsSettings['homepage_hero'], true) ?: [],
            'sectionVisibility' => json_decode($cmsSettings['homepage_sections'], true) ?: [],
            'sectionOrder' => json_decode($cmsSettings['homepage_section_order'], true) ?: [],
            'settings' => $cmsSettings,
        ]);
    }

    private function homeSettingsDefaults(): array
    {
        return [
            'home_about_subtitle' => 'About Us',
            'home_about_title' => 'Bridging Global Compassion with Local Action',
            'home_about_description' => '<p>At Global Harmony Initiative Inc., we believe that harmony begins when humanity comes together — across borders, beliefs, and backgrounds — to create lasting change. From classrooms in Kenya to community wells in Zanzibar, we connect people and resources to nurture sustainable growth and empower local leaders to build a brighter tomorrow.</p>',
            'home_about_who_we_are_heading' => 'Who We Are',
            'home_about_who_we_are_text_1' => 'Global Harmony Initiative Inc. (GHI) is a U.S.-registered 501(c)(3) nonprofit organization working hand in hand with communities across East Africa to alleviate poverty and promote sustainable development.',
            'home_about_who_we_are_text_2' => 'We connect compassion with action — linking donors, volunteers, and local leaders to transform lives through education, health, and economic empowerment.',
            'home_about_who_we_are_text_3' => 'Founded on the belief that every person deserves opportunity and dignity, GHI stands as a bridge between global goodwill and community-driven impact.',
            'home_about_button_text' => 'Read More',
            'home_foundation_subtitle' => 'About Us',
            'home_foundation_title' => 'Our Foundation',
            'home_foundation_mission_heading' => 'Our Mission',
            'home_foundation_mission_text' => 'To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.',
            'home_foundation_vision_heading' => 'Our Vision',
            'home_foundation_vision_text' => 'A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.',
            'home_foundation_values_heading' => 'Our Values',
            'home_objectives_subtitle' => 'What we do',
            'home_objectives_title' => 'Our Core Objectives',
            'home_counters_subtitle' => 'Our Impact',
            'home_counters_title' => 'Making a Measurable Difference',
            'home_counters_description' => "Through our collective efforts, we've made significant progress in empowering communities across East Africa. Every number represents lives touched and futures transformed.",
            'home_counters_button_text' => 'Join With Us',
            'home_initiatives_subtitle' => 'Our Initiatives',
            'home_initiatives_title' => 'Strategic Initiatives for Lasting Impact',
            'home_initiatives_description' => 'We work across multiple areas to address critical needs and create lasting positive change in East African communities.',
            'home_initiatives_empty_message' => 'No initiatives configured.',
            'home_initiatives_empty_button_text' => '',
            'home_initiatives_all_button_text' => 'See All Initiatives',
            'home_events_subtitle' => 'Events & Activities',
            'home_events_title' => 'Each step brings us closer to our vision of a brighter future for All. Join us in making a difference!',
            'home_events_empty_message' => 'No upcoming events at this time. Please check back later.',
            'home_stories_subtitle' => 'Our Impact',
            'home_stories_title' => 'Stories of Transformation and Positive Outcomes',
            'home_stories_description' => 'Discover how our activities are creating lasting change in communities across East Africa. Read, engage, and share these inspiring stories.',
            'home_stories_empty_message' => 'No stories available at this time. Please check back later.',
            'home_gallery_subtitle' => 'Our work',
            'home_gallery_title' => 'Recent Activities Gallery',
            'home_gallery_description' => 'See the impact of our programs through images from our most recent activities across East Africa.',
            'home_volunteer_subtitle' => 'Become a Volunteer?',
            'home_volunteer_title' => 'Join your hand with us for a better life and beautiful future.',
            'home_volunteer_description' => "We welcome dedicated individuals who share our passion for creating positive change. As a volunteer with Global Harmony Initiative, you'll have the opportunity to make a real difference in the lives of communities across East Africa.",
            'home_volunteer_bullet_1' => 'We are friendly to each other.',
            'home_volunteer_bullet_2' => 'If you join with us, we will give you free training.',
            'home_volunteer_bullet_3' => "It's an opportunity to help communities in need.",
            'home_volunteer_bullet_4' => 'No goal requirements.',
            'home_volunteer_bullet_5' => "Joining is totally free. We don't need any money from you.",
            'home_volunteer_button_text' => 'Join With Us',
        ];
    }
}
