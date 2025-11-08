'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { useStore } from '@/lib/store';
import Link from 'next/link';
import { FiVideo, FiEdit3, FiMail, FiCalendar, FiFileText, FiMessageSquare, FiBook, FiUsers, FiSettings, FiLogOut, FiHome, FiCreditCard } from 'react-icons/fi';
import { signOut } from 'firebase/auth';
import { auth } from '@/lib/firebase';

const tools = [
  { name: 'Dashboard', icon: FiHome, href: '/dashboard', score: null },
  { name: 'YouTube Video Kit', icon: FiVideo, href: '/dashboard/youtube-kit', score: 9.8 },
  { name: 'Caption Generator', icon: FiEdit3, href: '/dashboard/caption-generator', score: 9.5 },
  { name: 'Cold Outreach', icon: FiMail, href: '/dashboard/cold-outreach', score: 8.5 },
  { name: 'Chatbot Script', icon: FiMessageSquare, href: '/dashboard/chatbot-script', score: 8.5 },
  { name: 'Lead Magnet', icon: FiBook, href: '/dashboard/lead-magnet', score: 8.0 },
  { name: 'Post Scheduler', icon: FiCalendar, href: '/dashboard/post-scheduler', score: 8.0 },
  { name: 'Proposal Generator', icon: FiFileText, href: '/dashboard/proposal-generator', score: 7.8 },
  { name: 'CRM Follow-Up', icon: FiUsers, href: '/dashboard/crm-followup', score: 7.3 },
];

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { user, loading, credits, subscription } = useStore();
  const router = useRouter();

  useEffect(() => {
    if (!loading && !user) {
      router.push('/auth/login');
    }
  }, [user, loading, router]);

  const handleSignOut = async () => {
    await signOut(auth);
    router.push('/');
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center">
        <div className="spinner"></div>
      </div>
    );
  }

  if (!user) {
    return null;
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
      <div className="flex">
        {/* Sidebar */}
        <aside className="w-64 min-h-screen glass border-r border-white/10 p-6 fixed left-0 top-0 overflow-y-auto">
          {/* Logo */}
          <Link href="/dashboard" className="flex items-center space-x-2 mb-8">
            <div className="w-10 h-10 rounded-lg bg-gradient-purple flex items-center justify-center">
              <span className="text-white font-bold text-xl">AI</span>
            </div>
            <span className="text-xl font-bold gradient-text">AIProStation</span>
          </Link>

          {/* User Info */}
          <div className="mb-8 p-4 glass rounded-lg">
            <p className="text-sm text-gray-400 mb-1">Credits</p>
            <p className="text-2xl font-bold">{credits}</p>
            <p className="text-xs text-gray-400 mt-2">
              {subscription.charAt(0).toUpperCase() + subscription.slice(1)} Plan
            </p>
          </div>

          {/* Navigation */}
          <nav className="space-y-2 mb-8">
            {tools.map((tool) => (
              <Link
                key={tool.href}
                href={tool.href}
                className="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-white/5 transition group"
              >
                <div className="flex items-center space-x-3">
                  <tool.icon className="w-5 h-5 text-purple-400 group-hover:text-purple-300" />
                  <span className="text-sm">{tool.name}</span>
                </div>
                {tool.score && (
                  <span className="text-xs text-gray-400">{tool.score}</span>
                )}
              </Link>
            ))}
          </nav>

          {/* Bottom Actions */}
          <div className="space-y-2 border-t border-white/10 pt-4">
            <Link
              href="/dashboard/subscription"
              className="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition"
            >
              <FiCreditCard className="w-5 h-5" />
              <span className="text-sm">Subscription</span>
            </Link>
            <Link
              href="/dashboard/settings"
              className="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition"
            >
              <FiSettings className="w-5 h-5" />
              <span className="text-sm">Settings</span>
            </Link>
            <button
              onClick={handleSignOut}
              className="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-red-500/10 transition text-red-500"
            >
              <FiLogOut className="w-5 h-5" />
              <span className="text-sm">Sign Out</span>
            </button>
          </div>
        </aside>

        {/* Main Content */}
        <main className="flex-1 ml-64 p-8">
          {children}
        </main>
      </div>
    </div>
  );
}
