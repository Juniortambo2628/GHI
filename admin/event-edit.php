<?php
/**
 * Event Edit Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Event;
use GHI\Models\Initiative;
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

$eventModel = new Event();
$initiativeModel = new Initiative();
$event = null;
$errors = [];
$success = false;

// Get all initiatives for dropdown
$allInitiatives = $initiativeModel->all(['status' => 'published'], 'title ASC');

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load event if editing
if ($eventId > 0) {
    $event = $eventModel->find($eventId);
    if ($event === null || $event === []) {
        header('Location: ' . BASE_URL . '/admin/events.php');
        exit;
    }
}

// Check if this is a modal request
$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';

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
        $initiativeId = empty($_POST['initiative_id']) ? null : (int)$_POST['initiative_id'];
        $eventDate = trim($_POST['event_date'] ?? '');
        $eventTime = trim($_POST['event_time'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = trim($_POST['status'] ?? 'draft');
        $image = trim((string) ($_POST['image'] ?? $event['image'] ?? ''));
        
        // Combine date and time
        $eventDateTime = $eventDate . ($eventTime !== '' && $eventTime !== '0' ? ' ' . $eventTime : ' 00:00:00');
        
        // Validate
        if ($title === '' || $title === '0') {
            $errors['title'] = 'Title is required';
        } elseif (strlen($title) < 3) {
            $errors['title'] = 'Title must be at least 3 characters';
        }
        
        if ($description === '' || $description === '0') {
            $errors['description'] = 'Description is required';
        }
        
        if ($eventDate === '' || $eventDate === '0') {
            $errors['event_date'] = 'Event date is required';
        }
        
        if ($location === '' || $location === '0') {
            $errors['location'] = 'Location is required';
        }
        
        // If no errors, save
        if ($errors === []) {
            try {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'initiative_id' => $initiativeId,
                    'event_date' => $eventDateTime,
                    'location' => $location,
                    'status' => $status,
                    'image' => $image,
                ];
                
                if ($eventId > 0) {
                    // Update existing
                    $eventModel->update($eventId, $data);
                    $success = 'Event updated successfully!';
                    log_message('info', 'Event updated', ['event_id' => $eventId]);
                } else {
                    // Create new
                    $eventId = $eventModel->create($data);
                    $success = 'Event created successfully!';
                    
                    // Dispatch event
                    $eventObj = new ContentCreatedEvent('event', $eventId, ['title' => $title]);
                    event_dispatch($eventObj, ContentCreatedEvent::NAME);
                    
                    log_message('info', 'Event created', ['event_id' => $eventId]);
                    
                    // Redirect to edit page
                    header('Location: ' . BASE_URL . '/admin/event-edit.php?id=' . $eventId . '&success=1');
                    exit;
                }
                
                // Reload event data
                $event = $eventModel->find($eventId);
            } catch (\Exception $e) {
                $errors['general'] = 'An error occurred: ' . $e->getMessage();
                log_message('error', 'Event save failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

// Parse event date/time for form
$eventDateFormatted = '';
$eventTimeFormatted = '';
if (!empty($event['event_date'])) {
    $dateTime = new DateTime($event['event_date']);
    $eventDateFormatted = $dateTime->format('Y-m-d');
    $eventTimeFormatted = $dateTime->format('H:i');
}

// Set page variables
$pageTitle = $eventId > 0 ? 'Edit Event' : 'Create Event';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Events', 'url' => BASE_URL . '/admin/events.php'],
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
                            <a href="<?php echo BASE_URL; ?>/admin/events.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Events
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
                        
                        <form id="eventForm" method="POST" action="" class="admin-edit-form">
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
                                            value="<?php echo e($event['title'] ?? ''); ?>" 
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
                                            data-placeholder="Enter event description..."
                                        ><?php echo $event['description'] ?? ''; ?></div>
                                        <input type="hidden" id="description" name="description" value="<?php echo e($event['description'] ?? ''); ?>">
                                        <?php if (isset($errors['description']) && ($errors['description'] !== '' && $errors['description'] !== '0')): ?>
                                            <div class="text-danger small mt-1"><?php echo e($errors['description']); ?></div>
                                        <?php endif;
 ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="initiative_id" class="form-label">Initiative</label>
                                                <select class="form-select" id="initiative_id" name="initiative_id">
                                                    <option value="">Select Initiative (Optional)</option>
                                                    <?php foreach ($allInitiatives as $init): ?>
                                                        <option value="<?php echo $init['id']; ?>" <?php echo ($event['initiative_id'] ?? '') == $init['id'] ? 'selected' : ''; ?>>
                                                            <?php echo e($init['title']); ?>
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
                                                    <option value="draft" <?php echo ($event['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    <option value="published" <?php echo ($event['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                                    <option value="archived" <?php echo ($event['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                                <input 
                                                    type="date" 
                                                    class="form-control <?php echo empty($errors['event_date']) ? '' : 'is-invalid'; ?>" 
                                                    id="event_date" 
                                                    name="event_date" 
                                                    value="<?php echo e($eventDateFormatted); ?>" 
                                                    required
                                                >
                                                <?php if (isset($errors['event_date']) && ($errors['event_date'] !== '' && $errors['event_date'] !== '0')): ?>
                                                    <div class="invalid-feedback"><?php echo e($errors['event_date']); ?></div>
                                                <?php endif;
 ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="event_time" class="form-label">Event Time</label>
                                                <input 
                                                    type="time" 
                                                    class="form-control" 
                                                    id="event_time" 
                                                    name="event_time" 
                                                    value="<?php echo e($eventTimeFormatted); ?>"
                                                >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                                <input 
                                                    type="text" 
                                                    class="form-control <?php echo empty($errors['location']) ? '' : 'is-invalid'; ?>" 
                                                    id="location" 
                                                    name="location" 
                                                    value="<?php echo e($event['location'] ?? ''); ?>" 
                                                    required
                                                >
                                                <?php if (isset($errors['location']) && ($errors['location'] !== '' && $errors['location'] !== '0')): ?>
                                                    <div class="invalid-feedback"><?php echo e($errors['location']); ?></div>
                                                <?php endif;
 ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Featured Image</label>
                                        
                                        <?php if (!empty($event['image'])): ?>
                                            <div class="current-image-container mb-3">
                                                <div class="current-image-label">
                                                    <i class="bi bi-image-fill me-2"></i>
                                                    <span>Current Image</span>
                                                </div>
                                                <div class="current-image-wrapper">
                                                    <img src="<?php echo getImageUrl($event['image']); ?>" alt="Current image" class="current-image-preview">
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
                                        <input type="hidden" id="image" name="image" value="<?php echo e($event['image'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/events.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $eventId > 0 ? 'Update' : 'Create'; ?> Event
                                </button>
                            </div>
                        </form>
                    </div>
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
