'use client';

import { useState } from 'react';
import { useStore } from '@/lib/store';
import { FiSun, FiMoon, FiGlobe, FiUser, FiMail, FiBell } from 'react-icons/fi';
import { updateProfile } from 'firebase/auth';
import { auth } from '@/lib/firebase';

export default function SettingsPage() {
  const { user, theme, toggleTheme, language, setLanguage } = useStore();
  const [displayName, setDisplayName] = useState(user?.displayName || '');
  const [emailNotifications, setEmailNotifications] = useState(true);
  const [marketingEmails, setMarketingEmails] = useState(false);
  const [saving, setSaving] = useState(false);

  const handleSaveProfile = async () => {
    if (!user) return;

    setSaving(true);
    try {
      await updateProfile(user, { displayName });
      alert('Profile updated successfully!');
    } catch (error) {
      console.error('Error updating profile:', error);
      alert('Failed to update profile');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-4xl font-bold mb-2">Settings</h1>
        <p className="text-gray-400">Manage your account preferences</p>
      </div>

      {/* Profile Settings */}
      <div className="glass rounded-xl p-6">
        <div className="flex items-center space-x-3 mb-6">
          <FiUser className="w-6 h-6 text-purple-400" />
          <h2 className="text-2xl font-bold">Profile Settings</h2>
        </div>

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">Display Name</label>
            <input
              type="text"
              value={displayName}
              onChange={(e) => setDisplayName(e.target.value)}
              className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Email</label>
            <div className="flex items-center space-x-2 px-4 py-2 bg-white/5 border border-white/10 rounded-lg">
              <FiMail className="text-gray-400" />
              <span className="text-gray-400">{user?.email}</span>
            </div>
            <p className="text-sm text-gray-500 mt-1">Email cannot be changed</p>
          </div>

          <button
            onClick={handleSaveProfile}
            disabled={saving}
            className="px-6 py-2 bg-gradient-purple rounded-lg font-semibold hover:opacity-90 transition disabled:opacity-50"
          >
            {saving ? 'Saving...' : 'Save Changes'}
          </button>
        </div>
      </div>

      {/* Appearance */}
      <div className="glass rounded-xl p-6">
        <div className="flex items-center space-x-3 mb-6">
          {theme === 'dark' ? (
            <FiMoon className="w-6 h-6 text-purple-400" />
          ) : (
            <FiSun className="w-6 h-6 text-purple-400" />
          )}
          <h2 className="text-2xl font-bold">Appearance</h2>
        </div>

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">Theme</label>
            <div className="flex space-x-4">
              <button
                onClick={toggleTheme}
                className={`flex-1 py-3 rounded-lg border-2 transition ${
                  theme === 'light'
                    ? 'border-purple-500 bg-purple-500/10'
                    : 'border-white/10 hover:border-white/20'
                }`}
              >
                <FiSun className="w-6 h-6 mx-auto mb-1" />
                <span className="text-sm">Light</span>
              </button>
              <button
                onClick={toggleTheme}
                className={`flex-1 py-3 rounded-lg border-2 transition ${
                  theme === 'dark'
                    ? 'border-purple-500 bg-purple-500/10'
                    : 'border-white/10 hover:border-white/20'
                }`}
              >
                <FiMoon className="w-6 h-6 mx-auto mb-1" />
                <span className="text-sm">Dark</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Language */}
      <div className="glass rounded-xl p-6">
        <div className="flex items-center space-x-3 mb-6">
          <FiGlobe className="w-6 h-6 text-purple-400" />
          <h2 className="text-2xl font-bold">Language & Region</h2>
        </div>

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">Language</label>
            <select
              value={language}
              onChange={(e) => setLanguage(e.target.value as 'en' | 'ar' | 'bs')}
              className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            >
              <option value="en">English</option>
              <option value="ar">العربية (Arabic)</option>
              <option value="bs">Bosanski (Bosnian)</option>
            </select>
          </div>
        </div>
      </div>

      {/* Notifications */}
      <div className="glass rounded-xl p-6">
        <div className="flex items-center space-x-3 mb-6">
          <FiBell className="w-6 h-6 text-purple-400" />
          <h2 className="text-2xl font-bold">Notifications</h2>
        </div>

        <div className="space-y-4">
          <label className="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer">
            <div>
              <p className="font-semibold">Email Notifications</p>
              <p className="text-sm text-gray-400">Receive updates about your account</p>
            </div>
            <input
              type="checkbox"
              checked={emailNotifications}
              onChange={(e) => setEmailNotifications(e.target.checked)}
              className="w-5 h-5"
            />
          </label>

          <label className="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer">
            <div>
              <p className="font-semibold">Marketing Emails</p>
              <p className="text-sm text-gray-400">Get tips and product updates</p>
            </div>
            <input
              type="checkbox"
              checked={marketingEmails}
              onChange={(e) => setMarketingEmails(e.target.checked)}
              className="w-5 h-5"
            />
          </label>
        </div>
      </div>

      {/* Danger Zone */}
      <div className="glass rounded-xl p-6 border-2 border-red-500/20">
        <h2 className="text-2xl font-bold mb-4 text-red-500">Danger Zone</h2>
        <div className="space-y-4">
          <button className="w-full py-3 border-2 border-red-500 text-red-500 rounded-lg font-semibold hover:bg-red-500/10 transition">
            Delete Account
          </button>
        </div>
      </div>
    </div>
  );
}
