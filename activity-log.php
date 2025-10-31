<?php
$pageTitle = 'Activity Log';
require_once __DIR__ . '/includes/header.php';

$pdo = db();

// Get activity log
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$userFilter = $_GET['user_id'] ?? '';
$actionFilter = $_GET['action'] ?? '';

$where = [];
$params = [];

if ($userFilter) {
    $where[] = "al.user_id = ?";
    $params[] = $userFilter;
}

if ($actionFilter) {
    $where[] = "al.action = ?";
    $params[] = $actionFilter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activity_log al $whereClause");
$stmt->execute($params);
$totalActivities = $stmt->fetch()['count'];

$pagination = paginate($totalActivities, $page, $perPage);

// Get activity log
$stmt = $pdo->prepare("
    SELECT al.*, u.username, u.first_name, u.last_name
    FROM activity_log al
    LEFT JOIN users u ON al.user_id = u.id
    $whereClause
    ORDER BY al.created_at DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
");
$stmt->execute($params);
$activities = $stmt->fetchAll();

// Get all users for filter
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
$allUsers = $stmt->fetchAll();

// Get unique actions
$stmt = $pdo->query("SELECT DISTINCT action FROM activity_log ORDER BY action");
$allActions = $stmt->fetchAll();
?>

<style>
    .activity-table {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .activity-row {
        display: grid;
        grid-template-columns: auto 150px 120px 1fr 150px;
        gap: 16px;
        padding: 16px;
        border-bottom: 1px solid var(--border);
        align-items: start;
    }

    .activity-row:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }

    .activity-icon.login { background: var(--success); }
    .activity-icon.logout { background: var(--text-light); }
    .activity-icon.create { background: var(--primary); }
    .activity-icon.update { background: var(--warning); }
    .activity-icon.delete { background: var(--danger); }
    .activity-icon.default { background: var(--info); }

    .activity-user {
        font-weight: 500;
        color: var(--text-dark);
    }

    .activity-action {
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 12px;
        background: var(--bg-light);
        color: var(--text-dark);
        font-weight: 500;
    }

    .activity-description {
        font-size: 14px;
        color: var(--text-light);
    }

    .activity-time {
        font-size: 13px;
        color: var(--text-light);
        text-align: right;
    }

    @media (max-width: 768px) {
        .activity-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }
    }
</style>

<div class="page-header">
    <h2>Activity Log</h2>
</div>

<!-- Filters -->
<form method="GET" action="/activity-log">
    <div class="filters">
        <select name="user_id" class="filter-select">
            <option value="">All Users</option>
            <?php foreach ($allUsers as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $userFilter == $u['id'] ? 'selected' : '' ?>>
                    <?= e($u['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="action" class="filter-select">
            <option value="">All Actions</option>
            <?php foreach ($allActions as $a): ?>
                <option value="<?= e($a['action']) ?>" <?= $actionFilter === $a['action'] ? 'selected' : '' ?>>
                    <?= e($a['action']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i>
            Filter
        </button>

        <?php if ($userFilter || $actionFilter): ?>
            <a href="/activity-log" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Clear
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- Activity Log -->
<div class="activity-table">
    <?php if (count($activities) > 0): ?>
        <?php foreach ($activities as $activity): ?>
            <?php
            $iconClass = 'default';
            $icon = 'fa-circle';

            if (strpos($activity['action'], 'login') !== false) {
                $iconClass = 'login';
                $icon = 'fa-sign-in-alt';
            } elseif (strpos($activity['action'], 'logout') !== false) {
                $iconClass = 'logout';
                $icon = 'fa-sign-out-alt';
            } elseif (strpos($activity['action'], 'create') !== false) {
                $iconClass = 'create';
                $icon = 'fa-plus';
            } elseif (strpos($activity['action'], 'update') !== false) {
                $iconClass = 'update';
                $icon = 'fa-edit';
            } elseif (strpos($activity['action'], 'delete') !== false) {
                $iconClass = 'delete';
                $icon = 'fa-trash';
            }
            ?>
            <div class="activity-row">
                <div class="activity-icon <?= $iconClass ?>">
                    <i class="fas <?= $icon ?>"></i>
                </div>

                <div class="activity-user">
                    <?php if ($activity['username']): ?>
                        <?= e($activity['first_name'] ? $activity['first_name'] . ' ' . $activity['last_name'] : $activity['username']) ?>
                    <?php else: ?>
                        <em>System</em>
                    <?php endif; ?>
                </div>

                <div class="activity-action">
                    <?= e($activity['action']) ?>
                </div>

                <div class="activity-description">
                    <?= e($activity['description'] ?: '-') ?>
                    <?php if ($activity['entity_type']): ?>
                        <br><small>(<?= e($activity['entity_type']) ?> #<?= e($activity['entity_id']) ?>)</small>
                    <?php endif; ?>
                </div>

                <div class="activity-time">
                    <?= timeAgo($activity['created_at']) ?><br>
                    <small><?= formatDate($activity['created_at'], 'M d, g:i A') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <p>No activity found</p>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
    <div class="pagination">
        <?php if ($pagination['has_previous']): ?>
            <a href="?page=<?= $page - 1 ?>&user_id=<?= urlencode($userFilter) ?>&action=<?= urlencode($actionFilter) ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&user_id=<?= urlencode($userFilter) ?>&action=<?= urlencode($actionFilter) ?>">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $page + 1 ?>&user_id=<?= urlencode($userFilter) ?>&action=<?= urlencode($actionFilter) ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
