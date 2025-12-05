<?php

/**
 * Update Image Paths in Database
 * Updates existing records to use correct image paths from Banners-and-portraits
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();

echo "Updating image paths in database...\n\n";

// Available images
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

// Update causes
echo "Updating causes...\n";
$causes = $db->query("SELECT id, image FROM causes")->fetchAll();
foreach ($causes as $index => $cause) {
    if (strpos($cause['image'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $cause['image'])) {
        $newImage = $availableImages[$index % count($availableImages)];
        $stmt = $db->prepare("UPDATE causes SET image = ? WHERE id = ?");
        $stmt->execute([$newImage, $cause['id']]);
        echo "  Updated cause ID {$cause['id']}: {$newImage}\n";
    }
}

// Update initiatives
echo "\nUpdating initiatives...\n";
$initiatives = $db->query("SELECT id, image FROM initiatives")->fetchAll();
foreach ($initiatives as $index => $initiative) {
    if (strpos($initiative['image'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $initiative['image'])) {
        $newImage = $availableImages[$index % count($availableImages)];
        $stmt = $db->prepare("UPDATE initiatives SET image = ? WHERE id = ?");
        $stmt->execute([$newImage, $initiative['id']]);
        echo "  Updated initiative ID {$initiative['id']}: {$newImage}\n";
    }
}

// Update events
echo "\nUpdating events...\n";
$events = $db->query("SELECT id, image FROM events")->fetchAll();
foreach ($events as $index => $event) {
    if (strpos($event['image'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $event['image'])) {
        $newImage = $availableImages[$index % count($availableImages)];
        $stmt = $db->prepare("UPDATE events SET image = ? WHERE id = ?");
        $stmt->execute([$newImage, $event['id']]);
        echo "  Updated event ID {$event['id']}: {$newImage}\n";
    }
}

// Update impact activities
echo "\nUpdating impact activities...\n";
$impacts = $db->query("SELECT id, thumbnail, image FROM impact_activities")->fetchAll();
foreach ($impacts as $index => $impact) {
    $newImage = $availableImages[$index % count($availableImages)];
    $needsUpdate = false;

    if (strpos($impact['thumbnail'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $impact['thumbnail'])) {
        $needsUpdate = true;
    }
    if (strpos($impact['image'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $impact['image'])) {
        $needsUpdate = true;
    }

    if ($needsUpdate) {
        $stmt = $db->prepare("UPDATE impact_activities SET thumbnail = ?, image = ? WHERE id = ?");
        $stmt->execute([$newImage, $newImage, $impact['id']]);
        echo "  Updated impact ID {$impact['id']}: {$newImage}\n";
    }
}

// Update stories
echo "\nUpdating stories...\n";

try {
    $stories = $db->query("SELECT id, image FROM stories")->fetchAll();
    foreach ($stories as $index => $story) {
        if (strpos($story['image'], 'img_') === 0 || ! file_exists(BASE_PATH . '/Banners-and-portraits/' . $story['image'])) {
            $newImage = $availableImages[$index % count($availableImages)];
            $stmt = $db->prepare("UPDATE stories SET image = ? WHERE id = ?");
            $stmt->execute([$newImage, $story['id']]);
            echo "  Updated story ID {$story['id']}: {$newImage}\n";
        }
    }
} catch (Exception $e) {
    echo "  Note: Could not update stories - " . $e->getMessage() . "\n";
}

echo "\n✅ Image path update completed!\n";
