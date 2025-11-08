# 🎨 Imerhaba Design Studio

**AI-Powered Design Generation System for Multi-Brand Ecosystem**

A complete design automation platform that generates 50-100 social media posts, landing pages, and marketing materials with AI-powered images for the Imerhaba business ecosystem.

---

## ✨ Features

### 🎯 Bulk Post Generator
- **Mass Production**: Generate 50-100 social media posts at once
- **Multi-Format**: 1:1 (Square), 4:5 (Portrait), 9:16 (Stories)
- **AI Images**: Automatic background generation via Replicate/Stability AI
- **Multi-Language**: English, Arabic, Bosnian support
- **Brand-Aware**: Automatically applies correct colors, fonts, and styling
- **Export**: PNG, HTML, PDF, or ZIP downloads

### 🏗️ Landing Page Builder
- **Drag & Drop**: Visual page builder with pre-made sections
- **Professional Templates**: Hero, Features, ROI Calculator, Contact Forms
- **SEO Optimized**: Built-in meta tags and optimization
- **Responsive**: Mobile-first design approach
- **Live Preview**: See changes in real-time
- **One-Click Export**: HTML, PDF, or publish directly

### 🎨 Multi-Brand Support
- **Imerhaba**: Navy + Gold (Consulting & Investment)
- **Zodiac**: Deep Blue + Silver (AI SaaS, Homes, Glamping)
- **Ruya**: Emerald + Sand (Real Estate)
- **Imerhaba Cars**: Luxury automotive styling
- **Imerhaba Tourism**: Adventure & culture theme

### 📊 Dashboard & Analytics
- Design library management
- Campaign organization
- Usage statistics
- Export history

---

## 🚀 Quick Start

### 1️⃣ Database Setup

```bash
# Initialize the database
php db/init.php
```

This will create all necessary tables:
- `designs` - Stores all generated designs
- `templates` - Reusable design templates
- `campaigns` - Campaign organization
- `landing_pages` - Website pages
- `settings` - API keys and config
- `analytics` - Usage tracking

### 2️⃣ Configure API Keys

Visit `settings.php` and add your API keys:

**For AI Image Generation (Choose one):**

- **Replicate** (Recommended): https://replicate.com
  - Free tier: 50 images/month
  - Sign up → Account → API Tokens
  - Copy your key: `r8_xxxxxxxxxxxxx`

- **Stability AI**: https://platform.stability.ai
  - More powerful, paid only
  - API Keys section
  - Copy key: `sk-xxxxxxxxxxxxx`

**Optional:**
- **OpenAI**: For DALL-E 3 or advanced text generation

### 3️⃣ Start Creating!

1. **Visit Dashboard**: `http://your-domain.com/dashboard.php`

2. **Bulk Generate Posts**:
   - Click "🚀 Bulk Generator"
   - Select brand (Imerhaba, Zodiac, Ruya)
   - Paste 50-100 post ideas (one per line)
   - Choose format (1:1, 4:5, 9:16)
   - Click "Generate All Designs"
   - Wait for AI to create all designs
   - Export as ZIP

3. **Build Landing Pages**:
   - Click "🎨 Landing Pages"
   - Drag sections to canvas
   - Customize content
   - Export HTML or publish

---

## 📁 Project Structure

```
Imerhaba-admin-panel/
├── index.php              # Entry point (redirects to dashboard)
├── dashboard.php          # Main dashboard
├── generator.php          # Bulk post generator
├── landing-builder.php    # Landing page builder
├── settings.php           # Configuration & API keys
│
├── config/
│   └── brands.php         # Brand colors, fonts, themes
│
├── db/
│   ├── config.php         # Database connection
│   ├── schema.sql         # Database schema
│   └── init.php           # Database initialization
│
├── api/
│   ├── save-design.php    # Save design to DB
│   ├── save-page.php      # Save landing page
│   ├── generate-image.php # AI image generation
│   └── export.php         # Export designs
│
└── exports/               # Generated files (auto-created)
```

---

## 🎨 Usage Examples

### Example 1: Generate 100 Investment Posts

1. Open `generator.php`
2. Select Brand: **Zodiac**
3. Paste ideas:
   ```
   Invest €10,000, earn 35-50% ROI in Bosnia
   AI-powered glamping investments starting €5,000
   Smart real estate with guaranteed returns
   Luxury vacation homes in Sarajevo mountains
   ... (96 more lines)
   ```
4. Choose: **1:1 Square** (Instagram/Facebook)
5. Language: **English**
6. Click **Generate All Designs**
7. Export as **ZIP** → Download 100 ready-to-post images!

### Example 2: Create Investment Landing Page

1. Open `landing-builder.php`
2. Drag sections:
   - Hero Banner (headline + CTA)
   - ROI Calculator (interactive)
   - Features Grid (3 benefits)
   - Testimonials
   - Contact Form
3. Select Brand: **Imerhaba**
4. Customize colors
5. Export HTML → Upload to your site!

