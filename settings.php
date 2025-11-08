<?php
require_once __DIR__ . '/db/config.php';
session_start();

$db = db();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = str_replace('setting_', '', $key);
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, is_encrypted)
                VALUES (:key, :value, :encrypted)
                ON DUPLICATE KEY UPDATE setting_value = :value
            ");
            $stmt->execute([
                'key' => $settingKey,
                'value' => $value,
                'encrypted' => in_array($settingKey, ['replicate_api_key', 'stability_api_key', 'openai_api_key']) ? 1 : 0
            ]);
        }
    }
    $success = true;
}

// Load current settings
$settings = [];
$result = $db->query("SELECT setting_key, setting_value FROM settings");
while ($row = $result->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Imerhaba Design Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --navy: #0B132B;
            --gold: #D9A441;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0B132B 0%, #1a2332 100%);
            color: #fff;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--gold), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input, select {
            width: 100%;
            padding: 0.9rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--gold);
        }

        .help-text {
            font-size: 0.85rem;
            opacity: 0.7;
            margin-top: 0.4rem;
            line-height: 1.4;
        }

        .help-text a {
            color: var(--gold);
            text-decoration: none;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        .save-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), #c28934);
            color: var(--navy);
            padding: 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(217, 164, 65, 0.4);
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .info-box {
            background: rgba(66, 153, 225, 0.1);
            border: 1px solid rgba(66, 153, 225, 0.3);
            color: #90cdf4;
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .info-box strong {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Settings</h1>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>

        <?php if (isset($success)): ?>
            <div class="success-message">✅ Settings saved successfully!</div>
        <?php endif; ?>

        <form method="POST">
            <!-- API Keys -->
            <div class="panel">
                <div class="section-title">🔑 API Keys</div>

                <div class="info-box">
                    <strong>About API Keys:</strong> These keys enable AI image generation.
                    Get your keys from the respective platforms and paste them here.
                    Keys are stored securely in the database.
                </div>

                <div class="form-group">
                    <label>Replicate API Key</label>
                    <input type="password" name="setting_replicate_api_key"
                           value="<?= htmlspecialchars($settings['replicate_api_key'] ?? '') ?>"
                           placeholder="r8_xxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                    <div class="help-text">
                        Get your key from <a href="https://replicate.com" target="_blank">replicate.com</a> →
                        Account → API Tokens
                    </div>
                </div>

                <div class="form-group">
                    <label>Stability AI API Key</label>
                    <input type="password" name="setting_stability_api_key"
                           value="<?= htmlspecialchars($settings['stability_api_key'] ?? '') ?>"
                           placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                    <div class="help-text">
                        Get your key from <a href="https://platform.stability.ai" target="_blank">platform.stability.ai</a>
                    </div>
                </div>

                <div class="form-group">
                    <label>OpenAI API Key (Optional)</label>
                    <input type="password" name="setting_openai_api_key"
                           value="<?= htmlspecialchars($settings['openai_api_key'] ?? '') ?>"
                           placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                    <div class="help-text">
                        For advanced text generation. Get from <a href="https://platform.openai.com" target="_blank">platform.openai.com</a>
                    </div>
                </div>
            </div>

            <!-- Image Generation Settings -->
            <div class="panel">
                <div class="section-title">🎨 Image Generation</div>

                <div class="form-group">
                    <label>Default Image Model</label>
                    <select name="setting_default_image_model">
                        <option value="stable-diffusion-xl" <?= ($settings['default_image_model'] ?? '') === 'stable-diffusion-xl' ? 'selected' : '' ?>>
                            Stable Diffusion XL (Recommended)
                        </option>
                        <option value="stable-diffusion-v2" <?= ($settings['default_image_model'] ?? '') === 'stable-diffusion-v2' ? 'selected' : '' ?>>
                            Stable Diffusion v2
                        </option>
                        <option value="dall-e-3" <?= ($settings['default_image_model'] ?? '') === 'dall-e-3' ? 'selected' : '' ?>>
                            DALL-E 3 (Requires OpenAI)
                        </option>
                    </select>
                    <div class="help-text">
                        Choose which AI model to use for image generation
                    </div>
                </div>

                <div class="form-group">
                    <label>Auto-Generate Images</label>
                    <select name="setting_auto_generate_images">
                        <option value="1" <?= ($settings['auto_generate_images'] ?? '1') === '1' ? 'selected' : '' ?>>
                            Yes - Generate automatically
                        </option>
                        <option value="0" <?= ($settings['auto_generate_images'] ?? '1') === '0' ? 'selected' : '' ?>>
                            No - Manual only
                        </option>
                    </select>
                    <div class="help-text">
                        Automatically generate AI images when creating designs
                    </div>
                </div>

                <div class="form-group">
                    <label>Add Watermark</label>
                    <select name="setting_watermark_enabled">
                        <option value="0" <?= ($settings['watermark_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>
                            Disabled
                        </option>
                        <option value="1" <?= ($settings['watermark_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>
                            Enabled
                        </option>
                    </select>
                    <div class="help-text">
                        Add your logo/watermark to generated images
                    </div>
                </div>
            </div>

            <!-- Export Settings -->
            <div class="panel">
                <div class="section-title">📦 Export Settings</div>

                <div class="form-group">
                    <label>Default Export Format</label>
                    <select name="setting_default_export_format">
                        <option value="png">PNG Images</option>
                        <option value="html">HTML Files</option>
                        <option value="pdf">PDF Documents</option>
                        <option value="zip">ZIP Archive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image Quality (1-100)</label>
                    <input type="number" name="setting_export_quality"
                           value="<?= htmlspecialchars($settings['export_quality'] ?? '95') ?>"
                           min="1" max="100">
                    <div class="help-text">
                        Higher quality = larger file size
                    </div>
                </div>
            </div>

            <button type="submit" class="save-btn">💾 Save All Settings</button>
        </form>
    </div>
</body>
</html>
