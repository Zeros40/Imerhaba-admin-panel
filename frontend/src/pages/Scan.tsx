import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAppStore } from '../context/store'
import { createProject, scanWebsite } from '../services/api'

export default function Scan() {
  const [url, setUrl] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const navigate = useNavigate()
  const { setCurrentProject, setScanInProgress, setError: setStoreError } = useAppStore()

  const handleScan = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')

    if (!url.trim()) {
      setError('Please enter a website URL')
      return
    }

    try {
      setLoading(true)
      setScanInProgress(true)

      // Create project
      const { projectId } = await createProject(url)
      console.log('Project created:', projectId)

      // Scan website
      const profile = await scanWebsite(projectId)
      console.log('Website scanned:', profile)

      // Set in store
      setCurrentProject({ id: projectId, name: url, websiteUrl: url, createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() })

      // Navigate to review
      navigate(`/projects/${projectId}/review`)
    } catch (err) {
      const errorMsg = err instanceof Error ? err.message : 'Failed to scan website'
      setError(errorMsg)
      setStoreError(errorMsg)
    } finally {
      setLoading(false)
      setScanInProgress(false)
    }
  }

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-2xl mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4">Analyze Your Website</h1>
          <p className="text-gray-600 text-lg">
            Paste your website URL below and let our AI analyze it to generate professional marketing content
          </p>
        </div>

        <div className="card">
          <form onSubmit={handleScan} className="space-y-6">
            <div>
              <label htmlFor="url" className="label-text">
                Website URL
              </label>
              <input
                id="url"
                type="url"
                value={url}
                onChange={(e) => setUrl(e.target.value)}
                placeholder="https://example.com"
                className="input-field"
                disabled={loading}
              />
              <p className="text-sm text-gray-500 mt-2">
                Enter the full URL including http:// or https://
              </p>
            </div>

            {error && (
              <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {error}
              </div>
            )}

            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full text-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              {loading && <div className="loader" style={{ width: '20px', height: '20px' }} />}
              {loading ? 'Analyzing...' : 'Analyze Website'}
            </button>
          </form>
        </div>

        {/* Information box */}
        <div className="card mt-8 bg-blue-50 border border-blue-200">
          <h3 className="font-bold text-blue-900 mb-3">What happens next?</h3>
          <ul className="space-y-2 text-blue-800 text-sm">
            <li>✓ Our AI will scrape and analyze your website content</li>
            <li>✓ We'll extract business information, keywords, and structure</li>
            <li>✓ You'll review the extracted data on the next screen</li>
            <li>✓ Then select what content you want to generate</li>
          </ul>
        </div>
      </div>
    </div>
  )
}
