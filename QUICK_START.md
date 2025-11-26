# Quick Start Guide - AI Agent Platform

Get up and running in 5 minutes! 🚀

## Prerequisites

- PHP 8.1 or higher
- MySQL/MariaDB
- Composer
- Git

## Installation (5 minutes)

### Step 1: Clone & Setup (1 min)
```bash
git clone <repo-url>
cd imerhaba-admin-panel
cp .env.example .env
```

### Step 2: Get API Keys (2 min)

#### OpenAI API Key
1. Go to https://platform.openai.com/api-keys
2. Create new API key
3. Copy and save it

#### Anthropic API Key (Optional)
1. Go to https://console.anthropic.com/
2. Create new API key
3. Copy and save it

### Step 3: Configure .env (1 min)
Edit `.env` file:
```env
OPENAI_API_KEY=sk-your-key-here
ANTHROPIC_API_KEY=sk-ant-your-key-here

DB_NAME=ai_agent_platform
DB_USER=root
DB_PASSWORD=

JWT_SECRET=your-secret-key-change-me
```

### Step 4: Setup Database (1 min)
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE ai_agent_platform CHARACTER SET utf8mb4;"

# Install dependencies
composer install

# Run migrations
php migrate.php

# Create directories
mkdir -p storage/{generated-apps,uploads}
chmod -R 755 storage logs
```

### Step 5: Run & Access (Instant!)
```bash
php -S localhost:8000
```

Open your browser: **http://localhost:8000**

## First Test

1. **Register** a new account
2. **Login** with your credentials
3. Go to **Generate** page
4. Enter prompt:
   ```
   Create a responsive todo list app with add, edit, and delete features
   ```
5. Select model: **gpt-4** (or claude-3-5-sonnet)
6. Choose type: **React**
7. Click **Generate Code** ✨

Wait 30-60 seconds and see your generated code!

## Common Tasks

### Generate a Different App Type

Choose from these types:
- **Web App** - Pure HTML/CSS/JavaScript
- **React** - Modern React with Hooks
- **Vue** - Vue 3 with Composition API
- **Angular** - Full Angular project
- **API** - REST API backend
- **CLI** - Command-line tool

### View Generated Code

1. Go to **Projects**
2. Click **View** on any project
3. See generated code in the modal
4. Click **Download** to export

### Manage Projects

- **Projects page** - See all your creations
- **View** - See details and code
- **Delete** - Remove project
- **Export** - Download code

### Edit Profile

1. Click **Profile** in top menu
2. Update your information
3. Change password if needed
4. View your usage statistics

## Troubleshooting

### "Cannot connect to database"
```bash
# Verify MySQL is running
mysql -u root -p

# Check database exists
mysql -u root -p -e "SHOW DATABASES;" | grep ai_agent

# Run migrations again
php migrate.php
```

### "API Key Error"
- Verify key is copied correctly (no spaces)
- Check key is valid on OpenAI/Anthropic website
- Ensure there are usage credits available

### "Generation Timeout"
- Try a shorter prompt
- Use a faster model (gpt-4-turbo, claude-3-sonnet)
- Check PHP max_execution_time in php.ini

### "Blank frontend page"
- Check browser console (F12)
- Verify PHP server is running
- Clear browser cache
- Check that api.php is accessible

## API Quick Reference

### Login & Get Token
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

### Generate Code
```bash
curl -X POST http://localhost:8000/api/v1/generation/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "project_name": "My App",
    "prompt": "Create a todo app",
    "ai_model": "gpt-4",
    "app_type": "react"
  }'
```

### List Projects
```bash
curl -X GET http://localhost:8000/api/v1/projects \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Next Steps

### Learn More
- Read **README.md** for full documentation
- Check **IMPLEMENTATION_GUIDE.md** for technical details
- Review API endpoints in README

### Customize
- Change styles in `public/css/styles.css`
- Modify frontend in `public/js/app.js`
- Add custom system prompts in `src/Services/AIService.php`

### Deploy
- Use a web hosting with PHP 8.1+ support
- Use managed database service (AWS RDS, etc.)
- Set up HTTPS/SSL certificate
- Configure proper domain in `.env`

### Extend
- Add more AI models
- Implement real-time code execution
- Add team collaboration features
- Create custom templates
- Build API for mobile app

## Support

### Resources
- Check README.md for detailed documentation
- Review example API calls
- Check browser DevTools (F12) for errors
- Check PHP error logs

### Common Questions

**Q: Can I use other AI models?**
A: Yes! Modify `config/env.php` and `src/Services/AIManager.php`

**Q: Is my code saved permanently?**
A: Yes! Check Projects page. You can download and export anytime.

**Q: Can I share my projects?**
A: Not yet. Future version will add sharing. For now, export and share the code.

**Q: How much will this cost?**
A: Depends on token usage with OpenAI/Anthropic. Check their pricing.

**Q: Can I use this in production?**
A: Yes! Follow security checklist in IMPLEMENTATION_GUIDE.md

## Performance Tips

1. **Use GPT-4 Turbo** - Faster than GPT-4, similar quality
2. **Be specific in prompts** - Better results, fewer tokens
3. **Start with Web App** - Faster than React for simple apps
4. **Clear your database** - Remove old projects regularly

## Security Reminder

⚠️ **Before going live:**
- Change `JWT_SECRET` to something strong
- Use environment variables for API keys
- Enable HTTPS/SSL
- Set `APP_ENV=production`
- Don't commit `.env` to git
- Regular database backups

## Happy Building! 🎉

You now have a complete AI platform for generating applications from prompts.

Need help? Check the documentation or review error messages in browser console.

Happy coding! 🚀
