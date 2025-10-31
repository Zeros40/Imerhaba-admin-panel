# Imerhaba Admin Panel

A modern, full-featured PHP admin panel with user management, authentication, activity logging, and contact form integration.

## Features

### Core Features
- **User Authentication**: Secure login/logout with session management
- **User Management**: Full CRUD operations for managing users
- **Role-based Access Control**: Admin, Manager, and User roles
- **Dashboard**: Statistics overview with charts and recent activity
- **Activity Logging**: Track all user actions and system events
- **Contact Form**: Public contact form with admin management
- **Settings Management**: System-wide configuration interface
- **Profile Management**: Users can update their profile and password

### Security Features
- Password hashing with bcrypt
- Session management with expiration
- CSRF protection ready
- SQL injection protection via prepared statements
- XSS protection with output escaping
- Secure session cookies

### UI/UX Features
- Modern, responsive design
- Clean and intuitive interface
- Mobile-friendly layout
- Flash messages for user feedback
- Pagination for large datasets
- Search and filtering capabilities

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3+
- Apache/Nginx with mod_rewrite
- PHPMailer (optional, for email functionality)

## Installation

### 1. Clone or Download
```bash
git clone https://github.com/yourusername/imerhaba-admin-panel.git
cd imerhaba-admin-panel
```

### 2. Configure Database
Edit `db/config.php` with your database credentials:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### 3. Initialize Database
Run the setup script to create tables and default admin user:
```bash
php db/setup.php
```

Or manually import the schema:
```bash
mysql -u your_user -p your_database < db/schema.sql
```

### 4. Configure Mail (Optional)
If you want email notifications for contact forms, configure your SMTP settings in `.env` file or environment variables:
```
MAIL_TRANSPORT=smtp
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
MAIL_FROM=noreply@yourdomain.com
MAIL_FROM_NAME=Imerhaba Admin
MAIL_TO=admin@yourdomain.com
```

### 5. Set Permissions
```bash
chmod 755 /path/to/admin-panel
chmod 644 /path/to/admin-panel/*.php
```

### 6. Configure Web Server

#### Apache (.htaccess included)
The project includes an `.htaccess` file for clean URLs.

#### Nginx
Add this to your nginx config:
```nginx
location / {
    try_files $uri $uri/ $uri.php?$query_string;
}
```

## Default Login Credentials

After installation, you can login with:
- **Username**: `admin`
- **Email**: `admin@imerhaba.com`
- **Password**: `admin123`

**Important**: Change the default password immediately after first login!

## Project Structure

```
imerhaba-admin-panel/
├── db/
│   ├── config.php          # Database configuration
│   ├── schema.sql          # Database schema
│   └── setup.php           # Database setup script
├── includes/
│   ├── auth.php            # Authentication class
│   ├── functions.php       # Helper functions
│   ├── header.php          # Layout header
│   └── footer.php          # Layout footer
├── mail/
│   ├── config.php          # Mail configuration
│   └── MailService.php     # Mail service class
├── index.php               # Homepage (redirects to dashboard/login)
├── login.php               # Login page
├── logout.php              # Logout handler
├── dashboard.php           # Admin dashboard
├── users.php               # User management
├── profile.php             # User profile
├── settings.php            # System settings
├── contact.php             # Public contact form
├── contact-messages.php    # Admin contact messages
├── activity-log.php        # Activity log viewer
├── .htaccess               # Apache rewrite rules
└── README.md               # This file
```

## Usage

### Accessing the Admin Panel
1. Navigate to `http://yourdomain.com` in your browser
2. You'll be redirected to the login page
3. Login with the default credentials
4. You'll be redirected to the dashboard

### Managing Users
1. Go to **Users** from the sidebar
2. Click **Add New User** to create a user
3. Use the search and filters to find users
4. Click the edit icon to modify a user
5. Click the delete icon to remove a user

### Managing Contact Messages
1. Go to **Contact Messages** from the sidebar
2. Filter by status (New, Read, Replied, Archived)
3. Click on a message to view details
4. Mark as replied or archive
5. Reply via email directly from the interface

### Viewing Activity Log
1. Go to **Activity Log** from the sidebar
2. Filter by user or action type
3. View detailed logs of all system activities

### System Settings
1. Go to **Settings** from the sidebar
2. Modify settings grouped by category
3. Click **Save Settings** to apply changes

### Profile Management
1. Go to **Profile** from the user menu
2. Update your personal information
3. Change your password
4. View account details

## Database Schema

### Users Table
- User authentication and profile information
- Supports multiple roles (admin, manager, user)
- Status tracking (active, inactive, suspended)

### Sessions Table
- Manages user sessions
- Tracks IP addresses and user agents
- Automatic expiration

### Settings Table
- System-wide configuration
- Categorized settings
- Multiple data types (string, number, boolean, json)

### Activity Log Table
- Tracks all user actions
- Links to entities (users, messages, etc.)
- IP address and user agent tracking

### Contact Messages Table
- Stores contact form submissions
- Status workflow (new, read, replied, archived)
- IP tracking for spam prevention

## API Endpoints

The system uses form-based interactions. Future versions may include REST API endpoints.

## Security Considerations

1. **Change Default Credentials**: Always change the default admin password
2. **Use HTTPS**: Deploy with SSL/TLS for production
3. **Database Security**: Use strong database passwords
4. **Session Security**: Configure secure session settings
5. **Regular Updates**: Keep PHP and dependencies updated
6. **Backup**: Regularly backup your database and files

## Customization

### Changing Colors
Edit the CSS variables in `includes/header.php`:
```css
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --success: #10b981;
    /* ... */
}
```

### Adding New Pages
1. Create a new PHP file
2. Include the header: `require_once __DIR__ . '/includes/header.php';`
3. Add your content
4. Include the footer: `require_once __DIR__ . '/includes/footer.php';`
5. Add navigation link in `includes/header.php`

### Adding Settings
Insert new settings directly into the database:
```sql
INSERT INTO settings (setting_key, setting_value, setting_type, description, category)
VALUES ('my_setting', 'value', 'string', 'Description', 'general');
```

## Troubleshooting

### Database Connection Error
- Check `db/config.php` credentials
- Verify database server is running
- Check database user permissions

### Login Not Working
- Clear browser cookies
- Check if database tables exist
- Verify default user was created

### Email Not Sending
- Check mail configuration in environment variables
- Verify SMTP credentials
- Check PHPMailer is installed
- Review error logs

### Session Issues
- Check session directory permissions
- Verify session configuration in php.ini
- Clear browser cookies

## Contributing

Contributions are welcome! Please follow these guidelines:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For issues, questions, or suggestions:
- Open an issue on GitHub
- Contact: admin@imerhaba.com

## Credits

Built with:
- PHP
- MySQL/MariaDB
- Font Awesome Icons
- Google Fonts (Inter)
- Modern CSS3

## Changelog

### Version 1.0.0 (Current)
- Initial release
- User authentication and management
- Dashboard with statistics
- Activity logging
- Contact form system
- Settings management
- Profile management
- Responsive design

## Roadmap

Future enhancements planned:
- [ ] Two-factor authentication
- [ ] Email notifications
- [ ] File upload management
- [ ] Advanced reporting
- [ ] Export functionality (CSV, PDF)
- [ ] REST API
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] Advanced permissions system
- [ ] Backup/restore functionality

---

Made with ❤️ by the Imerhaba Team
