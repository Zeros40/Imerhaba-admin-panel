# AI Agent Platform 🚀

A Full-Stack AI Agent Platform for building apps from prompts using multiple AI models including GPT-4, Claude 3.5 Sonnet, and OpenAI's o1.

## Features

- **Multi-AI Model Support**: GPT-4, GPT-4 Turbo, Claude 3.5 Sonnet, O1, O1-Mini
- **Code Generation**: Generate HTML/CSS/JavaScript, React, Vue, Angular, and API code from prompts
- **Project Management**: Create, manage, and version control your generated applications
- **User Authentication**: Secure JWT-based authentication system
- **Usage Tracking**: Monitor API token usage and costs
- **Code Refinement**: Iteratively improve generated code with refinement prompts
- **Multi-version Support**: Keep track of different versions of your generated apps
- **Export & Download**: Download generated code in various formats

## Tech Stack

### Backend
- **Language**: PHP 8.1+
- **Database**: MariaDB/MySQL
- **API**: RESTful API with JWT Authentication
- **AI Integration**: OpenAI API, Anthropic API

### Frontend
- **Framework**: Vanilla JavaScript (HTML/CSS/JS)
- **HTTP Client**: Axios
- **UI Components**: Custom CSS Framework
- **Architecture**: Single Page Application (SPA)

### DevOps
- **Package Manager**: Composer
- **CI/CD**: GitHub Actions
- **Version Control**: Git
- **Hosting**: Apache with mod_rewrite

## Installation

### Prerequisites

- PHP 8.1 or higher
- MariaDB/MySQL 5.7+
- Composer
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/imerhaba-admin-panel.git
cd imerhaba-admin-panel
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and add your configuration:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_HOST=127.0.0.1
DB_NAME=ai_agent_platform
DB_USER=root
DB_PASSWORD=

# OpenAI
OPENAI_API_KEY=your-openai-api-key
GPT4_MODEL=gpt-4
O1_MODEL=o1

# Anthropic
ANTHROPIC_API_KEY=your-anthropic-api-key
CLAUDE_MODEL=claude-3-5-sonnet-20241022

# JWT
JWT_SECRET=your-secret-key-change-in-production
```

### Step 4: Create Database

```bash
mysql -u root -p -e "CREATE DATABASE ai_agent_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 5: Run Migrations

```bash
php migrate.php
```

### Step 6: Create Necessary Directories

```bash
mkdir -p storage/{generated-apps,uploads} logs
chmod -R 755 storage logs
```

### Step 7: Start Development Server

```bash
php -S localhost:8000
```

Access the application at `http://localhost:8000`

## API Endpoints

### Authentication

#### Register User
```
POST /api/v1/auth/register
Content-Type: application/json

{
  "username": "john_doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "first_name": "John",
  "last_name": "Doe"
}
```

#### Login
```
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePassword123"
}

Response:
{
  "success": true,
  "data": {
    "token": "eyJhbGc...",
    "user": {
      "id": 1,
      "username": "john_doe",
      "email": "john@example.com",
      "first_name": "John",
      "last_name": "Doe"
    }
  }
}
```

#### Change Password
```
POST /api/v1/auth/change-password
Authorization: Bearer {token}
Content-Type: application/json

{
  "old_password": "OldPassword123",
  "new_password": "NewPassword123"
}
```

#### Logout
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

### Code Generation

#### Generate App
```
POST /api/v1/generation/generate
Authorization: Bearer {token}
Content-Type: application/json

{
  "project_name": "My Todo App",
  "prompt": "Create a todo application with add, edit, delete functionality",
  "ai_model": "gpt-4",
  "app_type": "react"
}

Response:
{
  "success": true,
  "data": {
    "project_id": 1,
    "generated_app_id": 1,
    "code": "import React, ...",
    "stats": {
      "input_tokens": 150,
      "output_tokens": 500,
      "processing_time": 2500
    }
  }
}
```

#### Regenerate Code
```
POST /api/v1/generation/{projectId}/regenerate
Authorization: Bearer {token}
Content-Type: application/json

{
  "prompt": "Updated description",
  "ai_model": "claude-3-5-sonnet-20241022"
}
```

#### Refine Code
```
POST /api/v1/generation/{projectId}/refine
Authorization: Bearer {token}
Content-Type: application/json

{
  "refinement": "Add dark mode support"
}
```

### Projects

#### List Projects
```
GET /api/v1/projects?page=1&limit=10
Authorization: Bearer {token}
```

#### Get Project Details
```
GET /api/v1/projects/{projectId}
Authorization: Bearer {token}
```

#### Update Project
```
PUT /api/v1/projects/{projectId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "description": "Updated description",
  "is_public": true
}
```

#### Delete Project
```
DELETE /api/v1/projects/{projectId}
Authorization: Bearer {token}
```

#### Export Project
```
GET /api/v1/projects/{projectId}/export
Authorization: Bearer {token}
```

### User

#### Get Profile
```
GET /api/v1/user/profile
Authorization: Bearer {token}
```

#### Update Profile
```
PUT /api/v1/user/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "bio": "AI enthusiast"
}
```

#### Get Statistics
```
GET /api/v1/user/stats
Authorization: Bearer {token}
```

#### Get Usage
```
GET /api/v1/user/usage?month=2024-01
Authorization: Bearer {token}
```

