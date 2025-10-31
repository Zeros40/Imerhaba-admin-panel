<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'Invalid email address';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?
                    WHERE id = ?
                ");
                $stmt->execute([$firstName, $lastName, $email, $user['id']]);

                // Update session
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                $_SESSION['email'] = $email;

                Auth::logActivity($user['id'], 'update_profile', 'user', $user['id'], 'Updated profile information');
                redirect('/profile', 'Profile updated successfully', 'success');
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = 'Email already exists';
                } else {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errors[] = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $userData = $stmt->fetch();

            if (!password_verify($currentPassword, $userData['password'])) {
                $errors[] = 'Current password is incorrect';
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([Auth::hashPassword($newPassword), $user['id']]);

                    Auth::logActivity($user['id'], 'change_password', 'user', $user['id'], 'Changed password');
                    redirect('/profile', 'Password changed successfully', 'success');
                } catch (PDOException $e) {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$currentUser = $stmt->fetch();
?>

<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    .profile-header {
        text-align: center;
        padding: 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
        margin-bottom: 24px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: white;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 700;
        margin: 0 auto 16px;
    }

    .profile-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .profile-email {
        font-size: 16px;
        opacity: 0.9;
    }

    .info-grid {
        display: grid;
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        background: var(--bg-light);
        border-radius: 8px;
    }

    .info-label {
        font-weight: 500;
        color: var(--text-dark);
    }

    .info-value {
        color: var(--text-light);
    }
</style>

<div class="profile-header">
    <div class="profile-avatar">
        <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
    </div>
    <div class="profile-name"><?= e($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?: e($currentUser['username']) ?></div>
    <div class="profile-email"><?= e($currentUser['email']) ?></div>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="profile-grid">
    <!-- Profile Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profile Information</h3>
        </div>

        <form method="POST" action="/profile">
            <input type="hidden" name="action" value="update_profile">

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?= e($currentUser['first_name']) ?>"
                >
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?= e($currentUser['last_name']) ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= e($currentUser['email']) ?>"
                >
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Update Profile
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Change Password</h3>
        </div>

        <form method="POST" action="/profile">
            <input type="hidden" name="action" value="change_password">

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-lock"></i>
                Change Password
            </button>
        </form>
    </div>
</div>

<!-- Account Information -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Account Information</h3>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Username</span>
            <span class="info-value"><?= e($currentUser['username']) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Role</span>
            <span class="info-value"><?= e(ucfirst($currentUser['role'])) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Status</span>
            <span class="info-value"><?= e(ucfirst($currentUser['status'])) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Member Since</span>
            <span class="info-value"><?= formatDate($currentUser['created_at'], 'F j, Y') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Last Login</span>
            <span class="info-value"><?= $currentUser['last_login'] ? formatDate($currentUser['last_login'], 'F j, Y g:i A') : 'Never' ?></span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
