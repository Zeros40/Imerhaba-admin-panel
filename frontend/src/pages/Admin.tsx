import { useState } from 'react'

export default function Admin() {
  const [activeTab, setActiveTab] = useState('dashboard')

  const tabs = [
    { id: 'dashboard', label: '📊 Dashboard', icon: '📊' },
    { id: 'users', label: '👥 Users', icon: '👥' },
    { id: 'projects', label: '📁 Projects', icon: '📁' },
    { id: 'plans', label: '💳 Plans', icon: '💳' },
    { id: 'templates', label: '📋 Templates', icon: '📋' },
    { id: 'translations', label: '🌐 Translations', icon: '🌐' },
    { id: 'logs', label: '📝 Logs', icon: '📝' },
    { id: 'settings', label: '⚙️ Settings', icon: '⚙️' },
  ]

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-7xl mx-auto px-4">
        <h1 className="text-3xl font-bold mb-8">Admin Dashboard</h1>

        <div className="grid lg:grid-cols-6 gap-6">
          {/* Sidebar */}
          <div className="lg:col-span-1">
            <div className="card">
              <nav className="space-y-2">
                {tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`w-full text-left px-4 py-2 rounded transition-colors text-sm ${
                      activeTab === tab.id
                        ? 'bg-primary text-white'
                        : 'hover:bg-gray-100'
                    }`}
                  >
                    <span className="mr-2">{tab.icon}</span>
                    {tab.label}
                  </button>
                ))}
              </nav>
            </div>
          </div>

          {/* Content */}
          <div className="lg:col-span-5">
            {activeTab === 'dashboard' && (
              <div className="space-y-6">
                <div className="grid md:grid-cols-3 gap-6">
                  <div className="card">
                    <div className="text-3xl font-bold text-primary">1,234</div>
                    <p className="text-gray-600">Total Projects</p>
                  </div>
                  <div className="card">
                    <div className="text-3xl font-bold text-primary">567</div>
                    <p className="text-gray-600">Active Users</p>
                  </div>
                  <div className="card">
                    <div className="text-3xl font-bold text-primary">$89K</div>
                    <p className="text-gray-600">Monthly Revenue</p>
                  </div>
                </div>

                <div className="card">
                  <h2 className="font-bold text-lg mb-4">Recent Activity</h2>
                  <div className="space-y-3 text-sm">
                    <p className="text-gray-600">• User john@example.com created a project</p>
                    <p className="text-gray-600">• 150 outputs generated today</p>
                    <p className="text-gray-600">• System uptime: 99.9%</p>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'users' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">Users Management</h2>
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left p-2">Email</th>
                        <th className="text-left p-2">Plan</th>
                        <th className="text-left p-2">Projects</th>
                        <th className="text-left p-2">Joined</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr className="border-b hover:bg-gray-50">
                        <td className="p-2">user@example.com</td>
                        <td className="p-2"><span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Tier 1</span></td>
                        <td className="p-2">5</td>
                        <td className="p-2">2024-01-15</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {activeTab === 'plans' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">Pricing Plans</h2>
                <div className="space-y-4">
                  {['Tier 1', 'Tier 2', 'Tier 3', 'Tier 4'].map((tier) => (
                    <div key={tier} className="border rounded-lg p-4 hover:border-primary transition-colors">
                      <div className="flex justify-between items-center">
                        <span className="font-medium">{tier}</span>
                        <button className="btn-secondary text-sm">Edit</button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === 'templates' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">Content Templates</h2>
                <p className="text-gray-600 text-sm">Manage prompts and templates for content generation</p>
              </div>
            )}

            {activeTab === 'translations' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">Translations</h2>
                <p className="text-gray-600 text-sm">Manage multilingual labels and content</p>
              </div>
            )}

            {activeTab === 'logs' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">Activity Logs</h2>
                <p className="text-gray-600 text-sm">View system logs and user activities</p>
              </div>
            )}

            {activeTab === 'settings' && (
              <div className="card">
                <h2 className="font-bold text-lg mb-4">System Settings</h2>
                <div className="space-y-4">
                  <div>
                    <label className="label-text">API Key</label>
                    <input type="password" className="input-field" defaultValue="sk-..." disabled />
                  </div>
                  <div>
                    <label className="label-text">Max Projects per User</label>
                    <input type="number" className="input-field" defaultValue="100" />
                  </div>
                  <button className="btn-primary">Save Settings</button>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
