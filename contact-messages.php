<?php
$pageTitle = 'Contact Messages';
require_once __DIR__ . '/includes/header.php';

$pdo = db();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $messageId = $_POST['id'] ?? null;

    if ($action === 'update_status' && $messageId) {
        $status = $_POST['status'] ?? 'read';
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            $stmt->execute([$status, $messageId]);
            Auth::logActivity($user['id'], 'update_message_status', 'contact_message', $messageId, "Updated message status to: $status");
            redirect('/contact-messages', 'Message status updated', 'success');
        } catch (PDOException $e) {
            redirect('/contact-messages', 'Error updating message: ' . $e->getMessage(), 'error');
        }
    } elseif ($action === 'delete' && $messageId) {
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$messageId]);
            Auth::logActivity($user['id'], 'delete_message', 'contact_message', $messageId, 'Deleted contact message');
            redirect('/contact-messages', 'Message deleted successfully', 'success');
        } catch (PDOException $e) {
            redirect('/contact-messages', 'Error deleting message: ' . $e->getMessage(), 'error');
        }
    }
}

// Get messages
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$statusFilter = $_GET['status'] ?? '';

$where = $statusFilter ? "WHERE status = ?" : "";
$params = $statusFilter ? [$statusFilter] : [];

// Count total
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contact_messages $where");
$stmt->execute($params);
$totalMessages = $stmt->fetch()['count'];

$pagination = paginate($totalMessages, $page, $perPage);

// Get messages
$stmt = $pdo->prepare("
    SELECT * FROM contact_messages
    $where
    ORDER BY created_at DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
");
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Get single message for view
$viewMessage = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$_GET['view']]);
    $viewMessage = $stmt->fetch();

    // Mark as read
    if ($viewMessage && $viewMessage['status'] === 'new') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$viewMessage['id']]);
    }
}
?>

<style>
    .message-list {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .message-item {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 16px;
        align-items: center;
        transition: background 0.2s;
        cursor: pointer;
    }

    .message-item:hover {
        background: var(--bg-light);
    }

    .message-item.unread {
        background: #f0f9ff;
    }

    .message-status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .message-status-indicator.new {
        background: var(--primary);
    }

    .message-status-indicator.read {
        background: var(--text-light);
    }

    .message-info h4 {
        font-size: 15px;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .message-info p {
        font-size: 13px;
        color: var(--text-light);
        margin: 0;
    }

    .message-preview {
        font-size: 14px;
        color: var(--text-light);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }

    .message-detail {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 24px;
    }

    .message-header {
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
    }

    .message-from {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }

    .message-from-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .message-from-info h3 {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .message-from-info p {
        font-size: 14px;
        color: var(--text-light);
        margin: 0;
    }

    .message-meta {
        display: flex;
        gap: 24px;
        font-size: 13px;
        color: var(--text-light);
        margin-top: 12px;
    }

    .message-body {
        line-height: 1.6;
        font-size: 15px;
        color: var(--text-dark);
        margin: 24px 0;
    }

    .message-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }
</style>

<?php if (!$viewMessage): ?>
    <div class="page-header">
        <h2>Contact Messages</h2>
    </div>

    <!-- Filters -->
    <div class="filters">
        <a href="/contact-messages" class="btn <?= !$statusFilter ? 'btn-primary' : 'btn-secondary' ?>">
            All
        </a>
        <a href="/contact-messages?status=new" class="btn <?= $statusFilter === 'new' ? 'btn-primary' : 'btn-secondary' ?>">
            New
        </a>
        <a href="/contact-messages?status=read" class="btn <?= $statusFilter === 'read' ? 'btn-primary' : 'btn-secondary' ?>">
            Read
        </a>
        <a href="/contact-messages?status=replied" class="btn <?= $statusFilter === 'replied' ? 'btn-primary' : 'btn-secondary' ?>">
            Replied
        </a>
        <a href="/contact-messages?status=archived" class="btn <?= $statusFilter === 'archived' ? 'btn-primary' : 'btn-secondary' ?>">
            Archived
        </a>
    </div>

    <div class="message-list">
        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $msg): ?>
                <a href="/contact-messages?view=<?= $msg['id'] ?>" style="text-decoration: none; color: inherit;">
                    <div class="message-item <?= $msg['status'] === 'new' ? 'unread' : '' ?>">
                        <div class="message-status-indicator <?= e($msg['status']) ?>"></div>
                        <div class="message-info">
                            <h4><?= e($msg['name']) ?></h4>
                            <p><?= e($msg['email']) ?></p>
                        </div>
                        <div class="message-preview">
                            <?= e(truncate($msg['message'], 60)) ?>
                        </div>
                        <div style="text-align: right; font-size: 13px; color: var(--text-light);">
                            <?= timeAgo($msg['created_at']) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-envelope-open"></i>
                <p>No messages found</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_previous']): ?>
                <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($statusFilter) ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($statusFilter) ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php elseif ($viewMessage): ?>
    <div class="page-header">
        <a href="/contact-messages" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Messages
        </a>
    </div>

    <div class="message-detail">
        <div class="message-header">
            <div class="message-from">
                <div class="message-from-avatar">
                    <?= strtoupper(substr($viewMessage['name'], 0, 1)) ?>
                </div>
                <div class="message-from-info">
                    <h3><?= e($viewMessage['name']) ?></h3>
                    <p><?= e($viewMessage['email']) ?></p>
                </div>
            </div>

            <div class="message-meta">
                <span><i class="fas fa-calendar"></i> <?= formatDate($viewMessage['created_at'], 'F j, Y g:i A') ?></span>
                <span><i class="fas fa-globe"></i> <?= e($viewMessage['ip_address']) ?></span>
                <span class="badge badge-<?= $viewMessage['status'] === 'new' ? 'info' : 'secondary' ?>">
                    <?= e(ucfirst($viewMessage['status'])) ?>
                </span>
            </div>
        </div>

        <?php if ($viewMessage['subject']): ?>
            <h4 style="margin-bottom: 16px;">Subject: <?= e($viewMessage['subject']) ?></h4>
        <?php endif; ?>

        <div class="message-body">
            <?= nl2br(e($viewMessage['message'])) ?>
        </div>

        <div class="message-actions">
            <form method="POST" action="/contact-messages" style="display: inline;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="<?= $viewMessage['id'] ?>">
                <input type="hidden" name="status" value="replied">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-reply"></i>
                    Mark as Replied
                </button>
            </form>

            <form method="POST" action="/contact-messages" style="display: inline;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="<?= $viewMessage['id'] ?>">
                <input type="hidden" name="status" value="archived">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-archive"></i>
                    Archive
                </button>
            </form>

            <form method="POST" action="/contact-messages" style="display: inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $viewMessage['id'] ?>">
                <button type="submit" class="btn btn-danger confirm-delete">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            </form>

            <a href="mailto:<?= e($viewMessage['email']) ?>" class="btn btn-primary">
                <i class="fas fa-envelope"></i>
                Reply via Email
            </a>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>Message not found</span>
    </div>
    <a href="/contact-messages" class="btn btn-primary">Back to Messages</a>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
