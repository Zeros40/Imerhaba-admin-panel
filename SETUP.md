# 🚀 Quick Setup Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache/Nginx)

## Step-by-Step Installation

### 1. Database Setup
```bash
# Navigate to project directory
cd /path/to/Imerhaba-admin-panel

# Initialize database (creates all tables)
php db/init.php
```

**Expected Output:**
```
🚀 Initializing Imerhaba Design Studio Database...

✅ Executed 15 SQL statements

📊 Verifying tables...
  ✓ designs
  ✓ templates
  ✓ campaigns
  ✓ landing_pages
  ✓ settings
  ✓ analytics

✨ Database initialization complete!
```

### 2. Configure API Keys

Visit: `http://your-domain.com/settings.php`

**Required for AI Image Generation:**

1. **Get Replicate API Key** (Recommended - Free Tier Available)
   - Go to https://replicate.com
   - Click "Sign up" (top right)
   - Verify email
   - Go to Account → API Tokens
   - Click "Create Token"
   - Copy token (starts with `r8_`)
   - Paste in Settings page

2. **Alternative: Stability AI**
   - Go to https://platform.stability.ai
   - Create account
   - Navigate to API Keys
   - Generate new key
   - Copy and paste in Settings

### 3. Set Permissions

```bash
# Make exports folder writable
mkdir -p exports
chmod 755 exports

# Ensure API folder is accessible
chmod 755 api
```

### 4. Access Dashboard

Navigate to: `http://your-domain.com/`

You'll be redirected to the dashboard automatically!

---

## First Time Use

### Generate Your First Design

1. Click **"🚀 Bulk Generator"**
2. Select Brand: **Imerhaba**
3. Paste some test ideas:
   ```
   Invest with confidence
   Grow your business in Bosnia
   Smart investment opportunities
   ```
4. Choose Ratio: **1:1**
5. Click **"Generate All Designs"**
6. Wait ~30 seconds
7. Click **"Export as ZIP"**
8. Done! ✅

### Create Your First Landing Page

1. Click **"🎨 Landing Pages"**
2. Drag **"Hero"** section to canvas
3. Drag **"Features"** section
4. Drag **"CTA"** section
5. Click **"Preview"** to see result
6. Click **"Export HTML"** to download
7. Upload to your website!

---

## Configuration Tips

### Brand Customization

Edit `config/brands.php` to customize brand colors:

```php
'imerhaba' => [
    'colors' => [
        'primary' => '#0B132B',    // Change this
        'secondary' => '#D9A441',  // And this
    ]
]
```

### API Settings

In `settings.php`:
- **Auto-Generate Images**: Enable/disable AI generation
- **Default Model**: Choose Stable Diffusion XL (best quality)
- **Export Quality**: 95 recommended (balance size/quality)

---

## Testing

### Test Database Connection
```bash
php -r "require 'db/config.php'; echo 'Connected: ' . (db() ? 'Yes' : 'No');"
```

### Test API Key
Visit `settings.php` → Save settings → Check for success message

---

## Common Issues

### "Table doesn't exist"
**Solution**: Run `php db/init.php` again

### "Permission denied" on exports
**Solution**:
```bash
chmod 755 exports/
chown www-data:www-data exports/  # Linux/Apache
```

### API Key Not Working
**Solution**:
1. Copy key exactly (no spaces)
2. Check key hasn't expired
3. Verify account has credits (for paid APIs)

---

## Production Deployment

### Secure Settings

1. **Disable Display Errors**
   ```php
   // In index.php
   ini_set('display_errors', '0');
   ```

2. **Enable HTTPS**
   - Get SSL certificate (Let's Encrypt)
   - Force HTTPS in .htaccess

3. **Database Backups**
   ```bash
   # Daily backup
   mysqldump -u user -p database > backup_$(date +%Y%m%d).sql
   ```

4. **Add Authentication** (Optional)
   - Add login system
   - Protect admin pages
   - Use sessions

---

## Need Help?

Check the main [README.md](README.md) for detailed documentation!

**Quick Links:**
- [Brand Configuration](config/brands.php)
- [Database Schema](db/schema.sql)
- [API Documentation](README.md#api-endpoints)

---

**You're ready to generate thousands of designs! 🎨**
