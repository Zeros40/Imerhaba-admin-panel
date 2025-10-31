<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/includes/header.php';

Auth::requireRole('admin');

$pdo = db();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key !== 'action' && strpos($key, 'setting_') === 0) {
            $settingKey = str_replace('setting_', '', $key);
            updateSetting($settingKey, $value);
        }
    }

    Auth::logActivity($user['id'], 'update_settings', 'settings', null, 'Updated system settings');
    redirect('/settings', 'Settings updated successfully', 'success');
}

// Get all settings grouped by category
$stmt = $pdo->query("
    SELECT * FROM settings
    ORDER BY category, setting_key
");
$allSettings = $stmt->fetchAll();

$settingsByCategory = [];
foreach ($allSettings as $setting) {
    $category = $setting['category'] ?: 'general';
    if (!isset($settingsByCategory[$category])) {
        $settingsByCategory[$category] = [];
    }
    $settingsByCategory[$category][] = $setting;
}
?>

<style>
    .settings-grid {
        display: grid;
        gap: 24px;
    }

    .setting-item {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        padding: 16px;
        border-bottom: 1px solid var(--border);
    }

    .setting-item:last-child {
        border-bottom: none;
    }

    .setting-info h4 {
        font-size: 15px;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .setting-info p {
        font-size: 13px;
        color: var(--text-light);
    }

    .setting-control input,
    .setting-control select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .setting-control input[type="checkbox"] {
        width: auto;
        height: 20px;
        cursor: pointer;
    }

    .category-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary);
    }

    @media (max-width: 768px) {
        .setting-item {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h2>System Settings</h2>
</div>

<form method="POST" action="/settings">
    <input type="hidden" name="action" value="update">

    <div class="settings-grid">
        <?php foreach ($settingsByCategory as $category => $settings): ?>
            <div class="card">
                <h3 class="category-title"><?= e(ucfirst($category)) ?></h3>

                <?php foreach ($settings as $setting): ?>
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4><?= e(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?></h4>
                            <?php if ($setting['description']): ?>
                                <p><?= e($setting['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="setting-control">
                            <?php if ($setting['setting_type'] === 'boolean'): ?>
                                <input
                                    type="checkbox"
                                    name="setting_<?= e($setting['setting_key']) ?>"
                                    value="true"
                                    <?= filter_var($setting['setting_value'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' ?>
                                >
                            <?php elseif ($setting['setting_type'] === 'number'): ?>
                                <input
                                    type="number"
                                    name="setting_<?= e($setting['setting_key']) ?>"
                                    value="<?= e($setting['setting_value']) ?>"
                                >
                            <?php else: ?>
                                <input
                                    type="text"
                                    name="setting_<?= e($setting['setting_key']) ?>"
                                    value="<?= e($setting['setting_value']) ?>"
                                >
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 24px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            Save Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
