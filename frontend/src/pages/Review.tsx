import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useAppStore } from '../context/store'
import { getProfile } from '../services/api'
import { BusinessProfile } from '../types'

export default function Review() {
  const { projectId } = useParams<{ projectId: string }>()
  const navigate = useNavigate()
  const [profile, setProfile] = useState<BusinessProfile | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchProfile = async () => {
      if (!projectId) return
      try {
        const data = await getProfile(projectId)
        setProfile(data)
      } catch (err) {
        console.error('Failed to fetch profile:', err)
      } finally {
        setLoading(false)
      }
    }
    fetchProfile()
  }, [projectId])

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="loader"></div>
      </div>
    )
  }

  if (!profile) {
    return (
      <div className="min-h-screen py-12">
        <div className="max-w-4xl mx-auto px-4">
          <div className="card bg-red-50 border border-red-200">
            <p className="text-red-700">No profile found. Please scan a website first.</p>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-4xl mx-auto px-4">
        <h1 className="text-3xl font-bold mb-8">Review Extracted Information</h1>

        <div className="grid md:grid-cols-2 gap-6">
          {/* Business Info */}
          <div className="card">
            <h2 className="font-bold text-lg mb-4">Business Information</h2>
            <div className="space-y-3 text-sm">
              <p><span className="font-semibold">Name:</span> {profile.businessName || 'N/A'}</p>
              <p><span className="font-semibold">Industry:</span> {profile.industry || 'N/A'}</p>
              <p><span className="font-semibold">Main Product:</span> {profile.mainProduct || 'N/A'}</p>
              <p><span className="font-semibold">Pricing:</span> {profile.pricing || 'N/A'}</p>
            </div>
          </div>

          {/* Brand */}
          <div className="card">
            <h2 className="font-bold text-lg mb-4">Brand</h2>
            <div className="space-y-3 text-sm">
              <p><span className="font-semibold">Tone:</span> {profile.brandTone || 'N/A'}</p>
              <p><span className="font-semibold">Colors:</span> {profile.brandColors?.join(', ') || 'N/A'}</p>
              <p><span className="font-semibold">Target Audience:</span> {profile.targetAudience || 'N/A'}</p>
            </div>
          </div>

          {/* Benefits */}
          <div className="card">
            <h2 className="font-bold text-lg mb-4">Key Benefits</h2>
            <ul className="space-y-2 text-sm">
              {profile.keyBenefits?.map((b, i) => (
                <li key={i} className="text-gray-700">• {b}</li>
              )) || <p className="text-gray-500">N/A</p>}
            </ul>
          </div>

          {/* Pain Points */}
          <div className="card">
            <h2 className="font-bold text-lg mb-4">Pain Points (Inferred)</h2>
            <ul className="space-y-2 text-sm">
              {profile.painPoints?.map((p, i) => (
                <li key={i} className="text-gray-700">• {p}</li>
              )) || <p className="text-gray-500">N/A</p>}
            </ul>
          </div>

          {/* Opportunities */}
          <div className="card col-span-full">
            <h2 className="font-bold text-lg mb-4">Opportunities Identified</h2>
            <ul className="space-y-2">
              {profile.opportunities?.map((o, i) => (
                <li key={i} className="text-gray-700 text-sm">• {o}</li>
              )) || <p className="text-gray-500 text-sm">N/A</p>}
            </ul>
          </div>
        </div>

        {/* Actions */}
        <div className="flex gap-4 mt-8">
          <button
            onClick={() => navigate(`/projects/${projectId}/details`)}
            className="btn-primary flex-1"
          >
            Proceed to Details →
          </button>
          <button
            onClick={() => navigate('/scan')}
            className="btn-secondary flex-1"
          >
            Scan Another Site
          </button>
        </div>
      </div>
    </div>
  )
}
