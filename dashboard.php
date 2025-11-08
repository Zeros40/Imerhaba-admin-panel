<?php
require_once __DIR__ . '/db/config.php';

// Simple authentication (enhance with proper auth later)
session_start();
$brands = require __DIR__ . '/config/brands.php';

// Get statistics
$db = db();
$stats = [
    'total_designs' => $db->query("SELECT COUNT(*) FROM designs")->fetchColumn() ?? 0,
    'total_campaigns' => $db->query("SELECT COUNT(*) FROM campaigns")->fetchColumn() ?? 0,
    'total_templates' => $db->query("SELECT COUNT(*) FROM templates")->fetchColumn() ?? 0,
    'total_pages' => $db->query("SELECT COUNT(*) FROM landing_pages")->fetchColumn() ?? 0
];

$recent_designs = $db->query("
    SELECT * FROM designs
    ORDER BY created_at DESC
    LIMIT 6
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imerhaba Design Studio - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            --ivory: #F8F5EF;
            --blue: #0424BB;
            --silver: #E6E9EF;
            --emerald: #0F4D3F;
            --sand: #E1C699;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0B132B 0%, #1a2332 100%);
            color: #fff;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--gold), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        /* Navigation */
        .nav {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: linear-gradient(135deg, var(--gold), #c28934);
            color: #0B132B;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(217, 164, 65, 0.3);
        }

        .nav-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--gold);
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            opacity: 0.8;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        /* Recent Designs */
        .section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .designs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .design-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }

        .design-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .design-brand {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .brand-imerhaba { background: var(--gold); color: var(--navy); }
        .brand-zodiac { background: var(--blue); color: white; }
        .brand-ruya { background: var(--emerald); color: white; }

        .design-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .design-meta {
            opacity: 0.7;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            opacity: 0.6;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .action-card {
            background: linear-gradient(135deg, rgba(217, 164, 65, 0.1), rgba(217, 164, 65, 0.05));
            border: 1px solid rgba(217, 164, 65, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            display: block;
        }

        .action-card:hover {
            background: linear-gradient(135deg, rgba(217, 164, 65, 0.2), rgba(217, 164, 65, 0.1));
            transform: scale(1.05);
        }

        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .action-desc {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            h1 { font-size: 1.8rem; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .nav { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>✨ Imerhaba Design Studio</h1>
            <p class="subtitle">AI-Powered Design Generation for Your Business Ecosystem</p>

            <div class="nav">
                <a href="generator.php" class="nav-btn">🚀 Bulk Generator</a>
                <a href="landing-builder.php" class="nav-btn">🎨 Landing Pages</a>
                <a href="templates.php" class="nav-btn secondary">📋 Templates</a>
                <a href="campaigns.php" class="nav-btn secondary">📊 Campaigns</a>
                <a href="settings.php" class="nav-btn secondary">⚙️ Settings</a>
            </div>
        </header>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_designs'] ?></div>
                <div class="stat-label">Total Designs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_campaigns'] ?></div>
                <div class="stat-label">Campaigns</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_templates'] ?></div>
                <div class="stat-label">Templates</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_pages'] ?></div>
                <div class="stat-label">Landing Pages</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <h2 class="section-title">⚡ Quick Actions</h2>
            <div class="quick-actions">
                <a href="generator.php" class="action-card">
                    <div class="action-icon">🎯</div>
                    <div class="action-title">Bulk Generate Posts</div>
                    <div class="action-desc">Create 50-100 social media designs at once</div>
                </a>
                <a href="landing-builder.php" class="action-card">
                    <div class="action-icon">🏗️</div>
                    <div class="action-title">Build Landing Page</div>
                    <div class="action-desc">Create campaign-ready websites</div>
                </a>
                <a href="templates.php?action=new" class="action-card">
                    <div class="action-icon">✏️</div>
                    <div class="action-title">New Template</div>
                    <div class="action-desc">Design reusable templates</div>
                </a>
                <a href="export.php" class="action-card">
                    <div class="action-icon">📦</div>
                    <div class="action-title">Export Library</div>
                    <div class="action-desc">Download all designs as ZIP</div>
                </a>
            </div>
        </div>

        <!-- Recent Designs -->
        <div class="section">
            <h2 class="section-title">📌 Recent Designs</h2>

            <?php if (empty($recent_designs)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p>No designs yet. Start creating!</p>
                </div>
            <?php else: ?>
                <div class="designs-grid">
                    <?php foreach ($recent_designs as $design): ?>
                        <div class="design-card">
                            <span class="design-brand brand-<?= htmlspecialchars($design['brand']) ?>">
                                <?= htmlspecialchars($design['brand']) ?>
                            </span>
                            <div class="design-title"><?= htmlspecialchars($design['title']) ?></div>
                            <div class="design-meta">
                                <?= htmlspecialchars($design['ratio']) ?> •
                                <?= date('M d, Y', strtotime($design['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
