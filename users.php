<?php
$pageTitle = 'User Management';
require_once __DIR__ . '/includes/header.php';

// Ensure admin role
Auth::requireRole('admin');

$pdo = db();
$action = $_GET['action'] ?? 'list';
$userId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'create') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        $errors = [];

        if (empty($username)) $errors[] = 'Username is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($password)) $errors[] = 'Password is required';
        if (!isValidEmail($email)) $errors[] = 'Invalid email address';

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, first_name, last_name, role, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $username,
                    $email,
                    Auth::hashPassword($password),
                    $firstName,
                    $lastName,
                    $role,
                    $status
                ]);

                $newUserId = $pdo->lastInsertId();
                Auth::logActivity($user['id'], 'create_user', 'user', $newUserId, "Created user: $username");
                redirect('/users', 'User created successfully', 'success');
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = 'Username or email already exists';
                } else {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    } elseif ($postAction === 'update' && $userId) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        $errors = [];

        if (empty($username)) $errors[] = 'Username is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (!isValidEmail($email)) $errors[] = 'Invalid email address';

        if (empty($errors)) {
            try {
                if (!empty($password)) {
                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET username = ?, email = ?, password = ?, first_name = ?, last_name = ?, role = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $username,
                        $email,
                        Auth::hashPassword($password),
                        $firstName,
                        $lastName,
                        $role,
                        $status,
                        $userId
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET username = ?, email = ?, first_name = ?, last_name = ?, role = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $username,
                        $email,
                        $firstName,
                        $lastName,
                        $role,
                        $status,
                        $userId
                    ]);
                }

                Auth::logActivity($user['id'], 'update_user', 'user', $userId, "Updated user: $username");
                redirect('/users', 'User updated successfully', 'success');
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = 'Username or email already exists';
                } else {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    } elseif ($postAction === 'delete' && $userId) {
        // Don't allow deleting yourself
        if ($userId == $user['id']) {
            redirect('/users', 'You cannot delete your own account', 'error');
        }

        try {
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $deletedUser = $stmt->fetch();

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            Auth::logActivity($user['id'], 'delete_user', 'user', $userId, "Deleted user: " . $deletedUser['username']);
            redirect('/users', 'User deleted successfully', 'success');
        } catch (PDOException $e) {
            redirect('/users', 'Error deleting user: ' . $e->getMessage(), 'error');
        }
    }
}

// Get users list
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if ($roleFilter) {
    $where[] = "role = ?";
    $params[] = $roleFilter;
}

if ($statusFilter) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users $whereClause");
$stmt->execute($params);
$totalUsers = $stmt->fetch()['count'];

$pagination = paginate($totalUsers, $page, $perPage);

// Get users
$stmt = $pdo->prepare("
    SELECT * FROM users
    $whereClause
    ORDER BY created_at DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
");
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get single user for edit
$editUser = null;
if ($action === 'edit' && $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $editUser = $stmt->fetch();
}
?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .filters {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .filter-input, .filter-select {
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .filter-input {
        min-width: 250px;
    }

    .filter-select {
        min-width: 150px;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .table-actions {
        display: flex;
        gap: 8px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 24px;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-decoration: none;
        color: var(--text-dark);
        font-size: 14px;
    }

    .pagination a:hover {
        background: var(--bg-light);
    }

    .pagination .active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($action === 'list'): ?>
    <div class="page-header">
        <h2>Users</h2>
        <a href="/users?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add New User
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/users">
        <div class="filters">
            <input
                type="text"
                name="search"
                class="filter-input"
                placeholder="Search users..."
                value="<?= e($search) ?>"
            >
            <select name="role" class="filter-select">
                <option value="">All Roles</option>
                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="manager" <?= $roleFilter === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>User</option>
            </select>
            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Filter
            </button>
            <?php if ($search || $roleFilter || $statusFilter): ?>
                <a href="/users" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Users Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <strong><?= e($u['username']) ?></strong><br>
                                <?php if ($u['first_name'] || $u['last_name']): ?>
                                    <small style="color: var(--text-light);">
                                        <?= e(trim($u['first_name'] . ' ' . $u['last_name'])) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                    <?= e(ucfirst($u['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'warning' ?>">
                                    <?= e(ucfirst($u['status'])) ?>
                                </span>
                            </td>
                            <td><?= formatDate($u['created_at'], 'M d, Y') ?></td>
                            <td><?= $u['last_login'] ? timeAgo($u['last_login']) : '-' ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="/users?action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($u['id'] != $user['id']): ?>
                                        <form method="POST" action="/users?id=<?= $u['id'] ?>" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger confirm-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-light);">
                            No users found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_previous']): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>&status=<?= urlencode($statusFilter) ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>&status=<?= urlencode($statusFilter) ?>">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>&status=<?= urlencode($statusFilter) ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php elseif ($action === 'create'): ?>
    <div class="page-header">
        <h2>Create New User</h2>
    </div>

    <?php if (isset($errors) && count($errors) > 0): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="/users">
            <input type="hidden" name="action" value="create">

            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required value="<?= e($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= e($_POST['first_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= e($_POST['last_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="user">User</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create User
                </button>
                <a href="/users" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>

<?php elseif ($action === 'edit' && $editUser): ?>
    <div class="page-header">
        <h2>Edit User: <?= e($editUser['username']) ?></h2>
    </div>

    <?php if (isset($errors) && count($errors) > 0): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="/users?id=<?= $editUser['id'] ?>">
            <input type="hidden" name="action" value="update">

            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required value="<?= e($editUser['username']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required value="<?= e($editUser['email']) ?>">
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= e($editUser['first_name']) ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= e($editUser['last_name']) ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="user" <?= $editUser['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="manager" <?= $editUser['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?= $editUser['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $editUser['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="suspended" <?= $editUser['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update User
                </button>
                <a href="/users" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>User not found</span>
    </div>
    <a href="/users" class="btn btn-primary">Back to Users</a>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
