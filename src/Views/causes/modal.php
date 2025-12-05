<!-- Modal -->
<?php
$modalId = 'causeModal';
$title = 'Cause Details';
require dirname(__DIR__, 3) . '/includes/modal.php';
?>

<script>
function openCauseModal(cause) {
    // Construct full image URL
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageUrl = cause.image ? `${baseUrl}/Banners-and-portraits/${cause.image}` : null;
    
    const modalData = {
        title: cause.title,
        description: cause.description,
        image: imageUrl,
        thumbnail: imageUrl,
        meta: {
            'Core Objective': cause.objective ? cause.objective.charAt(0).toUpperCase() + cause.objective.slice(1) : 'N/A',
            'Status': cause.status.charAt(0).toUpperCase() + cause.status.slice(1)
        },
        tags: cause.objective ? [cause.objective, cause.status] : [cause.status],
        action_url: '<?php echo BASE_URL; ?>/initiatives.php?cause=' + cause.id,
        action_text: 'View Related Initiatives'
    };
    openModal('causeModal', modalData);
}
</script>


