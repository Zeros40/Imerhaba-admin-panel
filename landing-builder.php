<?php
require_once __DIR__ . '/db/config.php';
session_start();

$brands = require __DIR__ . '/config/brands.php';

// Pre-defined sections
$sections = [
    'hero' => 'Hero Banner',
    'features' => 'Features Grid',
    'roi' => 'ROI Calculator',
    'gallery' => 'Image Gallery',
    'testimonials' => 'Testimonials',
    'cta' => 'Call to Action',
    'contact' => 'Contact Form',
    'faq' => 'FAQ Accordion'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Builder - Imerhaba Design Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
        }

        .container {
            max-width: 1800px;
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

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), #c28934);
            color: var(--navy);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 350px 1fr 350px;
            gap: 2rem;
            min-height: 80vh;
        }

        .panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .panel-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .section-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .section-item {
            background: rgba(255, 255, 255, 0.08);
            padding: 1rem;
            border-radius: 8px;
            cursor: move;
            transition: all 0.2s;
            border: 2px dashed rgba(255, 255, 255, 0.2);
        }

        .section-item:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--gold);
        }

        .section-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .section-desc {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .canvas {
            background: #fff;
            border-radius: 12px;
            min-height: 600px;
            overflow-y: auto;
            position: relative;
        }

        .canvas-section {
            padding: 3rem;
            border-bottom: 2px dashed #ddd;
            position: relative;
            color: #333;
        }

        .canvas-section:hover .section-controls {
            opacity: 1;
        }

        .section-controls {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .control-btn {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }

        .empty-canvas {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            opacity: 0.5;
            color: #666;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.7rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--gold);
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .color-input {
            height: 40px;
            cursor: pointer;
        }

        .export-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        @media (max-width: 1400px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Landing Page Builder</h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="savePage()">💾 Save Page</button>
                <button class="btn btn-secondary" onclick="previewPage()">👁️ Preview</button>
                <a href="dashboard.php" class="btn btn-secondary">← Back</a>
            </div>
        </div>

        <div class="main-grid">
            <!-- Left: Sections Library -->
            <div class="panel">
                <div class="panel-title">📦 Available Sections</div>
                <div class="section-list" id="sectionsList">
                    <?php foreach ($sections as $key => $name): ?>
                        <div class="section-item" draggable="true" data-section="<?= $key ?>">
                            <div class="section-name"><?= $name ?></div>
                            <div class="section-desc">Drag to canvas →</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Center: Canvas -->
            <div class="panel">
                <div class="canvas" id="canvas">
                    <div class="empty-canvas">
                        <h2>👈 Drag sections here to start building</h2>
                        <p>Your landing page will appear here</p>
                    </div>
                </div>
            </div>

            <!-- Right: Settings -->
            <div class="panel">
                <div class="panel-title">⚙️ Page Settings</div>

                <form id="pageSettings">
                    <div class="form-group">
                        <label>Page Name</label>
                        <input type="text" name="page_name" value="My Landing Page" required>
                    </div>

                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand" id="brandSelect">
                            <?php foreach ($brands as $key => $brand): ?>
                                <option value="<?= $key ?>"><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>URL Slug</label>
                        <input type="text" name="slug" placeholder="investment-opportunity" required>
                    </div>

                    <div class="form-group">
                        <label>SEO Title</label>
                        <input type="text" name="seo_title" placeholder="Best Investment Opportunity in Bosnia">
                    </div>

                    <div class="form-group">
                        <label>SEO Description</label>
                        <textarea name="seo_description" placeholder="Invest with confidence..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Primary Color</label>
                        <input type="color" name="primary_color" value="#0B132B" class="color-input">
                    </div>

                    <div class="form-group">
                        <label>Accent Color</label>
                        <input type="color" name="accent_color" value="#D9A441" class="color-input">
                    </div>

                    <div class="panel-title" style="margin-top: 2rem;">📤 Export</div>
                    <div class="export-options">
                        <button type="button" class="btn btn-secondary" onclick="exportHTML()">HTML File</button>
                        <button type="button" class="btn btn-secondary" onclick="exportPDF()">PDF</button>
                        <button type="button" class="btn btn-secondary" onclick="publishPage()">Publish Live</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const brands = <?= json_encode($brands) ?>;
        let pageData = { sections: [] };

        // Drag and Drop
        const sectionItems = document.querySelectorAll('.section-item');
        const canvas = document.getElementById('canvas');

        sectionItems.forEach(item => {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('section', this.dataset.section);
            });
        });

        canvas.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        canvas.addEventListener('drop', function(e) {
            e.preventDefault();
            const sectionType = e.dataTransfer.getData('section');
            addSection(sectionType);
        });

        function addSection(type) {
            // Remove empty state
            if (canvas.querySelector('.empty-canvas')) {
                canvas.innerHTML = '';
            }

            const section = createSection(type);
            canvas.appendChild(section);
            pageData.sections.push({ type, content: {} });
        }

        function createSection(type) {
            const section = document.createElement('div');
            section.className = 'canvas-section';
            section.dataset.type = type;

            const controls = document.createElement('div');
            controls.className = 'section-controls';
            controls.innerHTML = `
                <button class="control-btn" onclick="editSection(this)" title="Edit">✏️</button>
                <button class="control-btn" onclick="moveUp(this)" title="Move Up">↑</button>
                <button class="control-btn" onclick="moveDown(this)" title="Move Down">↓</button>
                <button class="control-btn" onclick="deleteSection(this)" title="Delete">🗑️</button>
            `;

            section.appendChild(controls);
            section.innerHTML += getSectionTemplate(type);

            return section;
        }

        function getSectionTemplate(type) {
            const brand = brands[document.getElementById('brandSelect').value] || brands.imerhaba;

            const templates = {
                hero: `
                    <div style="text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, ${brand.colors.primary}, ${brand.colors.background});">
                        <h1 style="font-size: 3rem; color: ${brand.colors.secondary}; margin-bottom: 1rem; font-family: Poppins, sans-serif;">
                            Your Headline Here
                        </h1>
                        <p style="font-size: 1.3rem; opacity: 0.9; margin-bottom: 2rem;">
                            Transform your business with innovative solutions
                        </p>
                        <button style="background: ${brand.colors.secondary}; color: ${brand.colors.primary}; padding: 1rem 3rem; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                            Get Started
                        </button>
                    </div>
                `,
                features: `
                    <div style="padding: 3rem 2rem;">
                        <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: ${brand.colors.primary}; font-family: Poppins, sans-serif;">
                            Key Features
                        </h2>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                            ${[1, 2, 3].map(i => `
                                <div style="background: #f5f5f5; padding: 2rem; border-radius: 12px; text-align: center;">
                                    <div style="font-size: 3rem; margin-bottom: 1rem;">✨</div>
                                    <h3 style="color: ${brand.colors.primary}; margin-bottom: 0.5rem;">Feature ${i}</h3>
                                    <p style="color: #666;">Description of this amazing feature</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `,
                roi: `
                    <div style="padding: 3rem 2rem; background: ${brand.colors.accent}; color: #333;">
                        <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 2rem; color: ${brand.colors.primary}; font-family: Poppins, sans-serif;">
                            ROI Calculator
                        </h2>
                        <div style="max-width: 500px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Investment Amount (€)</label>
                            <input type="number" placeholder="10000" style="width: 100%; padding: 1rem; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 1rem;">
                            <div style="text-align: center; padding: 2rem; background: ${brand.colors.primary}; color: white; border-radius: 8px;">
                                <div style="font-size: 1rem; opacity: 0.9;">Estimated Returns</div>
                                <div style="font-size: 3rem; font-weight: 700; color: ${brand.colors.secondary};">€14,250</div>
                                <div style="font-size: 1.2rem; opacity: 0.9;">42.5% ROI</div>
                            </div>
                        </div>
                    </div>
                `,
                cta: `
                    <div style="padding: 4rem 2rem; text-align: center; background: ${brand.colors.primary}; color: white;">
                        <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: ${brand.colors.secondary}; font-family: Poppins, sans-serif;">
                            Ready to Get Started?
                        </h2>
                        <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
                            Join thousands of successful investors today
                        </p>
                        <button style="background: ${brand.colors.secondary}; color: ${brand.colors.primary}; padding: 1.2rem 3rem; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 700; cursor: pointer;">
                            Start Investing Now
                        </button>
                    </div>
                `,
                contact: `
                    <div style="padding: 3rem 2rem; background: #f9f9f9;">
                        <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 2rem; color: ${brand.colors.primary}; font-family: Poppins, sans-serif;">
                            Contact Us
                        </h2>
                        <form style="max-width: 600px; margin: 0 auto;">
                            <input type="text" placeholder="Your Name" style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 1rem;">
                            <input type="email" placeholder="Your Email" style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 1rem;">
                            <textarea placeholder="Your Message" style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 1rem; min-height: 120px;"></textarea>
                            <button type="submit" style="width: 100%; padding: 1.2rem; background: ${brand.colors.primary}; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                Send Message
                            </button>
                        </form>
                    </div>
                `
            };

            return templates[type] || '<p>Section content</p>';
        }

        function editSection(btn) {
            alert('Edit functionality - coming soon!');
        }

        function moveUp(btn) {
            const section = btn.closest('.canvas-section');
            const prev = section.previousElementSibling;
            if (prev) {
                section.parentNode.insertBefore(section, prev);
            }
        }

        function moveDown(btn) {
            const section = btn.closest('.canvas-section');
            const next = section.nextElementSibling;
            if (next) {
                section.parentNode.insertBefore(next, section);
            }
        }

        function deleteSection(btn) {
            if (confirm('Delete this section?')) {
                btn.closest('.canvas-section').remove();
            }
        }

        async function savePage() {
            const formData = new FormData(document.getElementById('pageSettings'));
            const pageHTML = canvas.innerHTML;

            const response = await fetch('api/save-page.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: formData.get('page_name'),
                    brand: formData.get('brand'),
                    slug: formData.get('slug'),
                    seo_title: formData.get('seo_title'),
                    seo_description: formData.get('seo_description'),
                    html: pageHTML,
                    sections: pageData.sections
                })
            });

            const result = await response.json();
            if (result.success) {
                alert('✅ Page saved successfully!');
            }
        }

        function previewPage() {
            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Preview</title>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Inter', sans-serif; }
                    </style>
                </head>
                <body>
                    ${canvas.innerHTML}
                </body>
                </html>
            `);
        }

        function exportHTML() {
            const html = generateFullHTML();
            const blob = new Blob([html], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'landing-page.html';
            a.click();
        }

        function generateFullHTML() {
            const formData = new FormData(document.getElementById('pageSettings'));
            const brand = brands[formData.get('brand')];

            return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${formData.get('seo_title')}</title>
    <meta name="description" content="${formData.get('seo_description')}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>
    ${canvas.innerHTML}
</body>
</html>`;
        }

        // Brand change updates colors
        document.getElementById('brandSelect').addEventListener('change', function() {
            const brand = brands[this.value];
            if (brand) {
                document.querySelector('[name="primary_color"]').value = brand.colors.primary;
                document.querySelector('[name="accent_color"]').value = brand.colors.secondary;
            }
        });
    </script>
</body>
</html>
