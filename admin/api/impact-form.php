<?php
/**
 * Impact Activity Form API Endpoint
 * Returns HTML form for AJAX modal loading
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\ImpactActivity;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Set JSON header
header('Content-Type: application/json');

// Get impact activity ID if editing
$activityId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$activityModel = new ImpactActivity();
$activity = null;

// Load activity if editing
if ($activityId > 0) {
    $activity = $activityModel->find($activityId);
    if ($activity === null || $activity === []) {
        echo json_encode(['error' => 'Impact Activity not found']);
        exit;
    }
}

// Generate form HTML
ob_start();
?>
<form id="modalImpactForm" class="modal-form" data-ajax-form data-entity="impact" data-id="<?php echo $activityId; ?>">
    <?php echo csrf_field(); ?>
    
    <div class="mb-3">
        <label for="modal_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_title" 
            name="title" 
            value="<?php echo e($activity['title'] ?? ''); ?>" 
            required
        >
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="modal_description" class="form-label">Description <span class="text-danger">*</span></label>
        <textarea 
            class="form-control" 
            id="modal_description" 
            name="description" 
            rows="6"
            required
        ><?php echo e($activity['description'] ?? ''); ?></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_metric_type" class="form-label">Metric Type</label>
                <select class="form-select" id="modal_metric_type" name="metric_type">
                    <option value="">Select Type</option>
                    <option value="people_reached" <?php echo ($activity['metric_type'] ?? '') === 'people_reached' ? 'selected' : ''; ?>>People Reached</option>
                    <option value="projects_completed" <?php echo ($activity['metric_type'] ?? '') === 'projects_completed' ? 'selected' : ''; ?>>Projects Completed</option>
                    <option value="funds_raised" <?php echo ($activity['metric_type'] ?? '') === 'funds_raised' ? 'selected' : ''; ?>>Funds Raised</option>
                    <option value="volunteers" <?php echo ($activity['metric_type'] ?? '') === 'volunteers' ? 'selected' : ''; ?>>Volunteers</option>
                    <option value="beneficiaries" <?php echo ($activity['metric_type'] ?? '') === 'beneficiaries' ? 'selected' : ''; ?>>Beneficiaries</option>
                    <option value="custom" <?php echo ($activity['metric_type'] ?? '') === 'custom' ? 'selected' : ''; ?>>Custom</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_metric_value" class="form-label">Metric Value</label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="modal_metric_value" 
                    name="metric_value" 
                    value="<?php echo $activity['metric_value'] ?? 0; ?>"
                    min="0"
                    step="0.01"
                >
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_activity_date" class="form-label">Activity Date</label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="modal_activity_date" 
                    name="activity_date" 
                    value="<?php echo $activity['activity_date'] ?? date('Y-m-d'); ?>"
                >
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_location" class="form-label">Location</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_location" 
                    name="location" 
                    value="<?php echo e($activity['location'] ?? ''); ?>"
                    placeholder="e.g., Community Center"
                >
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_status" class="form-label">Status</label>
                <select class="form-select" id="modal_status" name="status">
                    <option value="draft" <?php echo ($activity['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($activity['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo ($activity['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_featured" class="form-label">Featured</label>
                <select class="form-select" id="modal_featured" name="featured">
                    <option value="0" <?php echo (($activity['featured'] ?? 0) == 0) ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo (($activity['featured'] ?? 0) == 1) ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="modal_image" class="form-label">Image URL</label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_image" 
            name="image" 
            value="<?php echo e($activity['image'] ?? ''); ?>"
            placeholder="e.g., pexels-example-123456.jpg"
        >
        <small class="text-muted">Enter image filename from Banners-and-portraits folder</small>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
            <span class="btn-text"><?php echo $activityId > 0 ? 'Update' : 'Create'; ?></span>
        </button>
    </div>
</form>
<?php
$formHtml = ob_get_clean();

// Return JSON response
echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'title' => $activityId > 0 ? 'Edit Impact Activity' : 'Create New Impact Activity',
    'id' => $activityId
]);

