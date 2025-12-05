<!-- Modal -->
<?php
$modalId = 'storyModal';
$title = 'Story Details';
require dirname(__DIR__, 3) . '/includes/modal.php';
?>

<script>
function openStoryModal(story) {
    // Construct full image URL
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageUrl = story.image ? `${baseUrl}/Banners-and-portraits/${story.image}` : null;
    
    const modalData = {
        title: story.title,
        description: story.content || story.description,
        image: imageUrl,
        thumbnail: imageUrl,
        meta: {
            'Region': story.region.charAt(0).toUpperCase() + story.region.slice(1),
            'Category': story.category.charAt(0).toUpperCase() + story.category.slice(1),
            'Date': new Date(story.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
            'Author': story.author
        },
        tags: [story.region, story.category],
        action_url: '#',
        action_text: 'Share Story'
    };
    openModal('storyModal', modalData);
}

function likeStory(storyId) {
    // TODO: Implement AJAX call to like story
    const likesElement = document.getElementById('likes-' + storyId);
    if (likesElement) {
        const currentLikes = parseInt(likesElement.textContent) || 0;
        likesElement.textContent = currentLikes + 1;
    }
}

function commentStory(storyId) {
    // TODO: Implement comment functionality
    alert('Comment functionality coming soon!');
}

function shareStory(storyId) {
    // TODO: Implement share functionality
    if (navigator.share) {
        navigator.share({
            title: 'GHI Story',
            text: 'Check out this inspiring story from Global Harmony Initiative',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script>