## Database Schema

### Users Table
- Stores user account information
- Supports multiple login sessions

### Projects Table
- Stores AI agent projects
- Tracks AI model used and generation status
- Supports public/private projects and templates

### Prompts Table
- Records all prompts submitted to AI models
- Tracks token usage and processing time
- Maintains prompt history for projects

### Generated Apps Table
- Stores generated application code
- Tracks language, framework, and dependencies
- Records version information

### Project Versions Table
- Maintains version history of projects
- Tracks changes and improvements over time

### User Sessions Table
- JWT token tracking
- Session management

### Usage Stats Table
- Monthly token usage tracking
- Cost calculation per model

## Supported App Types

1. **Web App** - Pure HTML/CSS/JavaScript
2. **React** - React with Hooks and functional components
3. **Vue** - Vue 3 with Composition API
4. **Angular** - Angular with TypeScript
5. **API** - REST API with backend framework
6. **CLI** - Command-line applications

## AI Models

### OpenAI
- **GPT-4**: Most capable, best for complex tasks
- **GPT-4 Turbo**: Faster GPT-4 variant
- **O1**: Reasoning-focused model
- **O1-Mini**: Smaller reasoning model

### Anthropic
- **Claude 3.5 Sonnet**: Balanced performance and speed

## Security Features

- JWT-based authentication
- Password hashing with bcrypt
- SQL injection protection via prepared statements
- CORS configuration
- Session management and token expiry
- API rate limiting ready (implement custom)

## Project Structure

```
imerhaba-admin-panel/
├── config/              # Configuration files
│   ├── constants.php
│   └── env.php
├── database/            # Database migrations and seeders
│   ├── migrations/
│   ├── seeders/
│   └── Migrator.php
├── src/                 # Source code
│   ├── Controllers/     # API controllers
│   ├── Middleware/      # Express-like middleware
│   ├── Services/        # Business logic
│   ├── Models/          # Data models
│   ├── Database.php     # Database connection
│   ├── Request.php      # Request handling
│   ├── Response.php     # Response handling
│   └── Router.php       # API routing
├── public/              # Frontend files
│   ├── index.html
│   ├── js/
│   │   └── app.js       # Main frontend application
│   └── css/
│       └── styles.css   # Global styles
├── storage/             # Generated apps and uploads
├── logs/                # Application logs
├── api.php              # API entry point
├── migrate.php          # Migration runner
└── README.md
```

## Usage Example

### 1. Register a New User

```javascript
const response = await axios.post('http://localhost:8000/api/v1/auth/register', {
  username: 'developer',
  email: 'dev@example.com',
  password: 'SecurePassword123'
});
```

### 2. Login

```javascript
const response = await axios.post('http://localhost:8000/api/v1/auth/login', {
  email: 'dev@example.com',
  password: 'SecurePassword123'
});

const token = response.data.data.token;
```

### 3. Generate an App

```javascript
const response = await axios.post(
  'http://localhost:8000/api/v1/generation/generate',
  {
    project_name: 'My Chat App',
    prompt: 'Create a real-time chat application with user authentication',
    ai_model: 'gpt-4',
    app_type: 'react'
  },
  { headers: { Authorization: `Bearer ${token}` } }
);

const generatedCode = response.data.data.code;
```

### 4. Refine the Generated Code

```javascript
const response = await axios.post(
  'http://localhost:8000/api/v1/generation/1/refine',
  {
    refinement: 'Add message timestamps and user avatars'
  },
  { headers: { Authorization: `Bearer ${token}` } }
);
```

## Troubleshooting

### Database Connection Error
- Ensure MariaDB/MySQL is running
- Verify database credentials in `.env`
- Check database exists: `mysql -u root -e "SHOW DATABASES;"`

### API Token Errors
- Verify JWT_SECRET is set in `.env`
- Check token is being sent in Authorization header
- Token may be expired, login again

### Generation Timeout
- Large prompts may take longer
- Check API key limits and quotas
- Increase max_execution_time in php.ini

## Configuration Options

### App Generation
- `APP_GENERATION_MAX_TOKENS`: Maximum tokens for generation (default: 4000)
- `APP_GENERATION_TEMPERATURE`: Creativity level 0-1 (default: 0.7)
- `DEFAULT_AI_MODEL`: Default model to use (default: gpt-4)

### JWT
- `JWT_SECRET`: Secret key for signing tokens
- `JWT_ALGORITHM`: Algorithm to use (default: HS256)
- `JWT_EXPIRY`: Token expiry time in seconds (default: 3600)

## Development

### Running Tests
```bash
composer test
```

### Code Linting
```bash
composer lint
```

### Code Style Fix
```bash
composer cs-fix
```

## License

MIT License - see LICENSE file for details

## Support

For issues and questions:
1. Check existing documentation
2. Review API endpoint examples
3. Check browser console and server logs
4. Create an issue on GitHub

## Future Enhancements

- [ ] Real-time code generation with WebSocket
- [ ] Code execution sandbox
- [ ] Team collaboration features
- [ ] Advanced analytics and insights
- [ ] Custom templates and components
- [ ] Plugin system for extending functionality
- [ ] Mobile app version
- [ ] Automated testing generation
- [ ] Database schema generation
- [ ] API documentation generation

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Credits

Built with ❤️ by the AI Agent Platform Team
