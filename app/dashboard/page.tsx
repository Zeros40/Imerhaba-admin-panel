'use client';

import { useStore } from '@/lib/store';
import Link from 'next/link';
import { FiVideo, FiEdit3, FiMail, FiCalendar, FiFileText, FiMessageSquare, FiBook, FiUsers, FiTrendingUp, FiZap } from 'react-icons/fi';
import { motion } from 'framer-motion';

const tools = [
  {
    name: 'YouTube Video Kit',
    description: 'Create faceless video scripts and ideas',
    icon: FiVideo,
    href: '/dashboard/youtube-kit',
    color: 'from-red-500 to-pink-500',
    score: 9.8,
  },
  {
    name: 'Caption Generator',
    description: 'Generate engaging social media captions',
    icon: FiEdit3,
    href: '/dashboard/caption-generator',
    color: 'from-blue-500 to-cyan-500',
    score: 9.5,
  },
  {
    name: 'Cold Outreach',
    description: 'Write compelling cold emails',
    icon: FiMail,
    href: '/dashboard/cold-outreach',
    color: 'from-purple-500 to-indigo-500',
    score: 8.5,
  },
  {
    name: 'Chatbot Script',
    description: 'Create intelligent chatbot flows',
    icon: FiMessageSquare,
    href: '/dashboard/chatbot-script',
    color: 'from-green-500 to-emerald-500',
    score: 8.5,
  },
  {
    name: 'Lead Magnet',
    description: 'Build valuable eBooks and guides',
    icon: FiBook,
    href: '/dashboard/lead-magnet',
    color: 'from-orange-500 to-amber-500',
    score: 8.0,
  },
  {
    name: 'Post Scheduler',
    description: 'Plan your content calendar',
    icon: FiCalendar,
    href: '/dashboard/post-scheduler',
    color: 'from-teal-500 to-cyan-500',
    score: 8.0,
  },
  {
    name: 'Proposal Generator',
    description: 'Create winning client proposals',
    icon: FiFileText,
    href: '/dashboard/proposal-generator',
    color: 'from-violet-500 to-purple-500',
    score: 7.8,
  },
  {
    name: 'CRM Follow-Up',
    description: 'Automate client communications',
    icon: FiUsers,
    href: '/dashboard/crm-followup',
    color: 'from-pink-500 to-rose-500',
    score: 7.3,
  },
];

export default function DashboardPage() {
  const { user, credits, subscription } = useStore();

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-4xl font-bold mb-2">
            Welcome back, {user?.displayName || 'User'}!
          </h1>
          <p className="text-gray-400">
            What would you like to create today?
          </p>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="glass rounded-xl p-6"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-400 mb-1">Available Credits</p>
              <p className="text-3xl font-bold">{credits}</p>
            </div>
            <div className="w-12 h-12 rounded-lg bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center">
              <FiZap className="w-6 h-6" />
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="glass rounded-xl p-6"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-400 mb-1">Active Plan</p>
              <p className="text-3xl font-bold capitalize">{subscription}</p>
            </div>
            <div className="w-12 h-12 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
              <FiTrendingUp className="w-6 h-6" />
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="glass rounded-xl p-6"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-400 mb-1">Tools Available</p>
              <p className="text-3xl font-bold">8</p>
            </div>
            <div className="w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-amber-500 flex items-center justify-center">
              <FiZap className="w-6 h-6" />
            </div>
          </div>
        </motion.div>
      </div>

      {/* Tools Grid */}
      <div>
        <h2 className="text-2xl font-bold mb-6">Your AI Tools</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {tools.map((tool, index) => (
            <motion.div
              key={tool.href}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.05 }}
            >
              <Link href={tool.href}>
                <div className="glass rounded-xl p-6 hover:scale-105 transition-transform cursor-pointer h-full">
                  <div className={`w-12 h-12 rounded-lg bg-gradient-to-r ${tool.color} flex items-center justify-center mb-4`}>
                    <tool.icon className="w-6 h-6 text-white" />
                  </div>
                  <div className="flex items-center justify-between mb-2">
                    <h3 className="font-bold">{tool.name}</h3>
                    <span className="text-sm text-purple-400 font-semibold">
                      {tool.score}/10
                    </span>
                  </div>
                  <p className="text-sm text-gray-400">{tool.description}</p>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Upgrade CTA */}
      {subscription === 'free' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4 }}
          className="glass rounded-xl p-8 text-center"
        >
          <h3 className="text-2xl font-bold mb-2">Unlock Full Potential</h3>
          <p className="text-gray-400 mb-6">
            Upgrade to Pro and get 500 monthly credits, priority support, and unlimited exports
          </p>
          <Link
            href="/dashboard/subscription"
            className="inline-block px-8 py-3 bg-gradient-purple rounded-lg font-semibold hover:opacity-90 transition"
          >
            Upgrade Now
          </Link>
        </motion.div>
      )}
    </div>
  );
}
