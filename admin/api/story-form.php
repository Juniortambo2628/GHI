<?php
/**
 * Story Form API Endpoint
 * Returns HTML form for AJAX modal loading
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\Story;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Set JSON header
header('Content-Type: application/json');

// Get story ID if editing
$storyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$storyModel = new Story();
$story = null;

// Load story if editing
if ($storyId > 0) {
    $story = $storyModel->find($storyId);
    if ($story === null || $story === []) {
        echo json_encode(['error' => 'Story not found']);
        exit;
    }
}

// Generate form HTML
ob_start();
?>
<form id="modalStoryForm" class="modal-form" data-ajax-form data-entity="story" data-id="<?php echo $storyId; ?>">
    <?php echo csrf_field(); ?>
    
    <div class="mb-3">
        <label for="modal_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_title" 
            name="title" 
            value="<?php echo e($story['title'] ?? ''); ?>" 
            required
        >
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="mb-3">
        <label for="modal_content" class="form-label">Content <span class="text-danger">*</span></label>
        <div id="modal_content_editor" class="quill-editor-modal-large"></div>
        <textarea 
            class="d-none" 
            id="modal_content" 
            name="content"
            required
        ><?php echo e($story['content'] ?? ''); ?></textarea>
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_author" class="form-label">Author</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="modal_author" 
                    name="author" 
                    value="<?php echo e($story['author'] ?? ''); ?>"
                    placeholder="e.g., John Doe"
                >
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_category" class="form-label">Category</label>
                <select class="form-select" id="modal_category" name="category">
                    <option value="">Select Category</option>
                    <option value="impact" <?php echo ($story['category'] ?? '') === 'impact' ? 'selected' : ''; ?>>Impact</option>
                    <option value="success" <?php echo ($story['category'] ?? '') === 'success' ? 'selected' : ''; ?>>Success Story</option>
                    <option value="testimonial" <?php echo ($story['category'] ?? '') === 'testimonial' ? 'selected' : ''; ?>>Testimonial</option>
                    <option value="update" <?php echo ($story['category'] ?? '') === 'update' ? 'selected' : ''; ?>>Update</option>
                    <option value="news" <?php echo ($story['category'] ?? '') === 'news' ? 'selected' : ''; ?>>News</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modal_status" class="form-label">Status</label>
                <select class="form-select" id="modal_status" name="status">
                    <option value="draft" <?php echo ($story['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($story['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo ($story['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
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
                    value="<?php echo e($story['slug'] ?? ''); ?>"
                    placeholder="auto-generated-from-title"
                >
                <small class="text-muted">Leave empty for auto-generation</small>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="modal_featured_image" class="form-label">Featured Image</label>
        <input 
            type="text" 
            class="form-control" 
            id="modal_featured_image" 
            name="featured_image" 
            value="<?php echo e($story['featured_image'] ?? ''); ?>"
            placeholder="e.g., pexels-example-123456.jpg"
        >
        <small class="text-muted">Enter image filename from Banners-and-portraits folder</small>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-micromodal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
            <span class="btn-text"><?php echo $storyId > 0 ? 'Update' : 'Create'; ?></span>
        </button>
    </div>
</form>
<?php
$formHtml = ob_get_clean();

// Return JSON response
echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'title' => $storyId > 0 ? 'Edit Story' : 'Create New Story',
    'id' => $storyId
]);

