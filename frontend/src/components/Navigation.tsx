import { Link } from 'react-router-dom'
import { useAppStore } from '../context/store'

export default function Navigation() {
  const { selectedLanguage, setSelectedLanguage } = useAppStore()

  return (
    <nav className="bg-white shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center gap-8">
            <Link to="/" className="flex items-center gap-2">
              <div className="w-8 h-8 bg-gradient-to-br from-secondary to-primary rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-sm">Z</span>
              </div>
              <span className="font-bold text-lg hidden sm:inline">ZODIAC 13</span>
            </Link>

            <div className="hidden md:flex gap-6">
              <Link to="/" className="text-gray-600 hover:text-gray-900 transition-colors">
                Home
              </Link>
              <Link to="/scan" className="text-gray-600 hover:text-gray-900 transition-colors">
                Scan
              </Link>
              <Link to="/projects" className="text-gray-600 hover:text-gray-900 transition-colors">
                Projects
              </Link>
              <Link to="/admin" className="text-gray-600 hover:text-gray-900 transition-colors">
                Admin
              </Link>
            </div>
          </div>

          <div className="flex items-center gap-4">
            <select
              value={selectedLanguage}
              onChange={(e) => setSelectedLanguage(e.target.value as any)}
              className="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white cursor-pointer hover:border-gray-400 transition-colors"
            >
              <option value="en">English</option>
              <option value="ar">العربية</option>
              <option value="bs">Bosanski</option>
            </select>
          </div>
        </div>
      </div>
    </nav>
  )
}
