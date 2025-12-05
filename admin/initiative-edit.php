<?php
/**
 * Initiative Edit Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/cache-helper.php';

use GHI\Models\Initiative;
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

$initiativeModel = new Initiative();
$causeModel = new Cause();
$initiative = null;
$errors = [];
$success = false;

// Get all causes for dropdown
$allCauses = $causeModel->all(['status' => 'active'], 'title ASC');

// Get initiative ID
$initiativeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load initiative if editing
if ($initiativeId > 0) {
    $initiative = $initiativeModel->find($initiativeId);
    if ($initiative === null || $initiative === []) {
        header('Location: ' . BASE_URL . '/admin/initiatives.php');
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
        $category = trim($_POST['category'] ?? '');
        $causeId = empty($_POST['cause_id']) ? null : (int)$_POST['cause_id'];
        $status = trim($_POST['status'] ?? 'draft');
        $image = trim((string) ($_POST['image'] ?? $initiative['image'] ?? ''));
        
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
        
        if ($category === '' || $category === '0') {
            $errors['category'] = 'Category is required';
        }
        
        // If no errors, save
        if ($errors === []) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'category' => $category,
                    'cause_id' => $causeId,
                    'status' => $status,
                    'image' => $image,
                ];
                
                if ($initiativeId > 0) {
                    // Update existing
                    $initiativeModel->update($initiativeId, $data);
                    $success = 'Initiative updated successfully!';
                    log_message('info', 'Initiative updated', ['initiative_id' => $initiativeId]);
                    
                    // Clear cache
                    SimpleCache::delete('initiatives_*');
                } else {
                    // Create new
                    $initiativeId = $initiativeModel->create($data);
                    $success = 'Initiative created successfully!';
                    
                    // Dispatch event
                    $event = new ContentCreatedEvent('initiative', $initiativeId, ['title' => $title]);
                    event_dispatch($event, ContentCreatedEvent::NAME);
                    
                    log_message('info', 'Initiative created', ['initiative_id' => $initiativeId]);
                    
                    // Clear cache
                    SimpleCache::delete('initiatives_*');
                    
                    // Redirect to edit page
                    header('Location: ' . BASE_URL . '/admin/initiative-edit.php?id=' . $initiativeId . '&success=1');
                    exit;
                }
                
                // Reload initiative data
                $initiative = $initiativeModel->find($initiativeId);
            } catch (\Exception $e) {
                $errors['general'] = 'An error occurred: ' . $e->getMessage();
                log_message('error', 'Initiative save failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

// Set page variables
$pageTitle = $initiativeId > 0 ? 'Edit Initiative' : 'Create Initiative';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Initiatives', 'url' => BASE_URL . '/admin/initiatives.php'],
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
                            <a href="<?php echo BASE_URL; ?>/admin/initiatives.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Initiatives
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
                        
                        <form id="initiativeForm" method="POST" action="" class="admin-form admin-edit-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row g-3">
                                <div class="col-12 col-lg-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            class="form-control <?php echo empty($errors['title']) ? '' : 'is-invalid'; ?>" 
                                            id="title" 
                                            name="title" 
                                            value="<?php echo e($initiative['title'] ?? ''); ?>" 
                                            required
                                        >
                                        <?php if (isset($errors['title']) && ($errors['title'] !== '' && $errors['title'] !== '0')): ?>
                                            <div class="invalid-feedback"><?php echo e($errors['title']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                        <div 
                                            id="descriptionEditor" 
                                            data-quill-editor
                                            data-placeholder="Enter initiative description..."
                                        ><?php echo $initiative['description'] ?? ''; ?></div>
                                        <input type="hidden" id="description" name="description" value="<?php echo e($initiative['description'] ?? ''); ?>">
                                        <?php if (isset($errors['description']) && ($errors['description'] !== '' && $errors['description'] !== '0')): ?>
                                            <div class="text-danger small mt-1"><?php echo e($errors['description']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-12 col-md-4">
                                            <div class="mb-3">
                                                <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                                <select 
                                                    class="form-select <?php echo empty($errors['category']) ? '' : 'is-invalid'; ?>" 
                                                    id="category" 
                                                    name="category" 
                                                    required
                                                >
                                                    <option value="">Select Category</option>
                                                    <option value="education" <?php echo ($initiative['category'] ?? '') === 'education' ? 'selected' : ''; ?>>Education</option>
                                                    <option value="health" <?php echo ($initiative['category'] ?? '') === 'health' ? 'selected' : ''; ?>>Health</option>
                                                    <option value="livelihood" <?php echo ($initiative['category'] ?? '') === 'livelihood' ? 'selected' : ''; ?>>Livelihood</option>
                                                    <option value="empowerment" <?php echo ($initiative['category'] ?? '') === 'empowerment' ? 'selected' : ''; ?>>Empowerment</option>
                                                    <option value="partnerships" <?php echo ($initiative['category'] ?? '') === 'partnerships' ? 'selected' : ''; ?>>Partnerships</option>
                                                </select>
                                                <?php if (isset($errors['category']) && ($errors['category'] !== '' && $errors['category'] !== '0')): ?>
                                                    <div class="invalid-feedback"><?php echo e($errors['category']); ?></div>
                                                <?php endif;
 ?>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="mb-3">
                                                <label for="cause_id" class="form-label fw-semibold">Related Cause</label>
                                                <select class="form-select" id="cause_id" name="cause_id">
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
                                        <div class="col-12 col-md-4">
                                            <div class="mb-3">
                                                <label for="status" class="form-label fw-semibold">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="draft" <?php echo ($initiative['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="published" <?php echo ($initiative['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                                    <option value="archived" <?php echo ($initiative['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-lg-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Featured Image</label>
                                        
                                        <?php if (!empty($initiative['image'])): ?>
                                            <div class="current-image-container mb-3">
                                                <div class="current-image-label">
                                                    <i class="bi bi-image-fill me-2"></i>
                                                    <span>Current Image</span>
                                                </div>
                                                <div class="current-image-wrapper">
                                                    <img src="<?php echo getImageUrl($initiative['image']); ?>" alt="Current image" class="current-image-preview">
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
                                        <input type="hidden" id="image" name="image" value="<?php echo e($initiative['image'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading mb-2">Core Objectives</h6>
                                        <ul class="objectives-list small">
                                            <li><strong>Livelihood:</strong> Poverty Alleviation</li>
                                            <li><strong>Education:</strong> Access & Youth Development</li>
                                            <li><strong>Health:</strong> Well-being</li>
                                            <li><strong>Empowerment:</strong> Community</li>
                                            <li><strong>Partnerships:</strong> Global Awareness</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="<?php echo BASE_URL; ?>/admin/initiatives.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $initiativeId > 0 ? 'Update' : 'Create'; ?> Initiative
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
