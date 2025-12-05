<?php
/**
 * Cause Form API Endpoint
 * Returns HTML form for AJAX modal loading
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\Cause;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Set JSON header
header('Content-Type: application/json');

// Get cause ID if editing
$causeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$causeModel = new Cause();
$cause = null;

// Load cause if editing
if ($causeId > 0) {
    $cause = $causeModel->find($causeId);
    if ($cause === null || $cause === []) {
        echo json_encode(['error' => 'Cause not found']);
        exit;
    }
}

// Generate form HTML
ob_start();
?>
<form id="modalCauseForm" class="modal-form" data-ajax-form data-entity="cause" data-id="<?php echo $causeId; ?>">
    <?php echo csrf_field(); ?>
    
    <div class="mb-3">
        <label for="modal_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_title" 
            name="title" 
            value="<?php echo e($cause['title'] ?? ''); ?>" 
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
        ><?php echo e($cause['description'] ?? ''); ?></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_slug" class="form-label">Slug</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_slug" 
                    name="slug" 
                    value="<?php echo e($cause['slug'] ?? ''); ?>"
                    placeholder="auto-generated-from-title"
                >
                <small class="text-muted">Leave empty for auto-generation</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_display_order" class="form-label">Display Order</label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="modal_display_order" 
                    name="display_order" 
                    value="<?php echo $cause['display_order'] ?? 0; ?>"
                    min="0"
                >
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_status" class="form-label">Status</label>
                <select class="form-select" id="modal_status" name="status">
                    <option value="draft" <?php echo ($cause['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="active" <?php echo ($cause['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($cause['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_icon" class="form-label">Icon Class</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_icon" 
                    name="icon" 
                    value="<?php echo e($cause['icon'] ?? ''); ?>"
                    placeholder="e.g., bi-heart"
                >
                <small class="text-muted">Bootstrap icon class</small>
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
            value="<?php echo e($cause['image'] ?? ''); ?>"
            placeholder="e.g., pexels-example-123456.jpg"
        >
        <small class="text-muted">Enter image filename from Banners-and-portraits folder</small>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
            <span class="btn-text"><?php echo $causeId > 0 ? 'Update' : 'Create'; ?></span>
        </button>
    </div>
</form>
<?php
$formHtml = ob_get_clean();

// Return JSON response
echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'title' => $causeId > 0 ? 'Edit Cause' : 'Create New Cause',
    'id' => $causeId
]);

