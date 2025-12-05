<?php
/**
 * Cause Edit Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Cause;
use GHI\Services\ValidationService;
use GHI\Services\CsrfService;
use GHI\Events\ContentCreatedEvent;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$causeModel = new Cause();
$cause = null;
$errors = [];
$success = false;

// Get cause ID
$causeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load cause if editing
if ($causeId > 0) {
    $cause = $causeModel->find($causeId);
    if ($cause === null || $cause === []) {
        header('Location: ' . BASE_URL . '/admin/causes.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    $token = $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
    if (!csrf_validate($token)) {
        $errors['general'] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $displayOrder = empty($_POST['display_order']) ? 0 : (int)$_POST['display_order'];
        $status = trim($_POST['status'] ?? 'draft');
        $image = trim((string) ($_POST['image'] ?? $cause['image'] ?? ''));
        
        // Generate slug if empty
        if (($slug === '' || $slug === '0') && ($title !== '' && $title !== '0')) {
            $slug = generateSlug($title);
        }
        
        // Validate
        if ($title === '' || $title === '0') {
            $errors['title'] = 'Title is required';
        } elseif (strlen($title) < 3) {
            $errors['title'] = 'Title must be at least 3 characters';
        }
        
        if ($description === '' || $description === '0') {
            $errors['description'] = 'Description is required';
        } elseif (strlen($description) < 10) {
            $errors['description'] = 'Description must be at least 10 characters';
        }
        
        if ($slug === '' || $slug === '0') {
            $errors['slug'] = 'Slug is required';
        }
        
        // If no errors, save
        if ($errors === []) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'slug' => $slug,
                    'display_order' => $displayOrder,
                    'status' => $status,
                    'image' => $image,
                ];
                
                if ($causeId > 0) {
                    // Update existing
                    $causeModel->update($causeId, $data);
                    $success = 'Cause updated successfully!';
                    log_message('info', 'Cause updated', ['cause_id' => $causeId]);
                } else {
                    // Create new
                    $causeId = $causeModel->create($data);
                    $success = 'Cause created successfully!';
                    
                    // Dispatch event
                    $event = new ContentCreatedEvent('cause', $causeId, ['title' => $title]);
                    event_dispatch($event, ContentCreatedEvent::NAME);
                    
                    log_message('info', 'Cause created', ['cause_id' => $causeId]);
                    
                    // Redirect to edit page
                    header('Location: ' . BASE_URL . '/admin/cause-edit.php?id=' . $causeId . '&success=1');
                    exit;
                }
                
                // Reload cause data
                $cause = $causeModel->find($causeId);
            } catch (\Exception $e) {
                $errors['general'] = 'An error occurred: ' . $e->getMessage();
                log_message('error', 'Cause save failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

// Set page variables
$pageTitle = $causeId > 0 ? 'Edit Cause' : 'Create Cause';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Causes', 'url' => BASE_URL . '/admin/causes.php'],
    ['label' => $pageTitle, 'url' => ''],
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <!-- Hero Area -->
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><?php echo $pageTitle; ?></h4>
                        <a href="<?php echo BASE_URL; ?>/admin/causes.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Causes
                        </a>
                    </div>
                        <div class="card-body form-card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i><?php echo e($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif;
 ?>
                        
                        <?php if (isset($errors['general']) && ($errors['general'] !== '' && $errors['general'] !== '0')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i><?php echo e($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif;
 ?>
                        
                        <form id="causeForm" method="POST" action="" class="admin-edit-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            class="form-control <?php echo empty($errors['title']) ? '' : 'is-invalid'; ?>" 
                                            id="title" 
                                            name="title" 
                                            value="<?php echo e($cause['title'] ?? ''); ?>" 
                                            required
                                        >
                                        <?php if (isset($errors['title']) && ($errors['title'] !== '' && $errors['title'] !== '0')): ?>
                                            <div class="invalid-feedback"><?php echo e($errors['title']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            class="form-control <?php echo empty($errors['slug']) ? '' : 'is-invalid'; ?>" 
                                            id="slug" 
                                            name="slug" 
                                            value="<?php echo e($cause['slug'] ?? ''); ?>" 
                                            required
                                        >
                                        <small class="text-muted">URL-friendly version of the title (auto-generated if left blank)</small>
                                        <?php if (isset($errors['slug']) && ($errors['slug'] !== '' && $errors['slug'] !== '0')): ?>
                                            <div class="invalid-feedback"><?php echo e($errors['slug']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <div 
                                            id="descriptionEditor" 
                                            data-quill-editor
                                            data-placeholder="Enter cause description..."
                                        ><?php echo $cause['description'] ?? ''; ?></div>
                                        <input type="hidden" id="description" name="description" value="<?php echo e($cause['description'] ?? ''); ?>">
                                        <?php if (isset($errors['description']) && ($errors['description'] !== '' && $errors['description'] !== '0')): ?>
                                            <div class="text-danger small mt-1"><?php echo e($errors['description']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="display_order" class="form-label">Display Order</label>
                                                <input 
                                                    type="number" 
                                                    class="form-control" 
                                                    id="display_order" 
                                                    name="display_order" 
                                                    value="<?php echo e($cause['display_order'] ?? 0); ?>" 
                                                    min="0"
                                                >
                                                <small class="text-muted">Lower numbers appear first</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="active" <?php echo ($cause['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo ($cause['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Featured Image</label>
                                        
                                        <?php if (!empty($cause['image'])): ?>
                                            <div class="current-image-container mb-3">
                                                <div class="current-image-label">
                                                    <i class="bi bi-image-fill me-2"></i>
                                                    <span>Current Image</span>
                                                </div>
                                                <div class="current-image-wrapper">
                                                    <img src="<?php echo getImageUrl($cause['image']); ?>" alt="Current image" class="current-image-preview">
                                                </div>
                                                <p class="small text-muted mt-2 mb-0">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Upload a new image below to replace
                                                </p>
                                            </div>
                                        <?php endif;
 ?>
                                        
                                        <div class="upload-area">
                                            <input 
                                                type="file" 
                                                class="form-control filepond-input" 
                                                id="imageUpload" 
                                                name="imageUpload"
                                                accept="image/*"
                                            >
                                        </div>
                                        <input type="hidden" id="image" name="image" value="<?php echo e($cause['image'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/causes.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $causeId > 0 ? 'Update' : 'Create'; ?> Cause
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
$formHandlerAsset = get_vite_asset('form-handler*.js', 'dist/js') ?? (BASE_URL . '/admin/js/form-handler.js');
?>
<script type="module" src="<?php echo $formHandlerAsset; ?>"></script>
