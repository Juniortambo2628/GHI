<!-- Modal -->
<?php
$modalId = 'impactModal';
$title = 'Impact Story Details';
require dirname(__DIR__, 3) . '/includes/modal.php';
?>

<script>
function openImpactModal(impact) {
    // Construct full image URL
    const baseUrl = '<?php echo BASE_URL; ?>';
    const imageUrl = (impact.image || impact.thumbnail) ? `${baseUrl}/Banners-and-portraits/${impact.image || impact.thumbnail}` : null;
    
    const modalData = {
        title: impact.title,
        description: impact.description,
        image: imageUrl,
        thumbnail: imageUrl,
        meta: {
            'Region': impact.region.charAt(0).toUpperCase() + impact.region.slice(1),
            'Core Objective': impact.objective.charAt(0).toUpperCase() + impact.objective.slice(1),
            'Date': new Date(impact.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
            'Lives Impacted': impact.lives_impacted ? impact.lives_impacted.toLocaleString() : '0'
        },
        tags: [impact.region, impact.objective],
        action_url: '<?php echo BASE_URL; ?>/stories.php?impact=' + impact.id,
        action_text: 'Read Full Story'
    };
    openModal('impactModal', modalData);
}

function number_format(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>


