<?php
require_once __DIR__ . '/db/config.php';
session_start();

$brands = require __DIR__ . '/config/brands.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Post Generator - Imerhaba Design Studio</title>
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
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0B132B 0%, #1a2332 100%);
            color: #fff;
            min-height: 100vh;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem;
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

        .main-grid {
            display: grid;
            grid-template-columns: 450px 1fr;
            gap: 2rem;
            height: calc(100vh - 200px);
        }

        .panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        select, textarea, input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
        }

        select:focus, textarea:focus, input:focus {
            outline: none;
            border-color: var(--gold);
        }

        textarea {
            min-height: 200px;
            resize: vertical;
        }

        .help-text {
            font-size: 0.85rem;
            opacity: 0.7;
            margin-top: 0.3rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--gold);
        }

        .ratio-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .ratio-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .ratio-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .ratio-btn.active {
            background: var(--gold);
            color: var(--navy);
            border-color: var(--gold);
        }

        .generate-btn {
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

        .generate-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(217, 164, 65, 0.4);
        }

        .generate-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0;
            display: none;
        }

        .progress-bar.active {
            display: block;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold), #fff);
            width: 0%;
            transition: width 0.3s;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .preview-canvas {
            width: 100%;
            aspect-ratio: 1;
            background: linear-gradient(135deg, #0B132B, #1a2332);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .preview-headline {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            z-index: 2;
        }

        .preview-subtext {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 1.5rem;
            z-index: 2;
        }

        .preview-cta {
            background: var(--gold);
            color: var(--navy);
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            z-index: 2;
        }

        .preview-status {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            opacity: 0.7;
        }

        .status-generating {
            color: #ffd700;
        }

        .status-done {
            color: #00ff00;
        }

        .status-error {
            color: #ff6b6b;
        }

        .empty-preview {
            text-align: center;
            padding: 3rem;
            opacity: 0.5;
        }

        .brand-accent {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            z-index: 3;
        }

        .stats-bar {
            display: flex;
            justify-content: space-around;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gold);
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .export-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .export-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            transition: all 0.2s;
        }

        .export-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 1200px) {
            .main-grid {
                grid-template-columns: 1fr;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Bulk Post Generator</h1>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>

        <div class="main-grid">
            <!-- Left Panel: Input Form -->
            <div class="panel">
                <form id="generatorForm">
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand" id="brandSelect" required>
                            <option value="">Select Brand...</option>
                            <?php foreach ($brands as $key => $brand): ?>
                                <option value="<?= $key ?>"><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Post Ideas (One per line)</label>
                        <textarea name="posts" id="postsInput" placeholder="Paste your post ideas here...
Example:
Invest €10,000, earn 35-50% ROI in Bosnia
Explore luxury glamping in nature
AI-powered business solutions
Book your dream vacation in Sarajevo
..."></textarea>
                        <div class="help-text" id="lineCount">0 posts</div>
                    </div>

                    <div class="form-group">
                        <label>Post Ratio</label>
                        <div class="ratio-grid">
                            <button type="button" class="ratio-btn active" data-ratio="1:1">1:1<br><small>Square</small></button>
                            <button type="button" class="ratio-btn" data-ratio="4:5">4:5<br><small>Portrait</small></button>
                            <button type="button" class="ratio-btn" data-ratio="9:16">9:16<br><small>Story</small></button>
                        </div>
                        <input type="hidden" name="ratio" id="ratioInput" value="1:1">
                    </div>

                    <div class="form-group">
                        <label>Language</label>
                        <select name="language" required>
                            <option value="EN">English</option>
                            <option value="AR">Arabic</option>
                            <option value="BS">Bosnian</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Default CTA Text</label>
                        <input type="text" name="cta" value="Learn More" placeholder="e.g., Invest Now, Book Tour">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="generateImages" name="generate_images" checked>
                        <label for="generateImages">Auto-generate AI images</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="addLogo" name="add_logo" checked>
                        <label for="addLogo">Add brand logo</label>
                    </div>

                    <button type="submit" class="generate-btn" id="generateBtn">
                        🚀 Generate All Designs
                    </button>

                    <div class="progress-bar" id="progressBar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>

                    <div class="export-section" id="exportSection" style="display: none;">
                        <label>Export Options</label>
                        <button type="button" class="export-btn" onclick="exportAll('png')">📥 Export as PNG</button>
                        <button type="button" class="export-btn" onclick="exportAll('html')">🌐 Export as HTML</button>
                        <button type="button" class="export-btn" onclick="exportAll('zip')">📦 Download ZIP</button>
                    </div>
                </form>
            </div>

            <!-- Right Panel: Preview Grid -->
            <div class="panel">
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-value" id="totalCount">0</div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="generatedCount">0</div>
                        <div class="stat-label">Generated</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="pendingCount">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>

                <div id="previewGrid" class="preview-grid">
                    <div class="empty-preview">
                        <h3>👆 Enter your post ideas and click Generate</h3>
                        <p>Designs will appear here in real-time</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const brands = <?= json_encode($brands) ?>;
        let generatedDesigns = [];

        // Line counter
        document.getElementById('postsInput').addEventListener('input', function() {
            const lines = this.value.split('\n').filter(line => line.trim().length > 0);
            document.getElementById('lineCount').textContent = `${lines.length} posts`;
        });

        // Ratio selector
        document.querySelectorAll('.ratio-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('ratioInput').value = this.dataset.ratio;
            });
        });

        // Brand color preview
        document.getElementById('brandSelect').addEventListener('change', function() {
            const brand = brands[this.value];
            if (brand) {
                document.documentElement.style.setProperty('--brand-primary', brand.colors.primary);
                document.documentElement.style.setProperty('--brand-secondary', brand.colors.secondary);
            }
        });

        // Form submission
        document.getElementById('generatorForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const posts = formData.get('posts').split('\n').filter(line => line.trim().length > 0);
            const brand = formData.get('brand');
            const ratio = formData.get('ratio');
            const language = formData.get('language');
            const cta = formData.get('cta');
            const generateImages = formData.get('generate_images') === 'on';

            if (!brand) {
                alert('Please select a brand');
                return;
            }

            if (posts.length === 0) {
                alert('Please enter at least one post idea');
                return;
            }

            // Update stats
            document.getElementById('totalCount').textContent = posts.length;
            document.getElementById('pendingCount').textContent = posts.length;
            document.getElementById('generatedCount').textContent = '0';

            // Show progress bar
            document.getElementById('progressBar').classList.add('active');
            document.getElementById('generateBtn').disabled = true;
            document.getElementById('generateBtn').textContent = '⚙️ Generating...';

            // Clear preview
            document.getElementById('previewGrid').innerHTML = '';

            // Generate designs
            generatedDesigns = [];
            for (let i = 0; i < posts.length; i++) {
                await generateDesign(posts[i], brand, ratio, language, cta, generateImages, i, posts.length);
            }

            // Done
            document.getElementById('generateBtn').disabled = false;
            document.getElementById('generateBtn').textContent = '✅ Generation Complete!';
            document.getElementById('exportSection').style.display = 'block';

            setTimeout(() => {
                document.getElementById('generateBtn').textContent = '🚀 Generate All Designs';
            }, 3000);
        });

        async function generateDesign(postText, brand, ratio, language, cta, generateImages, index, total) {
            const brandData = brands[brand];

            // Create preview card
            const card = document.createElement('div');
            card.className = 'preview-card';
            card.innerHTML = `
                <div class="preview-canvas" style="background: ${brandData.colors.background}">
                    <span class="brand-accent" style="background: ${brandData.colors.secondary}; color: ${brandData.colors.primary}">${brand}</span>
                    <div class="preview-headline" style="color: ${brandData.colors.secondary}">${postText}</div>
                    <div class="preview-subtext">Transform your business with innovation</div>
                    <div class="preview-cta">${cta}</div>
                </div>
                <div class="preview-status status-generating">⚙️ Generating...</div>
            `;
            document.getElementById('previewGrid').appendChild(card);

            // Simulate API call (replace with real API)
            await new Promise(resolve => setTimeout(resolve, 500));

            // Update progress
            const progress = ((index + 1) / total) * 100;
            document.getElementById('progressFill').style.width = `${progress}%`;
            document.getElementById('generatedCount').textContent = index + 1;
            document.getElementById('pendingCount').textContent = total - (index + 1);

            // Mark as done
            card.querySelector('.preview-status').className = 'preview-status status-done';
            card.querySelector('.preview-status').textContent = '✓ Generated';

            generatedDesigns.push({
                text: postText,
                brand: brand,
                ratio: ratio,
                html: card.querySelector('.preview-canvas').outerHTML
            });

            // Save to database via API
            await fetch('api/save-design.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    brand: brand,
                    title: postText.substring(0, 100),
                    description: postText,
                    headline: postText,
                    cta_text: cta,
                    ratio: ratio,
                    language: language,
                    design_data: { colors: brandData.colors }
                })
            });
        }

        async function exportAll(format) {
            alert(`Exporting ${generatedDesigns.length} designs as ${format.toUpperCase()}...`);

            // Call export API
            const response = await fetch('api/export.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    designs: generatedDesigns,
                    format: format
                })
            });

            if (format === 'zip') {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `imerhaba-designs-${Date.now()}.zip`;
                a.click();
            }
        }
    </script>
</body>
</html>
