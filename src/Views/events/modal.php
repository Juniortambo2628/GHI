<!-- Modal -->
<?php
$modalId = 'eventModal';
$title = 'Event Details';
require dirname(__DIR__, 3) . '/includes/modal.php';
?>

<script>
function openEventModal(event) {
    // Construct full image URL
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageUrl = event.image ? `${baseUrl}/Banners-and-portraits/${event.image}` : null;
    
    const modalData = {
        title: event.title,
        description: event.description,
        image: imageUrl,
        thumbnail: imageUrl,
        meta: {
            'Initiative': event.initiative,
            'Date': new Date(event.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
            'Location': event.location,
            'Status': event.status.charAt(0).toUpperCase() + event.status.slice(1)
        },
        tags: [event.initiative, event.status],
        action_url: '<?php echo BASE_URL; ?>/get-involved.php?event=' + event.id,
        action_text: 'Get Involved'
    };
    openModal('eventModal', modalData);
}
</script>


