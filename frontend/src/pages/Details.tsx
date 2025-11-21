import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useAppStore } from '../context/store'

export default function Details() {
  const { projectId } = useParams<{ projectId: string }>()
  const navigate = useNavigate()
  const { optionalDetails, setOptionalDetails } = useAppStore()

  const [details, setDetails] = useState(optionalDetails)

  const handleChange = (field: string, value: string) => {
    setDetails((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setOptionalDetails(details)
    navigate(`/projects/${projectId}/outputs`)
  }

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-2xl mx-auto px-4">
        <h1 className="text-3xl font-bold mb-8">Add Optional Details</h1>

        <form onSubmit={handleSubmit} className="card space-y-6">
          <div>
            <label className="label-text">Target Audience (Optional)</label>
            <input
              type="text"
              value={details.targetAudience || ''}
              onChange={(e) => handleChange('targetAudience', e.target.value)}
              placeholder="e.g., Young professionals aged 25-35"
              className="input-field"
            />
          </div>

          <div>
            <label className="label-text">Main Offer (Optional)</label>
            <input
              type="text"
              value={details.mainOffer || ''}
              onChange={(e) => handleChange('mainOffer', e.target.value)}
              placeholder="e.g., 30% discount for first 100 customers"
              className="input-field"
            />
          </div>

          <div>
            <label className="label-text">Price Point (Optional)</label>
            <input
              type="text"
              value={details.price || ''}
              onChange={(e) => handleChange('price', e.target.value)}
              placeholder="e.g., $99/month"
              className="input-field"
            />
          </div>

          <div>
            <label className="label-text">Brand Tone (Optional)</label>
            <select
              value={details.brandTone || ''}
              onChange={(e) => handleChange('brandTone', e.target.value)}
              className="input-field"
            >
              <option value="">Select a tone...</option>
              <option value="professional">Professional</option>
              <option value="casual">Casual & Friendly</option>
              <option value="bold">Bold & Energetic</option>
              <option value="luxurious">Luxurious & Premium</option>
              <option value="technical">Technical & Data-Driven</option>
            </select>
          </div>

          <div>
            <label className="label-text">Main Goal (Optional)</label>
            <input
              type="text"
              value={details.mainGoal || ''}
              onChange={(e) => handleChange('mainGoal', e.target.value)}
              placeholder="e.g., Increase email signups by 50%"
              className="input-field"
            />
          </div>

          <p className="text-sm text-gray-500 bg-blue-50 p-4 rounded-lg">
            ℹ️ These fields are optional. Our AI will infer values from your website if left blank.
          </p>

          <div className="flex gap-4">
            <button type="submit" className="btn-primary flex-1">
              Continue to Outputs →
            </button>
            <button
              type="button"
              onClick={() => navigate(`/projects/${projectId}/review`)}
              className="btn-secondary flex-1"
            >
              ← Back
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
