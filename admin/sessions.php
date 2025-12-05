<?php
/**
 * Sessions Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\DatabaseService;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$pageTitle = 'Active Sessions';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Sessions', 'url' => BASE_URL . '/admin/sessions.php'],
];

$sessions = [];
$sessionStats = [
    'total' => 0,
    'active' => 0,
    'expired' => 0,
    'unique_users' => 0,
];
$sessionMessage = '';
$sessionError = '';
$hasSessionsTable = false;
$connection = null;

try {
    $connection = DatabaseService::getConnection();
    $schemaManager = method_exists($connection, 'createSchemaManager')
        ? $connection->createSchemaManager()
        : $connection->getSchemaManager();
    $tableNames = array_map('strtolower', $schemaManager->listTableNames());
    $hasSessionsTable = in_array('users_sessions', $tableNames, true);
} catch (\Throwable $throwable) {
    $sessionError = 'Unable to connect to the database: ' . $throwable->getMessage();
}

if ($hasSessionsTable && $connection instanceof \Doctrine\DBAL\Connection) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
        if (! csrf_validate($token, 'sessions')) {
            $sessionError = 'Invalid security token. Please try again.';
        } else {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'terminate_session') {
                    $sessionId = (int) ($_POST['session_id'] ?? 0);
                    if ($sessionId > 0) {
                        $connection->delete('users_sessions', ['id' => $sessionId]);
                        $sessionMessage = 'Session terminated successfully.';
                    }
                } elseif ($action === 'terminate_expired') {
                    $deleted = $connection->executeStatement(
                        'DELETE FROM users_sessions WHERE expires IS NOT NULL AND expires < :now',
                        ['now' => time()]
                    );
                    $sessionMessage = $deleted > 0
                        ? $deleted . ' expired session' . ($deleted === 1 ? '' : 's') . ' removed.'
                        : 'No expired sessions to clear.';
                }
            } catch (\Throwable $e) {
                $sessionError = 'Unable to update sessions: ' . $e->getMessage();
            }
        }
    }

    if ($sessionError === '') {
        try {
            $sessions = $connection->createQueryBuilder()
                ->select(
                    'us.id',
                    'us.user_id',
                    'us.ip_address',
                    'us.user_agent',
                    'us.created',
                    'us.last_used',
                    'us.expires',
                    'u.email',
                    'u.username',
                    'u.last_login'
                )
                ->from('users_sessions', 'us')
                ->leftJoin('us', 'users', 'u', 'u.id = us.user_id')
                ->orderBy('us.last_used', 'DESC')
                ->fetchAllAssociative();

            $now = time();
            $sessionStats['total'] = count($sessions);
            $sessionStats['expired'] = count(array_filter($sessions, static fn($session): bool => isset($session['expires']) && (int) $session['expires'] > 0 && (int) $session['expires'] < $now));
            $sessionStats['active'] = $sessionStats['total'] - $sessionStats['expired'];
            $sessionStats['unique_users'] = count(array_unique(array_filter(array_column($sessions, 'user_id'))));
        } catch (\Throwable $e) {
            $sessionError = 'Unable to load sessions: ' . $e->getMessage();
        }
    }
}

$formatTimestamp = static function (?int $timestamp, string $format = 'M j, Y g:i A'): ?string {
    if ($timestamp === null || $timestamp === 0) {
        return null;
    }

    try {
        $date = (new \DateTimeImmutable('@' . $timestamp))->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $date->format($format);
    } catch (\Throwable) {
        return null;
    }
};

$relativeTime = static function (?int $timestamp) use ($formatTimestamp): string {
    if ($timestamp === null || $timestamp === 0) {
        return '—';
    }

    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 60) {
        return 'moments ago';
    }
    
    if ($diff < 3600) {
        $minutes = max(1, floor($diff / 60));
        return $minutes . ' min ago';
    }
    
    if ($diff < 86400) {
        $hours = max(1, floor($diff / 3600));
        return $hours . ' hr ago';
    }

    return $formatTimestamp($timestamp) ?? '—';
};

$deviceFromAgent = static function (?string $agent): string {
    if ($agent === null || $agent === '' || $agent === '0') {
        return 'Unknown device';
    }

    $agent = strtolower($agent);
    if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
        return 'Mobile device';
    }
    
    if (str_contains($agent, 'tablet') || str_contains($agent, 'ipad')) {
        return 'Tablet device';
    }

    return 'Desktop browser';
};

// Include header and sidebar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <!-- Hero Area -->
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="container-fluid">
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <p class="summary-label text-muted mb-1">Total Sessions</p>
                        <h3 class="summary-value mb-0"><?php echo number_format($sessionStats['total']); ?></h3>
                        <small class="text-muted">Across <?php echo max(1, $sessionStats['unique_users']); ?> admin(s)</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <p class="summary-label text-muted mb-1">Active Right Now</p>
                        <h3 class="summary-value text-success mb-0"><?php echo number_format($sessionStats['active']); ?></h3>
                        <small class="text-muted">Sessions still valid</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <p class="summary-label text-muted mb-1">Expired Sessions</p>
                        <h3 class="summary-value text-warning mb-0"><?php echo number_format($sessionStats['expired']); ?></h3>
                        <small class="text-muted">Pending cleanup</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <p class="summary-label text-muted mb-1">Last Refresh</p>
                        <h6 class="mb-0"><?php echo date('M j, Y g:i A'); ?></h6>
                        <small class="text-muted">Page refresh updates data</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <h4 class="mb-0">Active Sessions</h4>
                        <small class="text-muted">Track logins powered by Delight Auth sessions table</small>
                    </div>
                    <?php if ($hasSessionsTable): ?>
                    <form method="POST" class="ms-auto d-flex align-items-center gap-2">
                        <?php echo csrf_field('sessions'); ?>
                        <input type="hidden" name="action" value="terminate_expired">
                        <button 
                            type="submit" 
                            class="btn btn-outline-danger btn-sm" 
                            <?php echo $sessionStats['expired'] === 0 ? 'disabled' : ''; ?>
                            data-delete-confirm="Remove all expired sessions?"
                        >
                            <i class="bi bi-trash me-1"></i>Clear expired
                        </button>
                    </form>
                    <?php endif;
 ?>
                </div>
                <div class="card-body">
                    <?php if ($sessionMessage !== '' && $sessionMessage !== '0'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo e($sessionMessage); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif;
 ?>

                    <?php if ($sessionError !== '' && $sessionError !== '0'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e($sessionError); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif;
 ?>

                    <?php if (! $hasSessionsTable): ?>
                        <div class="alert alert-warning mb-0">
                            <strong>Session table missing.</strong> We could not find the <code>users_sessions</code> table. Make sure Delight Auth migrations have been applied so session data can be monitored.
                        </div>
                    <?php elseif (empty($sessions)): ?>
                        <p class="text-muted mb-0">No sessions found. Once administrators log in you will see them listed here.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th>Last Activity</th>
                                        <th>Created</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $session): 
                                        $createdTs = isset($session['created']) ? (int) $session['created'] : null;
                                        $lastUsedTs = isset($session['last_used']) ? (int) $session['last_used'] : null;
                                        $expiresTs = isset($session['expires']) ? (int) $session['expires'] : null;
                                        $isExpired = $expiresTs && $expiresTs < time();
                                        $statusLabel = $isExpired ? 'Expired' : 'Active';
                                        $statusClass = $isExpired ? 'badge bg-secondary' : 'badge bg-success';
                                        $sessionId = (int) ($session['id'] ?? 0);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?php echo e($session['username'] ?? 'Unknown User'); ?></div>
                                            <div class="text-muted small"><?php echo e($session['email'] ?? '—'); ?></div>
                                        </td>
                                        <td>
                                            <span class="fw-medium"><?php echo e($session['ip_address'] ?? '—'); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-medium"><?php echo e($deviceFromAgent($session['user_agent'] ?? null)); ?></div>
                                            <div class="text-muted small">
                                                <?php 
                                                    $agent = $session['user_agent'] ?? '—';
                                                    $agentLabel = function_exists('mb_strimwidth')
                                                        ? mb_strimwidth((string) $agent, 0, 48, strlen((string) $agent) > 48 ? '…' : '')
                                                        : (strlen((string) $agent) > 48 ? substr((string) $agent, 0, 48) . '…' : $agent);
                                                    echo e($agentLabel);
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium"><?php echo e($relativeTime($lastUsedTs)); ?></div>
                                            <div class="text-muted small"><?php echo e($formatTimestamp($lastUsedTs) ?? '—'); ?></div>
                                        </td>
                                        <td><?php echo e($formatTimestamp($createdTs) ?? '—'); ?></td>
                                        <td><?php echo e($formatTimestamp($expiresTs) ?? '—'); ?></td>
                                        <td><span class="<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                                        <td class="text-end">
                                            <?php if ($sessionId > 0): ?>
                                            <form method="POST" class="d-inline">
                                                <?php echo csrf_field('sessions'); ?>
                                                <input type="hidden" name="action" value="terminate_session">
                                                <input type="hidden" name="session_id" value="<?php echo $sessionId; ?>">
                                                <button
                                                    type="submit"
                                                    class="btn btn-link text-danger p-0"
                                                    data-delete-confirm="Terminate this session?"
                                                >
                                                    <i class="bi bi-x-circle me-1"></i>Terminate
                                                </button>
                                            </form>
                                            <?php endif;
                                                 ?>
                                        </td>
                                    </tr>
<?php endforeach;
 ?>
                                </tbody>
                            </table>
                        </div>
<?php endif;
 ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

