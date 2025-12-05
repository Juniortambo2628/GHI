<?php
/**
 * Impact Activity Edit Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\ImpactActivity;
use GHI\Models\Event;
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

$impactModel = new ImpactActivity();
$eventModel = new Event();
$impact = null;
$errors = [];
$success = false;

// Get all events for dropdown
$allEvents = $eventModel->all(['status' => 'published'], 'event_date DESC');

// Get impact ID
$impactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load impact if editing
if ($impactId > 0) {
    $impact = $impactModel->find($impactId);
    if ($impact === null || $impact === []) {
        header('Location: ' . BASE_URL . '/admin/impact-stories.php');
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
        $eventId = empty($_POST['event_id']) ? null : (int)$_POST['event_id'];
        $peopleAffected = empty($_POST['people_affected']) ? 0 : (int)$_POST['people_affected'];
        $displayOrder = empty($_POST['display_order']) ? 0 : (int)$_POST['display_order'];
        $status = trim($_POST['status'] ?? 'draft');
        $thumbnail = trim((string) ($_POST['thumbnail'] ?? $impact['thumbnail'] ?? ''));
        
        // Validate
        if ($title === '' || $title === '0') {
            $errors['title'] = 'Title is required';
        } elseif (strlen($title) < 3) {
            $errors['title'] = 'Title must be at least 3 characters';
        }
        
        if ($description === '' || $description === '0') {
            $errors['description'] = 'Description is required';
        }
        
        if ($peopleAffected < 0) {
            $errors['people_affected'] = 'People affected must be a positive number';
        }
        
        // If no errors, save
        if ($errors === []) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'event_id' => $eventId,
                    'people_affected' => $peopleAffected,
                    'display_order' => $displayOrder,
                    'status' => $status,
                    'thumbnail' => $thumbnail,
                ];
                
                if ($impactId > 0) {
                    // Update existing
                    $impactModel->update($impactId, $data);
                    $success = 'Impact activity updated successfully!';
                    log_message('info', 'Impact activity updated', ['impact_id' => $impactId]);
                } else {
                    // Create new
                    $impactId = $impactModel->create($data);
                    $success = 'Impact activity created successfully!';
                    
                    // Dispatch event
                    $event = new ContentCreatedEvent('impact_activity', $impactId, ['title' => $title]);
                    event_dispatch($event, ContentCreatedEvent::NAME);
                    
                    log_message('info', 'Impact activity created', ['impact_id' => $impactId]);
                    
                    // Redirect to edit page
                    header('Location: ' . BASE_URL . '/admin/impact-edit.php?id=' . $impactId . '&success=1');
                    exit;
                }
                
                // Reload impact data
                $impact = $impactModel->find($impactId);
            } catch (\Exception $e) {
                $errors['general'] = 'An error occurred: ' . $e->getMessage();
                log_message('error', 'Impact activity save failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

// Set page variables
$pageTitle = $impactId > 0 ? 'Edit Impact Activity' : 'Create Impact Activity';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Impact Stories', 'url' => BASE_URL . '/admin/impact-stories.php'],
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
                        <a href="<?php echo BASE_URL; ?>/admin/impact-stories.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Impact Activities
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
                        
                        <form id="impactForm" method="POST" action="" class="admin-edit-form">
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
                                            value="<?php echo e($impact['title'] ?? ''); ?>" 
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
                                            data-placeholder="Enter impact activity description..."
                                        ><?php echo $impact['description'] ?? ''; ?></div>
                                        <input type="hidden" id="description" name="description" value="<?php echo e($impact['description'] ?? ''); ?>">
                                        <?php if (isset($errors['description']) && ($errors['description'] !== '' && $errors['description'] !== '0')): ?>
                                            <div class="text-danger small mt-1"><?php echo e($errors['description']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="event_id" class="form-label">Related Event</label>
                                                <select class="form-select" id="event_id" name="event_id">
                                                    <option value="">Select Event (Optional)</option>
                                                    <?php foreach ($allEvents as $event): ?>
                                                        <option value="<?php echo $event['id']; ?>" <?php echo ($impact['event_id'] ?? '') == $event['id'] ? 'selected' : ''; ?>>
                                                            <?php echo e($event['title']); ?> - <?php echo formatDate($event['event_date']); ?>
                                                        </option>
                                                    <?php endforeach;
 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="draft" <?php echo ($impact['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="published" <?php echo ($impact['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                                    <option value="archived" <?php echo ($impact['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="people_affected" class="form-label">People Affected</label>
                                                <input 
                                                    type="number" 
                                                    class="form-control <?php echo empty($errors['people_affected']) ? '' : 'is-invalid'; ?>" 
                                                    id="people_affected" 
                                                    name="people_affected" 
                                                    value="<?php echo e($impact['people_affected'] ?? 0); ?>" 
                                                    min="0"
                                                >
                                                <?php if (isset($errors['people_affected']) && ($errors['people_affected'] !== '' && $errors['people_affected'] !== '0')): ?>
                                                    <div class="invalid-feedback"><?php echo e($errors['people_affected']); ?></div>
                                                <?php endif;
 ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="display_order" class="form-label">Display Order</label>
                                                <input 
                                                    type="number" 
                                                    class="form-control" 
                                                    id="display_order" 
                                                    name="display_order" 
                                                    value="<?php echo e($impact['display_order'] ?? 0); ?>" 
                                                    min="0"
                                                >
                                                <small class="text-muted">Lower numbers appear first</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="thumbnail" class="form-label fw-semibold">Thumbnail Image</label>
                                        
                                        <?php if (!empty($impact['thumbnail'])): ?>
                                            <div class="current-image-container mb-3">
                                                <div class="current-image-label">
                                                    <i class="bi bi-image-fill me-2"></i>
                                                    <span>Current Image</span>
                                                </div>
                                                <div class="current-image-wrapper">
                                                    <img src="<?php echo getImageUrl($impact['thumbnail']); ?>" alt="Current thumbnail" class="current-image-preview">
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
                                                id="thumbnailUpload" 
                                                name="thumbnailUpload"
                                                accept="image/*"
                                            >
                                        </div>
                                        <input type="hidden" id="thumbnail" name="thumbnail" value="<?php echo e($impact['thumbnail'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/impact-stories.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $impactId > 0 ? 'Update' : 'Create'; ?> Impact Activity
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
