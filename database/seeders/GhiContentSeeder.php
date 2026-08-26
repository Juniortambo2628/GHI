<?php

namespace Database\Seeders;

use App\Models\Cause;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\Story;
use Illuminate\Database\Seeder;

class GhiContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCauses();
        $this->seedInitiatives();
        $this->seedEvents();
        $this->seedImpactActivities();
        $this->seedStories();
    }

    private function seedCauses(): void
    {
        $causes = [
            [
                'title' => 'Poverty Alleviation',
                'slug' => 'poverty-alleviation',
                'description' => 'Support initiatives that promote sustainable livelihoods, entrepreneurship, and financial inclusion.',
                'status' => 'published',
                'display_order' => 1,
            ],
            [
                'title' => 'Education Access',
                'slug' => 'education-access',
                'description' => 'Improve access to quality education and skills development for children and youth.',
                'status' => 'published',
                'display_order' => 2,
            ],
            [
                'title' => 'Health and Well-being',
                'slug' => 'health-and-well-being',
                'description' => 'Strengthen healthcare access, nutrition, and clean water initiatives in vulnerable communities.',
                'status' => 'published',
                'display_order' => 3,
            ],
            [
                'title' => 'Community Empowerment',
                'slug' => 'community-empowerment',
                'description' => 'Encourage local leadership, gender equity, and youth participation in development.',
                'status' => 'published',
                'display_order' => 4,
            ],
            [
                'title' => 'Global Partnership',
                'slug' => 'global-partnership',
                'description' => 'Mobilize individuals and institutions in the U.S. and beyond to support lasting change in Africa.',
                'status' => 'published',
                'display_order' => 5,
            ],
        ];

        $count = 0;
        foreach ($causes as $data) {
            Cause::firstOrCreate(['slug' => $data['slug']], $data);
            $count++;
        }

        $this->command->info("✓ Seeded {$count} causes");
    }

    private function seedInitiatives(): void
    {
        $initiatives = [
            [
                'title' => 'Elimisha Charity Campaign',
                'slug' => 'elimisha-charity-campaign',
                'category' => 'education',
                'description' => "A recurring outreach campaign bringing educational resources, school essentials, and warm meals to children in Nairobi's underserved neighborhoods, reinforcing their right to learn and dream beyond their circumstances.",
                'content' => "Elimisha Charity Campaign is Global Harmony Initiative's ongoing commitment to children living in Nairobi's informal settlements. Working alongside local grassroots charities, the campaign combines direct engagement — school supplies, books, and educational materials — with the simple dignity of a shared meal and a listened-to story. Each visit is built around the belief that a child's environment should never determine the size of their dreams. As the campaign grows, GHI intends to extend it to additional underserved neighborhoods across Nairobi, deepening partnerships with community-based organizations already doing trusted work on the ground.",
                'status' => 'published',
                'primary_cause' => 'education-access',
                'secondary_causes' => ['poverty-alleviation'],
            ],
            [
                'title' => 'Krisi na Ebenezer',
                'slug' => 'krisi-na-ebenezer',
                'category' => 'community-empowerment',
                'description' => "An annual festive-season initiative held in partnership with Ebenezer Life Centre, bringing gift-giving, shared meals, and joyful celebration to children in Ahero, Kisumu during the Christmas season.",
                'content' => "Krisi na Ebenezer (\"Christmas with Ebenezer\") is Global Harmony Initiative's annual Christmas tradition with the Ebenezer Life Centre community in Ahero, Kisumu. Each year the initiative brings together local partners, volunteers, and well-wishers for a day built around gift-giving, music, dance, and a shared festive meal — a reminder that joy and belonging are as essential as material support. The initiative is designed to grow year over year, deepening the relationship between GHI and the Ebenezer community and, where possible, extending the same festive spirit to additional partner homes.",
                'status' => 'published',
                'primary_cause' => 'community-empowerment',
                'secondary_causes' => ['poverty-alleviation'],
            ],
            [
                'title' => 'Elders & Widows Compassion Outreach',
                'slug' => 'elders-widows-compassion-outreach',
                'category' => 'partnership',
                'description' => "A season-of-giving initiative that partners with local leadership and county government offices to deliver food, essentials, and dignity-affirming support to widows and vulnerable elders across Kisumu County.",
                'content' => "This initiative reflects Global Harmony Initiative's belief that lasting change happens when citizens and leadership unite with shared purpose. In partnership with county government offices and local leaders, the initiative organizes seasonal outreach — particularly around the Christmas period — to extend food, essential items, and genuine human connection to widows and elders who continue to carry immense strength within their communities. GHI intends to build on this model with additional county-level partnerships in future seasons.",
                'status' => 'published',
                'primary_cause' => 'global-partnership',
                'secondary_causes' => ['poverty-alleviation'],
            ],
            [
                'title' => "Everybody's Birthday Celebration",
                'slug' => 'everybodys-birthday-celebration',
                'category' => 'community-empowerment',
                'description' => "An initiative dedicated to honoring every child in our partner children's homes with the joy of a birthday celebration, school supplies, and a reminder of their inherent worth — regardless of when they were actually born.",
                'content' => "Many children in residential care grow up without ever having their birthday marked. Everybody's Birthday Celebration exists to change that, gathering an entire children's home for a single joint celebration — complete with food, dancing, encouragement from community and faith leaders, and practical gifts like school supplies. The initiative is intended to run annually across GHI's partner homes, ensuring that every child, no matter their individual story, is reminded that they are seen and valued.",
                'status' => 'published',
                'primary_cause' => 'community-empowerment',
                'secondary_causes' => [],
            ],
            [
                'title' => 'Street Justice',
                'slug' => 'street-justice',
                'category' => 'education',
                'description' => "A children's rights and access-to-justice education initiative delivered in partnership with You Can Dream Foundation, equipping children in informal settlements with knowledge of their rights alongside essential learning and welfare support.",
                'content' => "Street Justice is a partnership between Global Harmony Initiative and You Can Dream Foundation focused on helping children in underserved communities understand their rights, recognize when those rights are at risk, and know where to turn for help. Sessions combine rights education with donation drives for books, stationery, and food, plus community-joy days that reinforce the same message through play and connection. The initiative is designed to be an ongoing presence in partner schools rather than a one-off visit, with future sessions planned across additional communities.",
                'status' => 'published',
                'primary_cause' => 'education-access',
                'secondary_causes' => ['community-empowerment'],
            ],
            [
                'title' => 'Mental Health Awareness for Kids',
                'slug' => 'mental-health-awareness-for-kids',
                'category' => 'health',
                'description' => "A mental wellbeing initiative, run in partnership with You Can Dream Foundation, that introduces children in vulnerable communities to emotional literacy, healthy coping skills, and safe spaces for expression.",
                'content' => "Emotional wellbeing is as vital to a child's development as physical health, yet it is rarely addressed directly in under-resourced communities. This initiative brings mental-health practitioners and trained volunteers together with children for full-day programs that blend breakout sessions on identifying and communicating feelings with team-building games, cooking, and creative arts. The goal is to normalize conversations about mental health early, giving children tools they'll carry for life. GHI plans to extend these programs to additional rescue centers and partner homes going forward.",
                'status' => 'published',
                'primary_cause' => 'health-and-well-being',
                'secondary_causes' => [],
            ],
            [
                'title' => 'Child Rescue & Rehabilitation Program',
                'slug' => 'child-rescue-rehabilitation-program',
                'category' => 'health',
                'description' => "An initiative supporting the rescue, recovery, and long-term wellbeing of children referred to Global Harmony Initiative through partner children's homes, providing nutrition, medical follow-up, and consistent care.",
                'content' => "Some of the children GHI encounters arrive through partner homes already in crisis — malnourished, unstable, or newly rescued from neglect. This initiative exists to walk alongside those children through recovery: emergency nutrition, growth monitoring, medical follow-up, and the basic essentials of a safe and nurturing environment. Each child's journey is documented and followed over time, allowing GHI and its supporters to see tangible progress. The initiative is ongoing, with new child profiles added as partner homes refer children in need of this level of sustained support.",
                'status' => 'published',
                'primary_cause' => 'health-and-well-being',
                'secondary_causes' => ['poverty-alleviation'],
            ],
        ];

        $count = 0;
        foreach ($initiatives as $data) {
            $primaryCauseSlug = $data['primary_cause'];
            $secondarySlugs = $data['secondary_causes'];
            unset($data['primary_cause'], $data['secondary_causes']);

            $initiative = Initiative::firstOrCreate(['slug' => $data['slug']], $data);

            // Link primary cause
            $primaryCause = Cause::where('slug', $primaryCauseSlug)->first();
            if ($primaryCause) {
                $initiative->causes()->syncWithoutDetaching([$primaryCause->id]);
            }

            // Link secondary causes
            foreach ($secondarySlugs as $slug) {
                $cause = Cause::where('slug', $slug)->first();
                if ($cause) {
                    $initiative->causes()->syncWithoutDetaching([$cause->id]);
                }
            }

            $count++;
        }

        $this->command->info("✓ Seeded {$count} initiatives with pivot relationships");
    }

    private function seedEvents(): void
    {
        $events = [
            [
                'title' => 'Elimisha Charity Campaign – Kangemi Slums Visit',
                'slug' => 'elimisha-charity-campaign-kangemi-slums',
                'description' => "Join Global Harmony Initiative and local partners in Kangemi for a day dedicated to celebrating life in the community — educational resources, warm meals, and time spent listening to the children who call this neighborhood home.",
                'content' => "On 12th December, Global Harmony Initiative returns to Kangemi alongside local charity partners for a day built around connection. Expect educational materials distributed to children across the neighborhood, a shared meal, and open conversation designed to remind every child present that their story matters and their potential is limitless. Community members and well-wishers are welcome to join or support the effort.",
                'event_date' => '2025-12-12 00:00:00',
                'location' => 'Kangemi Slums, Nairobi',
                'initiative_slug' => 'elimisha-charity-campaign',
                'status' => 'published',
            ],
            [
                'title' => 'Krisi na Ebenezer 2025',
                'slug' => 'krisi-na-ebenezer-2025',
                'description' => "Come and let's share Christmas together — a festive get-together at Ebenezer Children's Home filled with gift-giving, dancing, and pre-Christmas cheer for the children of Ahero.",
                'content' => "On Thursday, 18th December, Global Harmony Initiative joins Ebenezer Life Centre for the annual Krisi na Ebenezer celebration in Ahero, Kisumu. Starting at 10:00 AM, the day will bring gifts, music, dancing, and a festive meal to the children of Ebenezer Children's Home — a joyful pre-Christmas treat ahead of the holiday season. All are welcome to attend or contribute toward making the celebration possible.",
                'event_date' => '2025-12-18 10:00:00',
                'location' => "Ebenezer Children's Home, Ahero, Kisumu",
                'initiative_slug' => 'krisi-na-ebenezer',
                'status' => 'published',
            ],
            [
                'title' => 'Christmas Compassion Outreach with Dr. Mathew Owili',
                'slug' => 'christmas-compassion-outreach-mathew-owili',
                'description' => "This Christmas Eve, Global Harmony Initiative partners with the Office of the Deputy Governor of Kisumu County, Dr. Mathew Owili, to extend warmth and essential support to widows of Kisumu Central.",
                'content' => "On 24th December, Global Harmony Initiative will join the Office of the Deputy Governor of Kisumu County, Dr. Mathew Owili, CBS, to bring Christmas warmth to widows across Kisumu Central. The outreach will include the distribution of essential food items alongside time spent in genuine fellowship — a reminder that when leadership and community unite with purpose, lives are brightened in lasting ways.",
                'event_date' => '2025-12-24 00:00:00',
                'location' => 'Kisumu Central, Kisumu County',
                'initiative_slug' => 'elders-widows-compassion-outreach',
                'status' => 'published',
            ],
            [
                'title' => "Everybody's Birthday Celebration 2026",
                'slug' => 'everybodys-birthday-celebration-2026',
                'description' => "A day dedicated to honoring every child at Ebenezer Children's Home with the celebration their birthday deserves — school supplies, a community feast, and words of encouragement from respected guests.",
                'content' => "On 24th June, Global Harmony Initiative and the Ebenezer community come together to give every child at Ebenezer Children's Home the joy of a birthday celebration, regardless of their actual birth date. Expect school supplies distributed to every child, a shared community feast, dancing, and encouraging words from special guests including Archbishop Dr. Winnie J. Owiti, Bishop Dr. Elijah Kwanya, and Rev. Jane Odera. The day is designed to remind every child present of their inherent worth.",
                'event_date' => '2026-06-24 00:00:00',
                'location' => "Ebenezer Children's Home, Ahero, Kisumu",
                'initiative_slug' => 'everybodys-birthday-celebration',
                'status' => 'published',
            ],
            [
                'title' => 'Street Justice – Hope Kids, Kibera',
                'slug' => 'street-justice-hope-kids-kibera',
                'description' => "Join us as we teach children at Hope Kids School their rights and how to access justice within their communities — plus donations of stationery and food are welcome.",
                'content' => "On 13th June, from 9:00–11:00 AM, Global Harmony Initiative and You Can Dream Foundation bring the Street Justice program to Hope Kids School in Kibera. The session will introduce children to their rights and the resources available to help them thrive, followed by shared learning, conversation, and a meal together. Donations of stationery and food are especially welcomed to support the school's daily needs.",
                'event_date' => '2026-06-13 09:00:00',
                'location' => 'Hope Kids School, Kibera',
                'initiative_slug' => 'street-justice',
                'status' => 'published',
            ],
            [
                'title' => 'Mental Health Awareness for Kids – Zimmerman',
                'slug' => 'mental-health-awareness-for-kids-zimmerman',
                'description' => "A full day dedicated to children's emotional wellbeing at Blessed Kids Rescue Center — mental health breakout sessions, team-building, cooking, and games.",
                'content' => "On 25th July, from 8:00 AM to 4:00 PM, You Can Dream Foundation and Global Harmony Initiative bring a full day of mental-health awareness programming to Blessed Kids Rescue Center in Zimmerman. Children will take part in age-appropriate breakout sessions on identifying and managing emotions, alongside team-building activities, shared cooking, outdoor games, and creative arts. Volunteers, mental-health practitioners, and community supporters are welcome to join or contribute via M-PESA.",
                'event_date' => '2026-07-25 08:00:00',
                'location' => 'Blessed Kids Rescue Center, Zimmerman',
                'initiative_slug' => 'mental-health-awareness-for-kids',
                'status' => 'published',
            ],
            [
                'title' => 'Baby Wendy – First Visit',
                'slug' => 'baby-wendy-first-visit',
                'description' => "GHI ambassador Linda Morgan will make our first visit to Baby Wendy, delivering urgent nutritional support as part of her ongoing recovery plan.",
                'content' => "On 9th July, GHI ambassador Linda Morgan will travel to Ebenezer Children's Home for our first visit to Baby Wendy, a child under the home's care following a period of neglect and serious health concerns. The visit will focus on emergency nutritional support, including fortified feeding, in line with the home's documented priority of catch-up growth and weight gain. This marks the beginning of GHI's sustained involvement in her recovery.",
                'event_date' => '2026-07-09 00:00:00',
                'location' => "Ebenezer Children's Home",
                'initiative_slug' => 'child-rescue-rehabilitation-program',
                'status' => 'published',
            ],
            [
                'title' => 'Baby Wendy – Second Visit',
                'slug' => 'baby-wendy-second-visit',
                'description' => "A follow-up visit to check on Baby Wendy's progress since July, and to continue providing the support that's helping her move from vulnerability toward stability.",
                'content' => "On 20th August, our team returns to Ebenezer Children's Home to check in on Baby Wendy's progress since our first visit in July. This follow-up will assess her weight gain, responsiveness, and overall recovery, and continue GHI's commitment to walking alongside her — and her caregivers — through every stage of her journey back to health.",
                'event_date' => '2026-08-20 00:00:00',
                'location' => "Ebenezer Children's Home",
                'initiative_slug' => 'child-rescue-rehabilitation-program',
                'status' => 'published',
            ],
        ];

        $count = 0;
        foreach ($events as $data) {
            $initiativeSlug = $data['initiative_slug'];
            unset($data['initiative_slug']);

            $initiative = Initiative::where('slug', $initiativeSlug)->first();
            $data['initiative_id'] = $initiative?->id;

            Event::firstOrCreate(['slug' => $data['slug']], $data);
            $count++;
        }

        $this->command->info("✓ Seeded {$count} events");
    }

    private function seedImpactActivities(): void
    {
        $activities = [
            [
                'title' => 'Elimisha Charity Campaign – Kangemi Slums Impact',
                'slug' => 'elimisha-charity-campaign-kangemi-slums-impact',
                'description' => "Reached over 150 children across Kangemi with educational materials and a warm shared meal, reinforcing a sense of belonging and possibility among some of Nairobi's most underserved young people.",
                'people_affected' => 150,
                'metric_type' => 'children_reached',
                'metric_value' => 150,
                'activity_date' => '2025-12-12',
                'location' => 'Kangemi Slums, Nairobi',
                'featured' => true,
                'event_slug' => 'elimisha-charity-campaign-kangemi-slums',
                'status' => 'published',
            ],
            [
                'title' => 'Krisi na Ebenezer 2025 Impact',
                'slug' => 'krisi-na-ebenezer-2025-impact',
                'description' => "Brought festive gifts, food, and a full day of celebration to roughly 100 children at Ebenezer Children's Home, delivering a joyful pre-Christmas moment made possible through partnership with Ebenezer Life Centre.",
                'people_affected' => 100,
                'metric_type' => 'children_reached',
                'metric_value' => 100,
                'activity_date' => '2025-12-18',
                'location' => "Ebenezer Children's Home, Ahero, Kisumu",
                'featured' => false,
                'event_slug' => 'krisi-na-ebenezer-2025',
                'status' => 'published',
            ],
            [
                'title' => 'Christmas Compassion Outreach with Dr. Mathew Owili Impact',
                'slug' => 'christmas-compassion-outreach-owili-impact',
                'description' => "Delivered essential food items and Christmas warmth to widows across Kisumu Central, in partnership with the Office of the Deputy Governor — a reminder of what's possible when leadership and community unite with purpose.",
                'people_affected' => 50,
                'metric_type' => 'widows_supported',
                'metric_value' => 50,
                'activity_date' => '2025-12-24',
                'location' => 'Kisumu Central, Kisumu County',
                'featured' => true,
                'event_slug' => 'christmas-compassion-outreach-mathew-owili',
                'status' => 'published',
            ],
            [
                'title' => "Everybody's Birthday Celebration 2026 Impact",
                'slug' => 'everybodys-birthday-celebration-2026-impact',
                'description' => "Gave 80 children at Ebenezer Children's Home a shared birthday celebration complete with school supplies, a community feast, and encouragement from visiting clergy — honoring each child's worth regardless of their actual birth date.",
                'people_affected' => 80,
                'metric_type' => 'children_reached',
                'metric_value' => 80,
                'activity_date' => '2026-06-24',
                'location' => "Ebenezer Children's Home, Ahero, Kisumu",
                'featured' => true,
                'event_slug' => 'everybodys-birthday-celebration-2026',
                'status' => 'published',
            ],
            [
                'title' => 'Street Justice – Hope Kids, Kibera Impact',
                'slug' => 'street-justice-hope-kids-kibera-impact',
                'description' => "Educated roughly 120 children at Hope Kids School on their rights and access to justice, followed later in the month by a donation drive delivering books, stationery, and food, and a closing community-joy day of shared meals and dance.",
                'people_affected' => 120,
                'metric_type' => 'children_reached',
                'metric_value' => 120,
                'activity_date' => '2026-06-13',
                'location' => 'Hope Kids School, Kibera',
                'featured' => false,
                'event_slug' => 'street-justice-hope-kids-kibera',
                'status' => 'published',
            ],
            [
                'title' => 'Mental Health Awareness for Kids – Zimmerman Impact',
                'slug' => 'mental-health-awareness-kids-zimmerman-impact',
                'description' => "Delivered a full day of emotional-wellbeing programming to around 100 children at Blessed Kids Rescue Center, combining breakout sessions on identifying and managing feelings with team-building, cooking, and creative play.",
                'people_affected' => 100,
                'metric_type' => 'children_reached',
                'metric_value' => 100,
                'activity_date' => '2026-07-25',
                'location' => 'Blessed Kids Rescue Center, Zimmerman',
                'featured' => false,
                'event_slug' => 'mental-health-awareness-for-kids-zimmerman',
                'status' => 'published',
            ],
            [
                'title' => 'Baby Wendy – Recovery Journey Impact',
                'slug' => 'baby-wendy-recovery-journey-impact',
                'description' => "Since our first visit in July, Baby Wendy's weight and responsiveness have visibly improved following sustained nutritional support — tangible progress from a child rescued from severe neglect toward stability and continued growth.",
                'people_affected' => 1,
                'metric_type' => 'child_supported',
                'metric_value' => 1,
                'activity_date' => '2026-08-20',
                'location' => "Ebenezer Children's Home",
                'featured' => true,
                'event_slug' => 'baby-wendy-second-visit',
                'status' => 'published',
            ],
        ];

        $count = 0;
        foreach ($activities as $data) {
            $eventSlug = $data['event_slug'];
            unset($data['event_slug']);

            $event = Event::where('slug', $eventSlug)->first();
            $data['event_id'] = $event?->id;

            ImpactActivity::firstOrCreate(['slug' => $data['slug']], $data);
            $count++;
        }

        $this->command->info("✓ Seeded {$count} impact activities");
    }

    private function seedStories(): void
    {
        $stories = [
            [
                'title' => "Spreading Hope in Kenya's Kangemi Slums",
                'slug' => 'spreading-hope-kangemi-slums',
                'content' => "In a collaborative effort with local charities, our team traveled to Kangemi, a disadvantaged neighborhood in Nairobi, Kenya, for a day centered on the children who call it home. Alongside our partners, we distributed educational resources and shared warm meals, listened to the children's stories, and encouraged them to pursue their dreams without limitation. The visit brought immediate joy, but more than that, it symbolized our continued commitment to walking alongside underprivileged communities — in Kangemi and beyond.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'education',
                'status' => 'published',
            ],
            [
                'title' => 'Festive Cheer in Ahero, Kenya',
                'slug' => 'festive-cheer-ahero-kenya',
                'content' => "On 18th December 2025, Global Harmony Initiative joined hands with Ebenezer Life Centre to host a lively get-together in Ahero, Nyanza. What unfolded was a pre-Christmas treat for the children — gift-giving, spirited dancing, and laughter that filled the compound from morning to afternoon. It was a day that showcased the power of community and the simple joy of being together, reaffirming GHI's dedication to bringing happiness into children's lives wherever we work.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'community-empowerment',
                'status' => 'published',
            ],
            [
                'title' => 'Christmas Warmth for the Widows of Kisumu Central',
                'slug' => 'christmas-warmth-widows-kisumu-central',
                'content' => "On 24th December 2025, Global Harmony Initiative had the honour of partnering with the Office of the Deputy Governor of Kisumu County, Dr. Mathew Owili, CBS, to extend Christmas warmth to widows from Kisumu Central. In the true spirit of the season, essential food items were shared alongside genuine fellowship with women who continue to carry immense strength within their community. The collaboration stood as a reminder that when leaders and citizens unite with purpose, lives can be brightened in ways that truly matter.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'partnership',
                'status' => 'published',
            ],
            [
                'title' => 'Every Child, Every Birthday: A Day at Ebenezer',
                'slug' => 'every-child-every-birthday-ebenezer',
                'content' => "The Global Harmony Initiative family, together with the Ebenezer community, spent an unforgettable day dedicated to service, joy, and meaningful connection. Led by our representative Linda Morgan and supported by the Ebenezer Family, the day brought school supplies — pens, rulers, books, and pencils — alongside a warm community feast to the children of Ebenezer Children's Home. That spirit carried through into the Everybody's Birthday Celebration held on 24th June 2026, a vibrant event honoring every child equally. Archbishop Dr. Winnie J. Owiti, Bishop Dr. Elijah Kwanya, and Rev. Jane Odera graced the occasion with messages of hope and dignity that resonated deeply with the children and staff. Laughter, dancing, and heartfelt interactions defined the day, leaving lasting impressions and reaffirming why this work matters: creating spaces where every child feels seen, valued, and supported. We remain grateful to the Ebenezer Family, volunteers, and partners whose generosity made it all possible.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'community-empowerment',
                'status' => 'published',
            ],
            [
                'title' => 'Street Justice: Building Confidence in Kibera',
                'slug' => 'street-justice-building-confidence-kibera',
                'content' => "Throughout June, Global Harmony Initiative and You Can Dream Foundation carried out a series of outreach activities in Kibera, centered on children's rights awareness, educational support, and community joy. On 13th June, the partners hosted the Street Justice initiative at Hope Kids School — a program introducing children to their rights and the resources available to help them thrive, which evolved into a day of learning, laughter, and shared meals. Later in the month, a donation drive delivered books, stationery, teaching materials, and food to students and staff alike, met with visible excitement from the children. The month closed with a community-joy engagement of shared food, gifts, and dance — a fitting reminder that unity and simple, shared moments can leave a lasting impact on young lives.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'education',
                'status' => 'published',
            ],
            [
                'title' => 'A Day for Emotional Wellbeing in Zimmerman',
                'slug' => 'day-for-emotional-wellbeing-zimmerman',
                'content' => "On 25th July 2026, Blessed Kids Rescue Center in Zimmerman hosted a full-day mental health awareness outreach, organized by You Can Dream Foundation in partnership with Global Harmony Initiative. Running from 8:00 AM to 4:00 PM, the program brought volunteers and mental-health practitioners together with children for breakout sessions on identifying emotions, managing stress, and seeking help — delivered through storytelling, drawing, and group discussion. Team-building activities, shared cooking, and outdoor games rounded out the day, giving children space to bond and express themselves freely. The strong turnout underscored a growing recognition that mental health is as vital to child development as any other form of care, reaffirming GHI's commitment to sustained wellness programming in underserved communities.",
                'author' => 'Global Harmony Initiative Team',
                'category' => 'health',
                'status' => 'published',
            ],
            [
                'title' => 'The Story of Baby Wendy',
                'slug' => 'story-of-baby-wendy',
                'content' => "Baby Wendy's journey is one marked by hardship, resilience, and the power of timely intervention. At just ten months old, she came into formal care at Ebenezer Children's Home following repeated neglect, abandonment, and serious health concerns — her early months made unstable by breastfeeding that ceased at three weeks, repeated moves between relatives, and a mother unable to provide consistent care. Her health eventually deteriorated to Severe Acute Malnutrition, and after a final abandonment on 30th May 2026, she was rescued and placed at Ebenezer on 4th June 2026, finally finding safety and consistent care. When Global Harmony Initiative learned of her case, we embraced her as one of our own. Our first visit on 9th July 2026, led by ambassador Linda Morgan, focused on emergency nutritional support and fortified feeding for catch-up growth — and even amid her fragile condition, we witnessed the resilience already noted in her case file. Our second visit on 20th August 2026 brought encouraging news: her weight and responsiveness had visibly improved, and her caregivers expressed deep gratitude for the continued support. Baby Wendy now stands as proof that timely intervention and coordinated care can change a child's trajectory. Her journey is far from over, but she is steadily moving from vulnerability toward strength — no longer just a rescued infant, but a cherished child of the Global Harmony family.",
                'author' => 'Linda Morgan, GHI Ambassador',
                'category' => 'health',
                'status' => 'published',
            ],
        ];

        $count = 0;
        foreach ($stories as $data) {
            Story::firstOrCreate(['slug' => $data['slug']], $data);
            $count++;
        }

        $this->command->info("✓ Seeded {$count} stories");
    }
}
