<?php
define('REQUIRE_AUTH', false);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/mail/MailService.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($message)) $errors[] = 'Message is required';
    if (!empty($email) && !isValidEmail($email)) $errors[] = 'Invalid email address';

    if (empty($errors)) {
        try {
            $pdo = db();

            // Save to database
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, ip_address)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $name,
                $email,
                $subject,
                $message,
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);

            // Try to send email notification (optional)
            try {
                $adminEmail = getenv('MAIL_TO') ?: getSetting('admin_email', 'admin@imerhaba.com');
                $emailSubject = $subject ?: 'New Contact Form Submission';

                MailService::sendContactMessage($name, $email, $message, $adminEmail, $emailSubject);
            } catch (Exception $e) {
                // Log but don't fail if email fails
                error_log("Contact form email failed: " . $e->getMessage());
            }

            $success = true;
        } catch (PDOException $e) {
            $errors[] = 'Error saving message. Please try again later.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?= e(getSetting('site_name', 'Imerhaba Admin Panel')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .contact-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 600px;
            padding: 48px;
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .logo p {
            color: var(--text-light);
            font-size: 14px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: var(--error);
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: var(--success);
            border: 1px solid #cfc;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        input, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-light);
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .success-message {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--success);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 24px;
        }

        .success-message h2 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        .success-message p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="contact-container">
        <div class="logo">
            <h1>Contact Us</h1>
            <p><?= e(getSetting('site_name', 'Imerhaba Admin Panel')) ?></p>
        </div>

        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Thank You!</h2>
                <p>Your message has been sent successfully. We'll get back to you soon.</p>
                <a href="/contact" class="btn">Send Another Message</a>
            </div>
        <?php else: ?>
            <?php if (count($errors) > 0): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/contact">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Your name"
                        required
                        value="<?= e($_POST['name'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="your@email.com"
                        required
                        value="<?= e($_POST['email'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="What is this about?"
                        value="<?= e($_POST['subject'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea
                        id="message"
                        name="message"
                        placeholder="Your message..."
                        required
                    ><?= e($_POST['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn">Send Message</button>
            </form>

            <div class="footer">
                <p><a href="/login">Admin Login</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
