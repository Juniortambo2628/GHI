<!-- Modal -->
<?php
$modalId = 'initiativeModal';
$title = 'Initiative Details';
require dirname(__DIR__, 3) . '/includes/modal.php';
?>

<script>
function openInitiativeModal(initiative) {
    // Construct full image URL
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageUrl = initiative.image ? `${baseUrl}/Banners-and-portraits/${initiative.image}` : null;
    
    const modalData = {
        title: initiative.title,
        description: initiative.description,
        image: imageUrl,
        thumbnail: imageUrl,
        meta: {
            'Core Objective': initiative.objective.charAt(0).toUpperCase() + initiative.objective.slice(1),
            'Status': initiative.status.charAt(0).toUpperCase() + initiative.status.slice(1),
            'Events Planned': initiative.events_planned,
            'Events Completed': initiative.events_completed
        },
        tags: [initiative.objective, initiative.status],
        action_url: '<?php echo BASE_URL; ?>/events.php?initiative=' + initiative.id,
        action_text: 'View Events'
    };
    openModal('initiativeModal', modalData);
}
</script>


