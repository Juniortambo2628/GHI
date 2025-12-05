<?php

/**
 * Database Seeder Script
 * Populates database with GHI-specific real content
 *
 * Usage: php scripts/seed-database.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/constants.php';

use Faker\Factory;
use GHI\Models\Cause;
use GHI\Models\ContactSubmission;
use GHI\Models\Event;
use GHI\Models\ImpactActivity;
use GHI\Models\Initiative;
use GHI\Models\NewsletterSubscriber;
use GHI\Models\Story;

$faker = Factory::create();
$db = Database::getInstance();

// Available images from Banners-and-portraits folder
$availableImages = [
    'pexels-lagosfoodbank-6472487.jpg',
    'pexels-lagosfoodbank-8054617.jpg',
    'pexels-speakmediauganda-33749783.jpg',
    'pexels-speakmediauganda-33749790.jpg',
    'pexels-speakmediauganda-33749791.jpg',
    'pexels-speakmediauganda-34222337.jpg',
    'pexels-speakmediauganda-34249567.jpg',
    'pexels-elsaboath-28586115.jpg',
    'pexels-caleboquendo-34612590.jpg',
    'pexels-g-star-media-2150953934-32968674.jpg',
    'pexels-ezeguna_graphy-sulaiman-muhammad-2153324075-34536427.jpg',
    'pexels-finalchoice-147015110-34599374.jpg',
    'pexels-lom-doudou-351893580-34617622.jpg',
    'pexels-mo-liban-3049584-5648154.jpg',
    'pexels-seyhmuskino-30668435.jpg',
    'pexels-rdne-6646883.jpg',
    'pexels-rdne-6646897.jpg',
    'pexels-rdne-6646918.jpg',
    'pexels-rdne-6647017.jpg',
    'pexels-belle-co-99483-1000445.jpg',
];

// Core Objectives for initiatives
$coreObjectives = [
    'education' => 'Education Access & Youth Development',
    'health' => 'Health & Well-being',
    'livelihood' => 'Poverty Alleviation & Livelihoods',
    'empowerment' => 'Community Empowerment',
    'partnerships' => 'Global Partnerships & Awareness',
];

// GHI-specific Causes (based on OBJECTIVES)
$causesData = [
    [
        'title' => 'Education for All',
        'description' => 'From paying school fees for bright students to supporting digital learning initiatives, GHI ensures that children and youth have the tools to shape their futures. We believe education is not a privilege — it\'s a foundation for building thriving communities.',
        'quote' => 'Education is not a privilege — it\'s a foundation.',
    ],
    [
        'title' => 'Healthcare Access',
        'description' => 'We strengthen access to healthcare, nutrition, and clean water through partnerships with local clinics and community health volunteers. Healthy families are the cornerstone of thriving communities.',
        'quote' => 'Healthy families are the cornerstone of thriving communities.',
    ],
    [
        'title' => 'Economic Empowerment',
        'description' => 'We support grassroots entrepreneurship, vocational training, and financial inclusion programs that help families generate income and build resilience. Every small business started today can feed a family tomorrow.',
        'quote' => 'Because every small business started today can feed a family tomorrow.',
    ],
    [
        'title' => 'Community Development',
        'description' => 'We champion gender equity, leadership training, and youth participation — ensuring everyone has a voice in the decisions that shape their lives. Empowered communities are unstoppable.',
        'quote' => 'Empowered communities are unstoppable.',
    ],
    [
        'title' => 'Global Partnerships',
        'description' => 'Through our international network, we mobilize supporters, volunteers, and institutions in the U.S. and beyond to drive lasting change in East Africa. When the world works together, transformation follows.',
        'quote' => 'Because when the world works together, transformation follows.',
    ],
];

// GHI-specific Initiatives
$initiativesData = [
    [
        'title' => 'School Fee Support Program',
        'description' => 'Providing financial assistance to promising students from low-income families, ensuring they can continue their education and pursue their dreams.',
        'content' => 'Our School Fee Support Program identifies bright students facing financial barriers and provides the necessary funding to keep them in school. We work directly with schools and families to ensure transparency and accountability. This program has helped hundreds of students continue their education, with many going on to pursue higher education and become community leaders.',
        'category' => 'education',
    ],
    [
        'title' => 'Digital Learning Initiative',
        'description' => 'Bringing technology and digital literacy to underserved communities, preparing youth for the digital economy.',
        'content' => 'The Digital Learning Initiative equips students and young adults with essential computer skills and access to online educational resources. We set up computer labs in community centers and schools, provide training workshops, and connect learners with online courses and certifications.',
        'category' => 'education',
    ],
    [
        'title' => 'Community Health Clinics',
        'description' => 'Supporting local health facilities with resources, training, and medical supplies to improve healthcare access.',
        'content' => 'We partner with existing community health clinics to strengthen their capacity. This includes providing medical equipment, training healthcare workers, organizing health awareness campaigns, and facilitating regular health check-ups for community members.',
        'category' => 'health',
    ],
    [
        'title' => 'Clean Water Projects',
        'description' => 'Installing wells, water purification systems, and water storage facilities to ensure communities have access to safe drinking water.',
        'content' => 'Access to clean water is fundamental to health and well-being. Our Clean Water Projects involve community consultation, well drilling, installation of water purification systems, and training on water conservation and hygiene practices.',
        'category' => 'health',
    ],
    [
        'title' => 'Vocational Training Centers',
        'description' => 'Offering skills training in trades like carpentry, tailoring, mechanics, and agriculture to create employment opportunities.',
        'content' => 'Our Vocational Training Centers provide hands-on training in marketable skills. Students learn practical trades, receive mentorship, and are connected with job opportunities or supported to start their own businesses. Graduates often become trainers themselves, creating a multiplier effect.',
        'category' => 'livelihood',
    ],
    [
        'title' => 'Women Entrepreneurship Program',
        'description' => 'Empowering women with business skills, microfinance, and mentorship to start and grow their own enterprises.',
        'content' => 'This program focuses on empowering women economically through entrepreneurship. We provide business training, access to microfinance loans, mentorship from successful businesswomen, and networking opportunities. Many participants have started successful businesses that support their families and create jobs.',
        'category' => 'empowerment',
    ],
    [
        'title' => 'Youth Mentorship Program',
        'description' => 'Connecting young people with mentors who guide them in education, career development, and personal growth.',
        'content' => 'The Youth Mentorship Program pairs young people with experienced mentors from various fields. Mentors provide guidance on education choices, career paths, personal development, and life skills. Regular workshops and networking events help build a supportive community.',
        'category' => 'education',
    ],
    [
        'title' => 'Agricultural Support Initiative',
        'description' => 'Providing farmers with seeds, tools, training, and market access to improve agricultural productivity and income.',
        'content' => 'We support smallholder farmers with improved seeds, farming tools, training on modern agricultural techniques, and connections to markets. This helps increase crop yields, improve food security, and boost household incomes.',
        'category' => 'livelihood',
    ],
    [
        'title' => 'Microfinance Program',
        'description' => 'Offering small loans and financial literacy training to help individuals and groups start or expand businesses.',
        'content' => 'Our Microfinance Program provides access to credit for those who cannot access traditional banking services. We offer small loans with reasonable terms, financial literacy training, and ongoing support to ensure successful repayment and business growth.',
        'category' => 'livelihood',
    ],
    [
        'title' => 'Global Volunteer Network',
        'description' => 'Connecting volunteers from around the world with local communities to share skills, knowledge, and resources.',
        'content' => 'The Global Volunteer Network facilitates meaningful volunteer experiences. Volunteers contribute their skills in education, healthcare, technology, and business development while learning about local cultures and challenges. This creates lasting connections and mutual understanding.',
        'category' => 'partnerships',
    ],
    [
        'title' => 'Community Feeding Program',
        'description' => 'Organizing food distribution events to ensure families and children in need have access to nutritious meals.',
        'content' => 'Our Community Feeding Programs provide meals to families facing food insecurity. We work with local partners to identify those in need, organize food distribution events, and ensure that nutritious meals reach children, elderly, and vulnerable community members.',
        'category' => 'health',
    ],
    [
        'title' => 'Shelter Improvement Project',
        'description' => 'Rebuilding and repairing homes for elderly and vulnerable community members, restoring safety and dignity.',
        'content' => 'This project focuses on improving housing conditions for those who cannot afford repairs. We rebuild damaged roofs, repair walls, improve ventilation, and ensure homes are safe and weatherproof. This work restores dignity and provides security for vulnerable families.',
        'category' => 'empowerment',
    ],
    [
        'title' => 'Youth Leadership Training',
        'description' => 'Developing leadership skills in young people to prepare them for roles in community development and governance.',
        'content' => 'Our Youth Leadership Training program teaches young people essential leadership skills, public speaking, project management, and community organizing. Graduates often take on leadership roles in their communities, organizing initiatives and advocating for positive change.',
        'category' => 'empowerment',
    ],
    [
        'title' => 'Health Awareness Campaigns',
        'description' => 'Organizing community health education sessions on topics like nutrition, hygiene, disease prevention, and maternal health.',
        'content' => 'We conduct regular health awareness campaigns in communities, covering topics such as nutrition, hygiene, disease prevention, maternal and child health, and mental wellness. These campaigns include workshops, demonstrations, and distribution of educational materials.',
        'category' => 'health',
    ],
    [
        'title' => 'Skills Exchange Program',
        'description' => 'Facilitating knowledge sharing between international volunteers and local communities, creating mutual learning opportunities.',
        'content' => 'The Skills Exchange Program creates opportunities for mutual learning. International volunteers share their expertise while learning from local knowledge and practices. This two-way exchange enriches both parties and creates sustainable solutions.',
        'category' => 'partnerships',
    ],
];

// GHI-specific Event titles and descriptions
$eventTitles = [
    'Community Health Fair',
    'School Fee Payment Drive',
    'Digital Skills Workshop',
    'Women\'s Business Networking Event',
    'Clean Water Well Inauguration',
    'Youth Leadership Summit',
    'Agricultural Training Session',
    'Community Feeding Day',
    'Vocational Training Graduation',
    'Microfinance Group Meeting',
    'Shelter Repair Project Launch',
    'Health Awareness Campaign',
    'Education Support Distribution',
    'Women Entrepreneurship Workshop',
    'Youth Mentorship Session',
    'Community Development Planning',
    'Skills Exchange Workshop',
    'Clean Water Project Completion',
    'School Supplies Distribution',
    'Community Garden Initiative',
    'Health Clinic Opening',
    'Business Development Training',
    'Leadership Development Program',
    'Community Celebration Event',
    'Partnership Building Workshop',
];

// GHI-specific Impact descriptions
$impactTitles = [
    'Feeding Families in Zanzibar',
    'Supporting Education Access',
    'Shelter for the Elderly',
    'Empowering Women Entrepreneurs',
    'Youth Skills Development',
    'Community Health Improvement',
    'Clean Water Access',
    'School Fee Support Success',
    'Agricultural Training Impact',
    'Vocational Training Graduates',
    'Microfinance Success Stories',
    'Digital Literacy Advancement',
    'Community Leadership Development',
    'Health Awareness Impact',
    'Partnership Building Success',
    'Economic Empowerment Results',
    'Education Transformation',
    'Community Resilience Building',
    'Youth Mentorship Success',
    'Women\'s Economic Independence',
    'Clean Water Community Impact',
    'School Infrastructure Support',
    'Healthcare Access Improvement',
    'Skills Training Outcomes',
    'Community Development Milestone',
    'Education Scholarship Program',
    'Health Clinic Support',
    'Agricultural Productivity Increase',
    'Youth Employment Creation',
    'Community Well-being Enhancement',
];

// GHI-specific Story titles and content
$storyTitles = [
    'A Student\'s Dream Realized Through School Fee Support',
    'From Digital Illiteracy to Tech Confidence',
    'How Clean Water Changed Our Community',
    'A Woman\'s Journey to Entrepreneurship',
    'Youth Leadership Transforming Communities',
    'Healthcare Access Saving Lives',
    'Vocational Training Opening Doors',
    'Microfinance Empowering Families',
    'Community Feeding Bringing Hope',
    'Shelter Repair Restoring Dignity',
    'Agricultural Training Boosting Incomes',
    'Mentorship Changing Young Lives',
    'Health Awareness Preventing Disease',
    'Skills Exchange Creating Connections',
    'Education Breaking Poverty Cycles',
    'Women\'s Empowerment Success Story',
    'Community Development Through Partnership',
    'Youth Employment Through Training',
    'Clean Water Improving Health',
    'School Support Keeping Dreams Alive',
];

echo "Starting database seeding with GHI-specific content...\n\n";

// Clear existing data
try {
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("DELETE FROM impact_activities");
    $db->exec("DELETE FROM events");
    $db->exec("DELETE FROM stories");
    $db->exec("DELETE FROM initiatives");
    $db->exec("DELETE FROM causes");
    $db->exec("DELETE FROM contact_submissions");
    $db->exec("DELETE FROM newsletter_subscribers");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Cleared existing data.\n\n";
} catch (Exception $e) {
    echo "Note: Could not clear existing data: " . $e->getMessage() . "\n";
    echo "Attempting to continue with seeding...\n\n";
}

// Seed Causes
echo "Seeding Causes...\n";
$causeModel = new Cause();
$causeIds = [];

foreach ($causesData as $i => $causeData) {
    $slug = strtolower(str_replace([' ', '&'], ['-', 'and'], $causeData['title']));

    $data = [
        'title' => $causeData['title'],
        'slug' => $slug,
        'description' => $causeData['description'],
        'quote' => $causeData['quote'],
        'image' => $availableImages[$i % count($availableImages)],
        'display_order' => $i + 1,
        'status' => 'active',
        'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    $causeIds[] = $causeModel->create($data);
    echo "  Created cause: {$data['title']}\n";
}

// Seed Initiatives
echo "\nSeeding Initiatives...\n";
$initiativeModel = new Initiative();
$initiativeIds = [];

foreach ($initiativesData as $i => $initData) {
    $slug = strtolower(str_replace([' ', '&', '\'', ','], ['-', 'and', '', ''], $initData['title']));
    $slug = preg_replace('/-+/', '-', trim($slug, '-'));

    // Map category to cause_id
    $categoryToCause = [
        'education' => $causeIds[0], // Education for All
        'health' => $causeIds[1],    // Healthcare Access
        'livelihood' => $causeIds[2], // Economic Empowerment
        'empowerment' => $causeIds[3], // Community Development
        'partnerships' => $causeIds[4], // Global Partnerships
    ];
    $causeId = $categoryToCause[$initData['category']] ?? $causeIds[0];

    $data = [
        'title' => $initData['title'],
        'slug' => $slug,
        'description' => $initData['description'],
        'content' => $initData['content'],
        'image' => $availableImages[$i % count($availableImages)],
        'category' => $initData['category'],
        'cause_id' => $causeId,
        'status' => 'published',
        'created_at' => $faker->dateTimeBetween('-8 months', 'now')->format('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    $initiativeIds[] = $initiativeModel->create($data);
    echo "  Created initiative: {$data['title']}\n";
}

// Seed Events
echo "\nSeeding Events...\n";
$eventModel = new Event();
$eventIds = [];

foreach ($eventTitles as $i => $eventTitle) {
    $initiativeId = $initiativeIds[array_rand($initiativeIds)];
    $eventDateTime = $faker->dateTimeBetween('-6 months', '+6 months');
    $slug = strtolower(str_replace([' ', '&', '\'', ','], ['-', 'and', '', ''], $eventTitle));
    $slug = preg_replace('/-+/', '-', trim($slug, '-'));

    $locations = [
        'Nairobi, Kenya',
        'Dar es Salaam, Tanzania',
        'Kampala, Uganda',
        'Mombasa, Kenya',
        'Arusha, Tanzania',
        'Zanzibar, Tanzania',
        'Kisumu, Kenya',
        'Mbale, Uganda',
        'Dodoma, Tanzania',
        'Nakuru, Kenya',
    ];

    $data = [
        'title' => $eventTitle,
        'slug' => $slug,
        'description' => 'Join us for this important community event that brings together stakeholders, beneficiaries, and supporters to create positive change.',
        'content' => 'This event is part of our ongoing commitment to community development. We will share updates on our programs, celebrate achievements, and plan for the future. All community members are welcome to attend and participate.',
        'image' => $availableImages[$i % count($availableImages)],
        'event_date' => $eventDateTime->format('Y-m-d H:i:s'),
        'location' => $locations[$i % count($locations)],
        'initiative_id' => $initiativeId,
        'status' => 'published',
        'created_at' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    $eventIds[] = $eventModel->create($data);
    echo "  Created event: {$data['title']} on {$eventDateTime->format('Y-m-d')}\n";
}

// Seed Impact Activities
echo "\nSeeding Impact Activities...\n";
$impactModel = new ImpactActivity();

foreach ($impactTitles as $i => $impactTitle) {
    $eventId = $eventIds[array_rand($eventIds)];
    $slug = strtolower(str_replace([' ', '&', '\'', ','], ['-', 'and', '', ''], $impactTitle));
    $slug = preg_replace('/-+/', '-', trim($slug, '-'));

    $outcomes = [
        'This initiative has made a significant positive impact on the community, improving lives and creating lasting change.',
        'Through dedicated effort and community support, we have achieved meaningful results that will benefit families for years to come.',
        'The success of this program demonstrates the power of community-led development and sustainable solutions.',
        'Participants have reported improved outcomes and increased opportunities as a direct result of this initiative.',
        'This impact story showcases how targeted support can transform communities and create pathways to prosperity.',
    ];

    $imageIndex = $i % count($availableImages);
    $data = [
        'title' => $impactTitle,
        'slug' => $slug,
        'description' => 'A significant achievement in our ongoing efforts to create positive change in East African communities.',
        'event_id' => $eventId,
        'people_affected' => $faker->numberBetween(25, 500),
        'outcome_summary' => $outcomes[$i % count($outcomes)],
        'thumbnail' => $availableImages[$imageIndex],
        'image' => $availableImages[$imageIndex],
        'display_order' => $i + 1,
        'status' => 'published',
        'created_at' => $faker->dateTimeBetween('-4 months', 'now')->format('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    $impactModel->create($data);
    echo "  Created impact: {$data['title']} ({$data['people_affected']} people affected)\n";
}

// Seed Stories
echo "\nSeeding Stories...\n";

try {
    $storyModel = new Story();

    $storyContent = [
        'This inspiring story demonstrates the transformative power of education and community support. Through our programs, individuals have overcome significant challenges to achieve their dreams and contribute to their communities.',
        'The journey from struggle to success is never easy, but with the right support and determination, anything is possible. This story highlights the resilience of the human spirit and the importance of community.',
        'Through partnership and collaboration, we have witnessed remarkable transformations. This story shares the experiences of those who have benefited from our programs and are now paying it forward.',
        'Education opens doors that once seemed permanently closed. This story follows the journey of someone who, with support, has achieved academic success and is now helping others do the same.',
        'Economic empowerment creates a ripple effect that benefits entire families and communities. This story showcases how small investments in people can lead to significant positive change.',
        'Health and well-being are fundamental to thriving communities. This story illustrates how improved access to healthcare has transformed lives and strengthened community resilience.',
        'When communities come together with shared purpose, incredible things happen. This story celebrates the power of collective action and mutual support.',
        'Youth are the future, and investing in their development pays dividends for generations. This story highlights the impact of youth-focused programs and mentorship.',
        'Women\'s empowerment is essential for community development. This story shares how women have overcome barriers to become leaders and change-makers in their communities.',
        'Clean water is life. This story demonstrates how access to safe drinking water has improved health, saved time, and created new opportunities for community members.',
        'Skills training provides pathways to employment and economic independence. This story follows graduates who have used their new skills to build better lives.',
        'Community support makes all the difference. This story shows how collective action and shared resources can address challenges and create opportunities.',
        'Education is the foundation of progress. This story celebrates students who, with support, have achieved academic excellence and are pursuing higher education.',
        'Healthcare access saves lives. This story highlights how improved medical services have prevented illness, treated conditions, and improved quality of life.',
        'Economic opportunities create hope. This story shares how entrepreneurship and business development have transformed families and communities.',
        'Partnerships amplify impact. This story demonstrates how collaboration between local and international partners creates sustainable solutions.',
        'Youth leadership drives change. This story showcases young people who have taken on leadership roles and are making a difference in their communities.',
        'Community development is a journey. This story shares the progress made over time and the vision for the future.',
        'Empowerment creates independence. This story highlights how programs focused on empowerment have helped individuals take control of their futures.',
        'Hope and opportunity go hand in hand. This story celebrates the positive changes happening in communities and the bright future ahead.',
    ];

    $categories = ['education', 'health', 'livelihood', 'empowerment'];

    foreach ($storyTitles as $i => $storyTitle) {
        $slug = strtolower(str_replace([' ', '&', '\'', ','], ['-', 'and', '', ''], $storyTitle));
        $slug = preg_replace('/-+/', '-', trim($slug, '-'));

        $data = [
            'title' => $storyTitle,
            'slug' => $slug,
            'description' => 'An inspiring story of transformation and hope from our community programs.',
            'content' => $storyContent[$i % count($storyContent)],
            'image' => $availableImages[$i % count($availableImages)],
            'category' => $categories[$i % count($categories)],
            'likes' => $faker->numberBetween(5, 100),
            'comments' => $faker->numberBetween(2, 50),
            'status' => 'published',
            'created_at' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $storyModel->create($data);
        echo "  Created story: {$data['title']}\n";
    }
} catch (Exception $e) {
    echo "  Note: Could not seed stories - " . $e->getMessage() . "\n";
}

// Seed Contact Submissions
echo "\nSeeding Contact Submissions...\n";
$contactModel = new ContactSubmission();

$contactMessages = [
    'I would like to learn more about your volunteer opportunities. How can I get involved?',
    'Thank you for the amazing work you are doing in East Africa. I would like to make a donation.',
    'I am interested in partnering with GHI for a community project in my area. Can we discuss this?',
    'I would like to receive updates about your programs and impact. Please add me to your newsletter.',
    'I have skills in education and would like to volunteer. What opportunities are available?',
    'Your work is inspiring! I would like to support a specific initiative. How can I do that?',
    'I am a student and would like to learn more about your programs for my research project.',
    'I am interested in organizing a fundraising event for GHI. Can you provide guidance?',
    'I would like to know more about your microfinance program and how it works.',
    'Thank you for supporting education in East Africa. I would like to sponsor a student.',
    'I am interested in your women\'s empowerment programs. How can I support this initiative?',
    'I would like to visit one of your project sites. Is this possible?',
    'I have medical training and would like to volunteer with your health programs.',
    'I am interested in learning more about your agricultural support initiatives.',
    'I would like to make a recurring donation. How can I set this up?',
];

for ($i = 0; $i < 15; $i++) {
    $data = [
        'name' => $faker->name(),
        'email' => $faker->email(),
        'message' => $contactMessages[$i % count($contactMessages)],
        'status' => $faker->randomElement(['new', 'read', 'replied']),
        'created_at' => $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d H:i:s'),
    ];
    $contactModel->create($data);
    echo "  Created contact submission from: {$data['name']}\n";
}

// Seed Newsletter Subscribers
echo "\nSeeding Newsletter Subscribers...\n";

try {
    $newsletterModel = new NewsletterSubscriber();

    for ($i = 0; $i < 50; $i++) {
        $data = [
            'email' => $faker->email(),
            'name' => $faker->name(),
            'status' => $faker->randomElement(['active', 'unsubscribed']),
            'subscribed_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $newsletterModel->create($data);
        if ($i % 10 == 0) {
            echo "  Created {$i} subscribers...\n";
        }
    }
    echo "  Created 50 newsletter subscribers.\n";
} catch (Exception $e) {
    echo "  Note: Could not seed newsletter subscribers - " . $e->getMessage() . "\n";
}

echo "\n✅ Database seeding completed successfully!\n";
echo "Summary:\n";
echo "  - Causes: " . count($causeIds) . "\n";
echo "  - Initiatives: " . count($initiativeIds) . "\n";
echo "  - Events: " . count($eventIds) . "\n";
echo "  - Impact Activities: " . count($impactTitles) . "\n";
echo "  - Stories: " . count($storyTitles) . "\n";
echo "  - Contact Submissions: 15\n";
echo "  - Newsletter Subscribers: 50\n";
