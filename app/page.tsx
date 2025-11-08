'use client';

import Navbar from '@/components/Navbar';
import Link from 'next/link';
import { FiVideo, FiEdit3, FiMail, FiCalendar, FiFileText, FiMessageSquare, FiBook, FiUsers, FiCheck, FiZap, FiShield, FiTrendingUp } from 'react-icons/fi';
import { motion } from 'framer-motion';
import { SUBSCRIPTION_PLANS } from '@/lib/stripe';

const tools = [
  {
    icon: FiVideo,
    name: 'Faceless YouTube Video Kit',
    description: 'Create viral YouTube content without showing your face',
    score: 9.8,
    color: 'from-red-500 to-pink-500',
  },
  {
    icon: FiEdit3,
    name: 'Caption & Content Generator',
    description: 'Generate engaging captions for all social platforms',
    score: 9.5,
    color: 'from-blue-500 to-cyan-500',
  },
  {
    icon: FiMail,
    name: 'Cold Outreach Writer',
    description: 'Craft persuasive emails that convert',
    score: 8.5,
    color: 'from-purple-500 to-indigo-500',
  },
  {
    icon: FiMessageSquare,
    name: 'Chatbot Script Generator',
    description: 'Build intelligent chatbot conversations',
    score: 8.5,
    color: 'from-green-500 to-emerald-500',
  },
  {
    icon: FiBook,
    name: 'Lead Magnet / eBook Builder',
    description: 'Create valuable lead magnets in minutes',
    score: 8.0,
    color: 'from-orange-500 to-amber-500',
  },
  {
    icon: FiCalendar,
    name: 'Post Scheduler Assistant',
    description: 'Plan and schedule your content strategy',
    score: 8.0,
    color: 'from-teal-500 to-cyan-500',
  },
  {
    icon: FiFileText,
    name: 'Proposal Generator',
    description: 'Create winning proposals for clients',
    score: 7.8,
    color: 'from-violet-500 to-purple-500',
  },
  {
    icon: FiUsers,
    name: 'CRM Follow-Up Writer',
    description: 'Automate your client communications',
    score: 7.3,
    color: 'from-pink-500 to-rose-500',
  },
];

const benefits = [
  {
    icon: FiZap,
    title: 'Lightning Fast',
    description: 'Generate professional content in seconds, not hours',
  },
  {
    icon: FiShield,
    title: 'Data Security',
    description: 'Enterprise-grade security with Firebase & Stripe',
  },
  {
    icon: FiTrendingUp,
    title: 'Scale Your Business',
    description: 'Tools designed to grow with your success',
  },
];

export default function Home() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
      <Navbar />

      {/* Hero Section */}
      <section className="pt-32 pb-20 px-4">
        <div className="max-w-7xl mx-auto text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
          >
            <h1 className="text-5xl md:text-7xl font-bold mb-6 leading-tight">
              Empower Your Business with
              <span className="gradient-text block mt-2">AI-Powered Tools</span>
            </h1>
            <p className="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
              8 powerful AI tools designed for marketers, creators, and freelancers.
              Create content, automate workflows, and scale your business.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/auth/register"
                className="px-8 py-4 bg-gradient-purple rounded-lg text-lg font-semibold hover:opacity-90 transition transform hover:scale-105"
              >
                Start Free Trial
              </Link>
              <Link
                href="#features"
                className="px-8 py-4 border-2 border-purple-500 rounded-lg text-lg font-semibold hover:bg-purple-500/10 transition"
              >
                Explore Tools
              </Link>
            </div>
          </motion.div>

          {/* Stats */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8"
          >
            {benefits.map((benefit, index) => (
              <div key={index} className="glass rounded-xl p-6">
                <benefit.icon className="w-12 h-12 mx-auto mb-4 text-purple-400" />
                <h3 className="text-xl font-bold mb-2">{benefit.title}</h3>
                <p className="text-gray-400">{benefit.description}</p>
              </div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" className="py-20 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4">
              8 Powerful AI Tools
            </h2>
            <p className="text-xl text-gray-300">
              Everything you need to succeed in one platform
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {tools.map((tool, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="glass rounded-xl p-6 hover:scale-105 transition-transform cursor-pointer"
              >
                <div className={`w-12 h-12 rounded-lg bg-gradient-to-r ${tool.color} flex items-center justify-center mb-4`}>
                  <tool.icon className="w-6 h-6 text-white" />
                </div>
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-bold">{tool.name}</h3>
                  <span className="text-sm font-semibold text-purple-400">
                    {tool.score}/10
                  </span>
                </div>
                <p className="text-gray-400 text-sm">{tool.description}</p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Pricing Section */}
      <section id="pricing" className="py-20 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4">
              Simple, Transparent Pricing
            </h2>
            <p className="text-xl text-gray-300">
              Choose the plan that fits your needs
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {Object.entries(SUBSCRIPTION_PLANS).map(([key, plan], index) => (
              <motion.div
                key={key}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className={`glass rounded-xl p-8 ${
                  key === 'pro' ? 'ring-2 ring-purple-500 scale-105' : ''
                }`}
              >
                {key === 'pro' && (
                  <div className="bg-gradient-purple text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                    Most Popular
                  </div>
                )}
                <h3 className="text-2xl font-bold mb-2">{plan.name}</h3>
                <div className="mb-6">
                  <span className="text-5xl font-bold">${plan.price}</span>
                  <span className="text-gray-400">/month</span>
                </div>
                <ul className="space-y-3 mb-8">
                  {plan.features.map((feature, i) => (
                    <li key={i} className="flex items-start">
                      <FiCheck className="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                      <span className="text-sm">{feature}</span>
                    </li>
                  ))}
                </ul>
                <Link
                  href="/auth/register"
                  className={`block w-full py-3 rounded-lg text-center font-semibold transition ${
                    key === 'pro'
                      ? 'bg-gradient-purple hover:opacity-90'
                      : 'border-2 border-purple-500 hover:bg-purple-500/10'
                  }`}
                >
                  Get Started
                </Link>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 px-4">
        <div className="max-w-4xl mx-auto text-center glass rounded-2xl p-12">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">
            Ready to Transform Your Business?
          </h2>
          <p className="text-xl text-gray-300 mb-8">
            Join thousands of creators and marketers who are already using AIProStation
          </p>
          <Link
            href="/auth/register"
            className="inline-block px-8 py-4 bg-gradient-purple rounded-lg text-lg font-semibold hover:opacity-90 transition transform hover:scale-105"
          >
            Start Your Free Trial
          </Link>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 px-4 border-t border-white/10">
        <div className="max-w-7xl mx-auto text-center text-gray-400">
          <p>&copy; 2024 AIProStation. Empowering creators worldwide.</p>
          <p className="mt-2 text-sm">Built with purpose, powered by AI</p>
        </div>
      </footer>
    </div>
  );
}
