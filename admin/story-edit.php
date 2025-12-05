<?php
/**
 * Story Edit Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Story;
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

$storyModel = new Story();
$story = null;
$errors = [];
$success = false;

// Get story ID
$storyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load story if editing
if ($storyId > 0) {
    $story = $storyModel->find($storyId);
    if ($story === null || $story === []) {
        header('Location: ' . BASE_URL . '/admin/stories.php');
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
        $status = trim($_POST['status'] ?? 'draft');
        $image = trim((string) ($_POST['image'] ?? $story['image'] ?? ''));
        
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
                    'status' => $status,
                    'image' => $image,
                ];
                
                if ($storyId > 0) {
                    // Update existing
                    $storyModel->update($storyId, $data);
                    $success = 'Story updated successfully!';
                    log_message('info', 'Story updated', ['story_id' => $storyId]);
                } else {
                    // Create new
                    $storyId = $storyModel->create($data);
                    $success = 'Story created successfully!';
                    
                    // Dispatch event
                    $event = new ContentCreatedEvent('story', $storyId, ['title' => $title]);
                    event_dispatch($event, ContentCreatedEvent::NAME);
                    
                    log_message('info', 'Story created', ['story_id' => $storyId]);
                    
                    // Redirect to edit page
                    header('Location: ' . BASE_URL . '/admin/story-edit.php?id=' . $storyId . '&success=1');
                    exit;
                }
                
                // Reload story data
                $story = $storyModel->find($storyId);
            } catch (\Exception $e) {
                $errors['general'] = 'An error occurred: ' . $e->getMessage();
                log_message('error', 'Story save failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

// Set page variables
$pageTitle = $storyId > 0 ? 'Edit Story' : 'Create Story';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Stories', 'url' => BASE_URL . '/admin/stories.php'],
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
                        <a href="<?php echo BASE_URL; ?>/admin/stories.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Stories
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
                        
                        <form id="storyForm" method="POST" action="" class="admin-edit-form">
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
                                            value="<?php echo e($story['title'] ?? ''); ?>" 
                                            required
                                        >
                                        <?php if (isset($errors['title']) && ($errors['title'] !== '' && $errors['title'] !== '0')): ?>
                                            <div class="invalid-feedback"><?php echo e($errors['title']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <div 
                                            id="descriptionEditor" 
                                            data-quill-editor
                                            data-placeholder="Enter story description..."
                                        ><?php echo $story['description'] ?? ''; ?></div>
                                        <input type="hidden" id="description" name="description" value="<?php echo e($story['description'] ?? ''); ?>">
                                        <?php if (isset($errors['description']) && ($errors['description'] !== '' && $errors['description'] !== '0')): ?>
                                            <div class="text-danger small mt-1"><?php echo e($errors['description']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                                <select 
                                                    class="form-select <?php echo empty($errors['category']) ? '' : 'is-invalid'; ?>" 
                                                    id="category" 
                                                    name="category" 
                                                    required
                                                >
                                                    <option value="">Select Category</option>
                                                    <option value="education" <?php echo ($story['category'] ?? '') === 'education' ? 'selected' : ''; ?>>Education</option>
                                                    <option value="health" <?php echo ($story['category'] ?? '') === 'health' ? 'selected' : ''; ?>>Health</option>
                                                    <option value="livelihood" <?php echo ($story['category'] ?? '') === 'livelihood' ? 'selected' : ''; ?>>Livelihood</option>
                                                    <option value="empowerment" <?php echo ($story['category'] ?? '') === 'empowerment' ? 'selected' : ''; ?>>Empowerment</option>
                                                </select>
                                                <?php if (isset($errors['category']) && ($errors['category'] !== '' && $errors['category'] !== '0')): ?>
                                                    <div class="invalid-feedback"><?php echo e($errors['category']); ?></div>
                                                <?php endif;
 ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="draft" <?php echo ($story['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="published" <?php echo ($story['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                                    <option value="archived" <?php echo ($story['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Featured Image</label>
                                        
                                        <?php if (!empty($story['image'])): ?>
                                            <div class="current-image-container mb-3">
                                                <div class="current-image-label">
                                                    <i class="bi bi-image-fill me-2"></i>
                                                    <span>Current Image</span>
                                                </div>
                                                <div class="current-image-wrapper">
                                                    <img src="<?php echo getImageUrl($story['image']); ?>" alt="Current image" class="current-image-preview">
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
                                        <input type="hidden" id="image" name="image" value="<?php echo e($story['image'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/stories.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $storyId > 0 ? 'Update' : 'Create'; ?> Story
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
