<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

// Get statistics
try {
    $pdo = db();

    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $stmt->fetch()['count'] ?? 0;

    // Count active sessions
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions WHERE expires_at > NOW()");
    $activeSessions = $stmt->fetch()['count'] ?? 0;

    // Count contact messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
    $totalMessages = $stmt->fetch()['count'] ?? 0;

    // Count unread messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'");
    $unreadMessages = $stmt->fetch()['count'] ?? 0;

    // Get recent activity
    $stmt = $pdo->query("
        SELECT al.*, u.username, u.first_name, u.last_name
        FROM activity_log al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 10
    ");
    $recentActivity = $stmt->fetchAll();

    // Get recent users
    $stmt = $pdo->query("
        SELECT id, username, email, first_name, last_name, role, created_at
        FROM users
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $recentUsers = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $totalUsers = $activeSessions = $totalMessages = $unreadMessages = 0;
    $recentActivity = $recentUsers = [];
}
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.2s;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-light);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-light);
        border-bottom: 2px solid var(--border);
    }

    td {
        padding: 12px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-secondary { background: #f1f5f9; color: #475569; }

    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .activity-user {
        font-weight: 500;
        color: var(--text-dark);
    }

    .activity-time {
        font-size: 12px;
        color: var(--text-light);
    }

    .activity-description {
        font-size: 13px;
        color: var(--text-light);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?= number_format($totalUsers) ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Active Sessions</div>
            <div class="stat-value"><?= number_format($activeSessions) ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Messages</div>
            <div class="stat-value"><?= number_format($totalMessages) ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-envelope-open-text"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Unread Messages</div>
            <div class="stat-value"><?= number_format($unreadMessages) ?></div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
        </div>

        <?php if (count($recentActivity) > 0): ?>
            <?php foreach ($recentActivity as $activity): ?>
                <div class="activity-item">
                    <div class="activity-header">
                        <span class="activity-user">
                            <?= e($activity['first_name'] ? $activity['first_name'] . ' ' . $activity['last_name'] : $activity['username'] ?? 'System') ?>
                        </span>
                        <span class="activity-time"><?= timeAgo($activity['created_at']) ?></span>
                    </div>
                    <div class="activity-description">
                        <strong><?= e($activity['action']) ?></strong>
                        <?php if ($activity['description']): ?>
                            - <?= e($activity['description']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>No recent activity</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Users</h3>
        </div>

        <?php if (count($recentUsers) > 0): ?>
            <div class="table-container">
                <table>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td>
                                    <strong><?= e($user['username']) ?></strong><br>
                                    <small style="color: var(--text-light);"><?= e($user['email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                        <?= e(ucfirst($user['role'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No users yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="/users?action=create" class="btn btn-primary">
            <i class="fas fa-user-plus"></i>
            Add New User
        </a>
        <a href="/contact-messages" class="btn btn-primary">
            <i class="fas fa-envelope"></i>
            View Messages
        </a>
        <a href="/settings" class="btn btn-primary">
            <i class="fas fa-cog"></i>
            System Settings
        </a>
        <a href="/activity-log" class="btn btn-primary">
            <i class="fas fa-history"></i>
            View Activity Log
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
