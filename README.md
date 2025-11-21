# ZODIAC 13 - AI Content Generation Platform

Convert any website URL into landing pages, sales proposals, pitch decks, SEO audits, and more with AI.

## Features

### 🎯 Tier 1 - Core Website Engine
- Landing Pages (Short & Long)
- Hero Sections
- Pain/Solution Blocks
- FAQ Sections
- Social Media Posts
- Buyer Personas
- Product Descriptions
- Brand Basics

### 📊 Tier 2 - Business & Marketing Pack (Coming Soon)
- Sales Proposals
- WhatsApp Scripts
- Email Sequences
- Business Plans
- Competitor Analysis
- Pitch Deck Outlines

### 🚀 Tier 3 - Enterprise Growth Suite (Coming Soon)
- Full Pitch Decks
- Brand Strategies
- 6-Month Plans
- SEO Audits
- SOPs & Workflows

### 🌟 Tier 4 - Complete All-In-One (Coming Soon)
- White-Label License
- API Access
- Multi-Language Support
- Advanced Analytics

## Tech Stack

### Backend
- **Runtime**: Node.js 20+
- **Framework**: Express.js
- **Language**: TypeScript
- **Database**: PostgreSQL
- **ORM**: Prisma
- **AI**: Anthropic Claude API
- **Scraping**: Puppeteer

### Frontend
- **Framework**: React 18
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **Build Tool**: Vite
- **State Management**: Zustand
- **HTTP Client**: Axios
- **Router**: React Router v6

### Infrastructure
- **Containerization**: Docker
- **Compose**: Docker Compose
- **Development**: Concurrent dev servers

## Quick Start

### Prerequisites
- Node.js 20+
- Docker & Docker Compose (optional)
- PostgreSQL 16+ (or use Docker)
- Anthropic Claude API Key

### Installation

1. **Clone and Navigate**
```bash
cd Imerhaba-admin-panel
```

2. **Set up Backend**
```bash
cd backend
cp .env.example .env
# Edit .env with your Claude API key and database URL
npm install
npm run build
npx prisma migrate deploy
npm run seed
npm run dev
```

3. **Set up Frontend** (in new terminal)
```bash
cd frontend
npm install
npm run dev
```

The app will be available at:
- Frontend: http://localhost:3000
- Backend API: http://localhost:5000

### Using Docker Compose

```bash
# Set your Claude API key
export CLAUDE_API_KEY="sk-..."

# Start all services
docker-compose up

# Run migrations
docker-compose exec backend npm run migrate
```

## Project Structure

```
zodiac-13/
├── backend/
│   ├── src/
│   │   ├── services/          # Business logic (extraction, generation, export)
│   │   ├── controllers/       # Request handlers
│   │   ├── routes/            # API endpoints
│   │   ├── middleware/        # Auth, validation, error handling
│   │   └── server.ts          # Express app setup
│   ├── prisma/
│   │   ├── schema.prisma      # Database schema
│   │   └── migrations/        # Database migrations
│   └── package.json
│
├── frontend/
│   ├── src/
│   │   ├── pages/             # Route pages (Scan, Review, Results, etc.)
│   │   ├── components/        # Reusable UI components
│   │   ├── services/          # API client
│   │   ├── context/           # Zustand store
│   │   ├── types/             # TypeScript types
│   │   ├── hooks/             # Custom React hooks
│   │   └── App.tsx            # Main app component
│   └── package.json
│
└── docker-compose.yml         # Development environment setup
```

## API Endpoints

### Projects
- `POST /api/projects` - Create new project
- `GET /api/projects` - List user projects
- `GET /api/projects/:projectId` - Get project details
- `DELETE /api/projects/:projectId` - Delete project

### Extraction
- `POST /api/projects/:projectId/scan` - Scan website and extract data
- `GET /api/projects/:projectId/profile` - Get extracted profile

### Generation
- `POST /api/projects/:projectId/generate` - Generate outputs
- `GET /api/projects/:projectId/outputs` - Get generated outputs

### Export
- `GET /api/projects/:projectId/export?format=pdf|html|docx` - Export outputs

## Database Schema

### Core Models
- **User** - User accounts with roles and plans
- **Project** - Website analysis projects
- **BusinessProfile** - Extracted website data
- **Output** - Generated content pieces
- **Translation** - Multilingual UI labels

### Supported Languages
- English (EN)
- Arabic (AR)
- Bosnian (BS)

## Environment Variables

### Backend (.env)
```
DATABASE_URL=postgresql://user:password@localhost:5432/zodiac13
PORT=5000
NODE_ENV=development
CLAUDE_API_KEY=sk-...your-key...
FRONTEND_URL=http://localhost:3000
LOG_LEVEL=debug
```

### Frontend (.env)
```
REACT_APP_API_URL=http://localhost:5000/api
```

## Development Workflow

1. **Create feature branch**
```bash
git checkout -b claude/feature-name-session-id
```

2. **Make changes and test**
```bash
cd backend && npm run dev
cd frontend && npm run dev
```

3. **Commit changes**
```bash
git add .
git commit -m "Add feature: description"
```

4. **Push to branch**
```bash
git push -u origin claude/feature-name-session-id
```

## Tier Prompts

Each output type has specialized prompts:

### Tier 1 Prompts
- **Landing Page**: 500-2000 words with hero, benefits, CTAs
- **Hero Section**: Attention-grabbing headline + subheadline
- **Pain/Solution**: 3-5 problems with solutions
- **FAQ**: 10-15 customer questions answered
- **Social Posts**: 10 posts across different platforms

See `/backend/src/services/generation.service.ts` for all prompts.

## Features Coming Soon

- ✅ Tier 1 Content Generation
- ⏳ Tier 2 Business Outputs
- ⏳ Tier 3 Enterprise Suite
- ⏳ Tier 4 White-Label + API
- ⏳ Real-time generation progress
- ⏳ Advanced analytics dashboard
- ⏳ Team collaboration features
- ⏳ Custom template builder

## Contributing

1. Create a feature branch
2. Make your changes
3. Test thoroughly
4. Commit with clear messages
5. Push and create a pull request

## Support

For issues or feature requests, please open an issue on GitHub.

## License

Proprietary - All Rights Reserved

---

**Built with ❤️ using Claude AI**
