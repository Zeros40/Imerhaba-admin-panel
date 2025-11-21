import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useAppStore } from '../context/store'
import { generateOutputs } from '../services/api'

const TIER1_OUTPUTS = [
  { id: 'LANDING_PAGE_SHORT', label: 'Landing Page (Short)', icon: '📄' },
  { id: 'LANDING_PAGE_LONG', label: 'Landing Page (Long)', icon: '📄' },
  { id: 'HERO_SECTION', label: 'Hero Section', icon: '⭐' },
  { id: 'PAIN_SOLUTION_BLOCK', label: 'Pain/Solution Block', icon: '🎯' },
  { id: 'FAQ', label: 'FAQ Section', icon: '❓' },
  { id: 'SOCIAL_POST', label: 'Social Media Posts', icon: '📱' },
  { id: 'PERSONA', label: 'Buyer Persona', icon: '👤' },
  { id: 'PRODUCT_DESCRIPTION', label: 'Product Description', icon: '🏷️' },
]

const TIER2_OUTPUTS = [
  { id: 'PROPOSAL', label: 'Sales Proposal', icon: '📋' },
  { id: 'WHATSAPP_SCRIPT', label: 'WhatsApp Script', icon: '💬' },
  { id: 'EMAIL_SEQUENCE', label: 'Email Sequence', icon: '✉️' },
  { id: 'BUSINESS_PLAN', label: 'Business Plan', icon: '📊' },
  { id: 'COMPETITOR_ANALYSIS', label: 'Competitor Analysis', icon: '🔍' },
  { id: 'PITCH_DECK_OUTLINE', label: 'Pitch Deck Outline', icon: '🎤' },
  { id: 'PRODUCT_SHEET', label: 'Product Sheet', icon: '📑' },
  { id: 'SERVICES_PRESENTATION', label: 'Services Presentation', icon: '🎨' },
  { id: 'ROI_BREAKDOWN', label: 'ROI Breakdown', icon: '💹' },
  { id: 'INVESTMENT_SUMMARY', label: 'Investment Summary', icon: '💼' },
  { id: 'POSITIONING_MAP', label: 'Positioning Map', icon: '🗺️' },
  { id: 'CALENDAR_30DAY', label: '30-Day Marketing Calendar', icon: '📅' },
]

const TIER3_OUTPUTS = [
  { id: 'FULL_PITCH_DECK', label: 'Full Pitch Deck', icon: '🎯' },
  { id: 'INVESTOR_PROPOSAL', label: 'Investor Proposal', icon: '🏢' },
  { id: 'BRAND_STRATEGY', label: 'Brand Strategy', icon: '🎭' },
  { id: 'SIX_MONTH_PLAN', label: '6-Month Growth Plan', icon: '📈' },
  { id: 'WEBSITE_REWRITE', label: 'Complete Website Rewrite', icon: '💻' },
  { id: 'SEO_AUDIT', label: 'SEO Audit Report', icon: '🔎' },
  { id: 'TECHNICAL_FLAGS', label: 'Technical Issues Report', icon: '⚠️' },
  { id: 'MARKET_MAP', label: 'Market Mapping', icon: '🌐' },
  { id: 'PRICING_BENCHMARK', label: 'Pricing Analysis', icon: '💰' },
  { id: 'CONTRACT', label: 'Service Contract Template', icon: '📜' },
  { id: 'INVOICE', label: 'Invoice Template', icon: '🧾' },
  { id: 'SOP', label: 'Standard Operating Procedures', icon: '📋' },
  { id: 'AUTOMATION_FLOW', label: 'Automation Workflow', icon: '⚙️' },
  { id: 'CRM_WORKFLOW', label: 'CRM Workflow Design', icon: '🤝' },
  { id: 'MULTI_LANGUAGE_PACK', label: 'Multilingual Strategy', icon: '🌍' },
  { id: 'ADS_PACK', label: 'Ads Strategy Pack', icon: '📢' },
  { id: 'SWOT', label: 'SWOT Analysis', icon: '📌' },
  { id: 'PESTEL', label: 'PESTEL Analysis', icon: '🔍' },
  { id: 'RISK_ANALYSIS', label: 'Risk Analysis', icon: '⚡' },
]

