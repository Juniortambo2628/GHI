<?php
/**
 * Initiative Form API Endpoint
 * Returns HTML form for AJAX modal loading
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\Initiative;
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

// Get initiative ID if editing
$initiativeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$initiativeModel = new Initiative();
$causeModel = new Cause();
$initiative = null;

// Load initiative if editing
if ($initiativeId > 0) {
    $initiative = $initiativeModel->find($initiativeId);
    if ($initiative === null || $initiative === []) {
        echo json_encode(['error' => 'Initiative not found']);
        exit;
    }
}

// Get all causes for dropdown
$allCauses = $causeModel->all(['status' => 'active'], 'title ASC');

// Generate form HTML
ob_start();
?>
<form id="modalInitiativeForm" class="modal-form" data-ajax-form data-entity="initiative" data-id="<?php echo $initiativeId; ?>">
    <?php echo csrf_field(); ?>
    
    <div class="mb-3">
        <label for="modal_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_title" 
            name="title" 
            value="<?php echo e($initiative['title'] ?? ''); ?>" 
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
        ><?php echo e($initiative['description'] ?? ''); ?></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="modal_category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="education" <?php echo ($initiative['category'] ?? '') === 'education' ? 'selected' : ''; ?>>Education</option>
                    <option value="health" <?php echo ($initiative['category'] ?? '') === 'health' ? 'selected' : ''; ?>>Health</option>
                    <option value="livelihood" <?php echo ($initiative['category'] ?? '') === 'livelihood' ? 'selected' : ''; ?>>Livelihood</option>
                    <option value="empowerment" <?php echo ($initiative['category'] ?? '') === 'empowerment' ? 'selected' : ''; ?>>Empowerment</option>
                    <option value="partnerships" <?php echo ($initiative['category'] ?? '') === 'partnerships' ? 'selected' : ''; ?>>Partnerships</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_cause_id" class="form-label">Related Cause</label>
                <select class="form-select" id="modal_cause_id" name="cause_id">
                    <option value="">Select Cause (Optional)</option>
                    <?php foreach ($allCauses as $cause): ?>
                        <option value="<?php echo $cause['id']; ?>" <?php echo ($initiative['cause_id'] ?? '') == $cause['id'] ? 'selected' : ''; ?>>
                            <?php echo e($cause['title']); ?>
                        </option>
                    <?php endforeach;
 ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_status" class="form-label">Status</label>
                <select class="form-select" id="modal_status" name="status">
                    <option value="draft" <?php echo ($initiative['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($initiative['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo ($initiative['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_image" class="form-label">Image URL</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_image" 
                    name="image" 
                    value="<?php echo e($initiative['image'] ?? ''); ?>"
                    placeholder="e.g., pexels-example-123456.jpg"
                >
                <small class="text-muted">Enter image filename from Banners-and-portraits folder</small>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
            <span class="btn-text"><?php echo $initiativeId > 0 ? 'Update' : 'Create'; ?></span>
        </button>
    </div>
</form>
<?php
$formHtml = ob_get_clean();

// Return JSON response
echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'title' => $initiativeId > 0 ? 'Edit Initiative' : 'Create New Initiative',
    'id' => $initiativeId
]);

