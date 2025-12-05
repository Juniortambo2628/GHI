<?php
/**
 * Event Form API Endpoint
 * Returns HTML form for AJAX modal loading
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\Event;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Set JSON header
header('Content-Type: application/json');

// Get event ID if editing
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$eventModel = new Event();
$event = null;

// Load event if editing
if ($eventId > 0) {
    $event = $eventModel->find($eventId);
    if ($event === null || $event === []) {
        echo json_encode(['error' => 'Event not found']);
        exit;
    }
}

// Generate form HTML
ob_start();
?>
<form id="modalEventForm" class="modal-form" data-ajax-form data-entity="event" data-id="<?php echo $eventId; ?>">
    <?php echo csrf_field(); ?>
    
    <div class="mb-3">
        <label for="modal_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_title" 
            name="title" 
            value="<?php echo e($event['title'] ?? ''); ?>" 
            required
        >
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="modal_description" class="form-label">Description <span class="text-danger">*</span></label>
        <div id="modal_description_editor" class="quill-editor-modal"></div>
        <textarea 
            class="d-none" 
            id="modal_description" 
            name="description"
            required
        ><?php echo e($event['description'] ?? ''); ?></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="modal_event_date" 
                    name="event_date" 
                    value="<?php 
                        if (!empty($event['event_date'])) {
                            // Convert DATETIME to date format (yyyy-MM-dd)
                            $date = new DateTime($event['event_date']);
                            echo $date->format('Y-m-d');
                        }
                        
                    ?>"
                    required
                >
                <div class="invalid-feedback"></div>
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
                    value="<?php echo e($event['location'] ?? ''); ?>"
                    placeholder="e.g., Community Center, Online"
                >
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_status" class="form-label">Status</label>
                <select class="form-select" id="modal_status" name="status">
                    <option value="upcoming" <?php echo ($event['status'] ?? 'upcoming') === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="ongoing" <?php echo ($event['status'] ?? '') === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="completed" <?php echo ($event['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($event['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_slug" class="form-label">Slug</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_slug" 
                    name="slug" 
                    value="<?php echo e($event['slug'] ?? ''); ?>"
                    placeholder="auto-generated-from-title"
                >
                <small class="text-muted">Leave empty for auto-generation</small>
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
            value="<?php echo e($event['image'] ?? ''); ?>"
            placeholder="e.g., pexels-example-123456.jpg"
        >
        <small class="text-muted">Enter image filename from Banners-and-portraits folder</small>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
            <span class="btn-text"><?php echo $eventId > 0 ? 'Update' : 'Create'; ?></span>
        </button>
    </div>
</form>
<?php
$formHtml = ob_get_clean();

// Return JSON response
echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'title' => $eventId > 0 ? 'Edit Event' : 'Create New Event',
    'id' => $eventId
]);

