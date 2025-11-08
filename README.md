# AIProStation - AI-Powered SaaS Platform

![AIProStation](https://img.shields.io/badge/AIProStation-v1.0.0-purple)
![Next.js](https://img.shields.io/badge/Next.js-14.0-black)
![Firebase](https://img.shields.io/badge/Firebase-10.7-orange)
![Stripe](https://img.shields.io/badge/Stripe-Integrated-blue)

AIProStation is a comprehensive SaaS platform that empowers marketers, creators, and freelancers with 8 powerful AI tools designed to streamline content creation, marketing automation, and business growth.

## 🌟 Features

### 8 Powerful AI Tools

1. **Faceless YouTube Video Kit** (9.8/10) - Create viral YouTube scripts without showing your face
2. **Caption & Content Generator** (9.5/10) - Generate engaging captions for all social platforms
3. **Cold Outreach Writer** (8.5/10) - Craft persuasive emails that convert
4. **Chatbot Script Generator** (8.5/10) - Build intelligent chatbot conversations
5. **Lead Magnet / eBook Builder** (8.0/10) - Create valuable lead magnets in minutes
6. **Post Scheduler Assistant** (8.0/10) - Plan your content calendar strategically
7. **Proposal Generator** (7.8/10) - Create winning client proposals
8. **CRM Follow-Up Writer** (7.3/10) - Automate your client communications

### Platform Features

- 🔐 **Firebase Authentication** - Secure login with email/password and Google OAuth
- 💳 **Stripe Integration** - Flexible subscription plans with secure payments
- 📊 **Usage Tracking** - Monitor your credits and tool usage
- 🌓 **Dark/Light Mode** - Beautiful UI with theme switching
- 🌍 **Multi-Language Support** - English, Arabic, and Bosnian
- 📱 **Mobile-First Design** - Fully responsive across all devices
- 🎨 **Modern UI/UX** - Glass morphism, gradients, and smooth animations

## 🚀 Getting Started

### Prerequisites

- Node.js 18+ and npm
- Firebase account
- Stripe account (for payments)
- OpenAI API key (optional, for AI generation)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/Zeros40/Imerhaba-admin-panel.git
cd Imerhaba-admin-panel
```

2. **Install dependencies**
```bash
npm install
```

3. **Set up environment variables**

Create a `.env.local` file in the root directory:

```env
# Firebase Configuration
NEXT_PUBLIC_FIREBASE_API_KEY=your_firebase_api_key
NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
NEXT_PUBLIC_FIREBASE_PROJECT_ID=your_project_id
NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET=your_project.appspot.com
NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=your_sender_id
NEXT_PUBLIC_FIREBASE_APP_ID=your_app_id

# Stripe Configuration
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=your_stripe_publishable_key
STRIPE_SECRET_KEY=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# OpenAI API (optional)
OPENAI_API_KEY=your_openai_api_key

# App URL
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

4. **Run the development server**
```bash
npm run dev
```

5. **Open your browser**
Navigate to [http://localhost:3000](http://localhost:3000)

## 🏗️ Project Structure

```
aiprostation/
├── app/                      # Next.js App Router
│   ├── api/                  # API routes
│   │   └── generate/         # AI generation endpoint
│   ├── auth/                 # Authentication pages
│   │   ├── login/
│   │   └── register/
│   ├── dashboard/            # Dashboard & tools
│   │   ├── youtube-kit/
│   │   ├── caption-generator/
│   │   ├── cold-outreach/
│   │   ├── post-scheduler/
│   │   ├── chatbot-script/
│   │   ├── lead-magnet/
│   │   ├── proposal-generator/
│   │   ├── crm-followup/
│   │   ├── subscription/
│   │   └── settings/
│   ├── layout.tsx            # Root layout
│   └── page.tsx              # Landing page
├── components/               # Reusable components
│   ├── AuthProvider.tsx
│   ├── ThemeProvider.tsx
│   ├── Navbar.tsx
│   └── AIToolLayout.tsx
├── lib/                      # Utilities & configs
│   ├── firebase.ts           # Firebase setup
│   ├── stripe.ts             # Stripe configuration
│   └── store.ts              # Zustand state management
├── styles/                   # Global styles
│   └── globals.css
├── public/                   # Static assets
└── ...config files
```

## 💰 Subscription Plans

### Free Plan
- 10 AI generations per month
- Access to 2 tools
- Basic support
- Community access

### Starter Plan - $29/month
- 100 AI generations per month
- Access to all 8 tools
- Priority support
- Advanced analytics
- Export capabilities

### Pro Plan - $79/month (Most Popular)
- 500 AI generations per month
- Access to all 8 tools
- Priority support
- Advanced analytics
- Unlimited exports
- API access
- White-label options

### Enterprise Plan - $299/month
- Unlimited AI generations
- Access to all 8 tools
- Dedicated support
- Custom integrations
- Team collaboration
- Priority API access
- Custom training
- SLA guarantee

## 🛠️ Tech Stack

- **Frontend**: Next.js 14, React 18, TypeScript
- **Styling**: Tailwind CSS, Framer Motion
- **Authentication**: Firebase Auth
- **Database**: Firestore
- **Payments**: Stripe
- **State Management**: Zustand
- **AI Integration**: OpenAI API (GPT-4)
- **Deployment**: Vercel (recommended)

## 🎨 Design System

### Colors
- **Primary Purple**: `#8B5CF6`
- **Primary Slate**: `#475569`
- **Gradient**: Linear gradient from Electric Purple to Slate

### Typography
- **Font**: Inter (Google Fonts)

### Theme
- Dark mode by default
- Light mode available
- Glass morphism effects
- Smooth animations

## 🔒 Security

- Firebase Authentication for secure user management
- Stripe for PCI-compliant payment processing
- Environment variables for sensitive data
- Server-side API routes for protected operations

## 📱 Responsive Design

AIProStation is fully responsive and optimized for:
- Desktop (1920px+)
- Laptop (1024px - 1919px)
- Tablet (768px - 1023px)
- Mobile (320px - 767px)

## 🌍 Internationalization

Supports three languages:
- English (en)
- Arabic (ar) - RTL support
- Bosnian (bs)

## 🚢 Deployment

### Vercel (Recommended)

1. Push your code to GitHub
2. Import your repository in Vercel
3. Add environment variables
4. Deploy

### Other Platforms

AIProStation can be deployed to any platform that supports Next.js:
- Netlify
- AWS Amplify
- Railway
- Render

## 📊 Firebase Setup

1. Create a new Firebase project
2. Enable Authentication (Email/Password and Google)
3. Create a Firestore database
4. Set up security rules:

```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /users/{userId} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
    }
  }
}
```

## 💳 Stripe Setup

1. Create a Stripe account
2. Create products and prices for each plan
3. Set up webhooks for subscription events
4. Add the webhook endpoint: `/api/webhooks/stripe`

## 🤖 OpenAI Integration

To enable AI generation:

1. Get an OpenAI API key
2. Add it to `.env.local`
3. The platform will automatically use GPT-4 for content generation

## 📈 Analytics & Monitoring

- Firebase Analytics (built-in)
- Track user engagement
- Monitor tool usage
- Credit consumption tracking

## 🧪 Testing

```bash
# Run tests (when implemented)
npm test

# Build for production
npm run build

# Start production server
npm start
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 💌 Support

For support, email support@aiprostation.com or join our Discord community.

## 🙏 Acknowledgments

- Next.js team for the amazing framework
- Firebase for authentication and database
- Stripe for payment processing
- OpenAI for AI capabilities
- The open-source community

## 🎯 Roadmap

- [ ] Advanced analytics dashboard
- [ ] Team collaboration features
- [ ] API access for enterprise users
- [ ] Mobile app (iOS & Android)
- [ ] More AI tools
- [ ] Integration marketplace
- [ ] White-label options

---

**Built with 💜 by the AIProStation team**

*Empowering people from all backgrounds to succeed*