export default function Outputs() {
  const { projectId } = useParams<{ projectId: string }>()
  const navigate = useNavigate()
  const { selectedLanguage, setGenerationInProgress, optionalDetails } = useAppStore()

  const [selectedOutputs, setSelectedOutputs] = useState<string[]>(TIER1_OUTPUTS.map((o) => o.id))
  const [loading, setLoading] = useState(false)

  const toggleOutput = (id: string) => {
    setSelectedOutputs((prev) =>
      prev.includes(id) ? prev.filter((o) => o !== id) : [...prev, id]
    )
  }

  const handleGenerate = async () => {
    if (!projectId || selectedOutputs.length === 0) return

    try {
      setLoading(true)
      setGenerationInProgress(true)

      await generateOutputs(projectId, selectedOutputs, optionalDetails, selectedLanguage)

      navigate(`/projects/${projectId}/results`)
    } catch (err) {
      console.error('Generation error:', err)
      alert('Failed to generate outputs. Please try again.')
    } finally {
      setLoading(false)
      setGenerationInProgress(false)
    }
  }

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-5xl mx-auto px-4">
        <h1 className="text-3xl font-bold mb-4">Select Outputs to Generate</h1>
        <p className="text-gray-600 mb-8">
          Choose what content you want us to generate (Tier 1 is included)
        </p>

        {/* Tier 1 */}
        <div className="mb-12">
          <h2 className="text-2xl font-bold mb-6 text-primary">Tier 1 - Core Outputs</h2>
          <div className="grid md:grid-cols-3 gap-4 mb-8">
            {TIER1_OUTPUTS.map((output) => (
              <label
                key={output.id}
                className="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary transition-colors"
              >
                <input
                  type="checkbox"
                  checked={selectedOutputs.includes(output.id)}
                  onChange={() => toggleOutput(output.id)}
                  className="w-5 h-5 text-primary rounded cursor-pointer"
                />
                <div className="ml-3">
                  <span className="text-lg mr-2">{output.icon}</span>
                  <span className="font-medium text-gray-900">{output.label}</span>
                </div>
              </label>
            ))}
          </div>
        </div>

        {/* Tier 2 */}
        <div className="mb-12 opacity-60">
          <h2 className="text-2xl font-bold mb-6 text-gray-500">Tier 2 - Business & Marketing (Coming Soon)</h2>
          <p className="text-gray-500 mb-6">Upgrade your plan to unlock these additional 12 outputs</p>
          <div className="grid md:grid-cols-3 gap-4">
            {TIER2_OUTPUTS.map((output) => (
              <div
                key={output.id}
                className="flex items-center p-4 border-2 border-gray-200 rounded-lg bg-gray-50"
              >
                <span className="text-lg mr-2">{output.icon}</span>
                <span className="font-medium text-gray-500 text-sm">{output.label}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Tier 3 */}
        <div className="mb-12 opacity-50">
          <h2 className="text-2xl font-bold mb-6 text-gray-400">Tier 3 - Enterprise Suite (Coming Soon)</h2>
          <p className="text-gray-400 mb-6">Premium plan with 19 additional enterprise-grade outputs</p>
          <div className="grid md:grid-cols-3 gap-4">
            {TIER3_OUTPUTS.map((output) => (
              <div
                key={output.id}
                className="flex items-center p-4 border-2 border-gray-200 rounded-lg bg-gray-50"
              >
                <span className="text-lg mr-2">{output.icon}</span>
                <span className="font-medium text-gray-400 text-sm">{output.label}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Summary */}
        <div className="card bg-blue-50 border border-blue-200">
          <p className="text-blue-900">
            <span className="font-bold">Selected: {selectedOutputs.length} outputs</span>
            <br />
            Language: <span className="font-bold uppercase">{selectedLanguage}</span>
          </p>
        </div>

        {/* Actions */}
        <div className="flex gap-4 mt-8">
          <button
            onClick={handleGenerate}
            disabled={loading || selectedOutputs.length === 0}
            className="btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            {loading && <div className="loader" style={{ width: '20px', height: '20px' }} />}
            {loading ? 'Generating...' : 'Generate Outputs'}
          </button>
          <button
            onClick={() => navigate(`/projects/${projectId}/details`)}
            className="btn-secondary flex-1"
          >
            ← Back
          </button>
        </div>
      </div>
    </div>
  )
}