### Example 3: Multi-Brand Campaign

Create 50 posts for each brand in one session:
- 50 × Imerhaba (consulting posts)
- 50 × Zodiac (AI tech posts)
- 50 × Ruya (real estate posts)

**Total: 150 designs in ~10 minutes!**

---

## 🎨 Brand Color Reference

```css
/* Imerhaba - Consulting & Investment */
Primary:   #0B132B (Navy)
Secondary: #D9A441 (Gold)
Accent:    #F8F5EF (Ivory)

/* Zodiac - AI SaaS */
Primary:   #0424BB (Deep Blue)
Secondary: #E6E9EF (Silver)
Accent:    #00D4FF (Electric Blue)

/* Ruya - Real Estate */
Primary:   #0F4D3F (Emerald)
Secondary: #E1C699 (Desert Sand)
Accent:    #2C7A62 (Teal)

/* Imerhaba Cars */
Primary:   #0B132B (Navy)
Secondary: #D9A441 (Gold)
Accent:    #C0C0C0 (Chrome)

/* Imerhaba Tourism */
Primary:   #0B132B (Navy)
Secondary: #D9A441 (Gold)
Accent:    #87CEEB (Sky Blue)
```

---

## 🔧 Advanced Configuration

### Custom Templates

Edit `config/brands.php` to add new brands:

```php
'new_brand' => [
    'name' => 'Brand Name',
    'colors' => [
        'primary' => '#000000',
        'secondary' => '#FFFFFF',
        'accent' => '#FF0000',
    ],
    'fonts' => [
        'heading' => 'Poppins',
        'body' => 'Inter',
    ],
    'mood' => 'Professional, innovative',
    'keywords' => ['keyword1', 'keyword2']
]
```

### Image Generation Prompts

Prompts are automatically enhanced with brand context. Edit `api/generate-image.php` → `enhancePrompt()` function to customize.

### Export Formats

- **PNG**: Screenshot-quality images (uses HTML → Canvas)
- **HTML**: Standalone HTML files (editable)
- **PDF**: Print-ready documents
- **ZIP**: Bundle all designs

---

## 📊 API Endpoints

### POST `/api/save-design.php`
Save a generated design to database

**Request:**
```json
{
  "brand": "imerhaba",
  "title": "Investment Opportunity",
  "headline": "Invest €10K, earn 50% ROI",
  "ratio": "1:1",
  "language": "EN"
}
```

### POST `/api/generate-image.php`
Generate AI image from prompt

**Request:**
```json
{
  "prompt": "Luxury business office in Bosnia",
  "brand": "imerhaba",
  "provider": "replicate"
}
```

### POST `/api/export.php`
Export designs in various formats

**Request:**
```json
{
  "designs": [...],
  "format": "zip"
}
```

---

## 🌐 Deployment

### Option 1: Traditional Hosting

1. Upload all files to your server
2. Run `php db/init.php`
3. Configure `.htaccess` for clean URLs
4. Set folder permissions:
   ```bash
   chmod 755 exports/
   ```

### Option 2: Hostinger AI Builder

1. Export landing pages as HTML
2. Upload via Hostinger File Manager
3. Link to custom domain

### Option 3: Docker (Advanced)

```dockerfile
FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html/
EXPOSE 80
```

---

## 🆘 Troubleshooting

### Database Connection Error
```
Error: SQLSTATE[HY000] [1045] Access denied
```
**Fix**: Check `db/config.php` credentials

### API Key Invalid
```
Error: Replicate API error: 401
```
**Fix**: Verify API key in `settings.php`

### Images Not Generating
**Possible causes:**
1. No API key configured
2. API quota exceeded
3. Network issues

**Fix**: Check Settings → API Keys section

### Export Not Working
**Fix**: Ensure `exports/` folder is writable:
```bash
chmod 755 exports/
```

---

## 📈 Roadmap

- [x] Bulk post generator
- [x] Landing page builder
- [x] Multi-brand support
- [x] AI image generation
- [x] Export functionality
- [ ] Animation/Video export
- [ ] Social media scheduling
- [ ] Analytics dashboard
- [ ] A/B testing
- [ ] Multi-user support

---

## 🤝 Contributing

This is a private project for the Imerhaba business ecosystem. For feature requests or bug reports, contact the development team.

---

## 📄 License

Proprietary - © 2025 Imerhaba Consulting & Investing Hub

---

## 🎯 For Lovable.dev Users

If you want to build this on Lovable, use this simplified prompt:

```
Build an AI design generator with:
1. Bulk post creator (paste 100 texts → generate 100 designs)
2. Landing page builder (drag-drop sections)
3. Multi-brand theming (3 color schemes)
4. AI image generation (Replicate API)
5. Export as PNG/HTML/ZIP

Tech: React + Tailwind + Supabase + Replicate API
```

---

**Made with ❤️ for the Imerhaba Ecosystem**

*Transform your marketing with AI-powered design automation*
