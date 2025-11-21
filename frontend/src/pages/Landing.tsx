import { Link } from 'react-router-dom'

export default function Landing() {
  const tiers = [
    {
      name: 'TIER 1 - Core Website Engine',
      price: '$99',
      features: ['Landing Pages', 'Hero Sections', 'FAQ', 'Social Posts', 'Brand Basics'],
    },
    {
      name: 'TIER 2 - Business & Marketing Pack',
      price: '$299',
      features: ['Proposal', 'Email Sequences', 'Business Plan', 'Pitch Deck', 'Competitor Analysis'],
    },
    {
      name: 'TIER 3 - Enterprise Growth Suite',
      price: '$799',
      features: ['Full Pitch Deck', 'Brand Strategy', '6-Month Plan', 'SEO Audit', 'SOPs'],
    },
    {
      name: 'TIER 4 - Complete All-In-One',
      price: '$1,999',
      features: ['Everything in Tier 3', 'White-Label License', 'API Access', 'Multi-Language Support'],
    },
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="gradient-bg text-white py-20">
        <div className="max-w-5xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold mb-6">ZODIAC 13</h1>
          <p className="text-xl mb-8 opacity-90">
            Convert any website URL into landing pages, sales proposals, pitch decks, SEO audits, and more with AI
          </p>
          <Link to="/scan" className="btn-primary text-lg">
            Start Analyzing →
          </Link>
        </div>
      </section>

      {/* Features Overview */}
      <section className="py-16 bg-gray-50">
        <div className="max-w-6xl mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">What You Can Generate</h2>

          <div className="grid md:grid-cols-3 gap-8">
            <div className="card">
              <h3 className="font-bold text-lg mb-4">📄 Content</h3>
              <ul className="space-y-2 text-gray-700">
                <li>• Landing Pages (Short & Long)</li>
                <li>• Website Copy</li>
                <li>• Sales Proposals</li>
                <li>• Email Sequences</li>
              </ul>
            </div>

            <div className="card">
              <h3 className="font-bold text-lg mb-4">📊 Strategy</h3>
              <ul className="space-y-2 text-gray-700">
                <li>• Business Plans</li>
                <li>• Pitch Decks</li>
                <li>• Marketing Plans</li>
                <li>• Competitor Analysis</li>
              </ul>
            </div>

            <div className="card">
              <h3 className="font-bold text-lg mb-4">🔧 Technical</h3>
              <ul className="space-y-2 text-gray-700">
                <li>• SEO Audits</li>
                <li>• Meta Data</li>
                <li>• Funnel Analysis</li>
                <li>• Technical Specs</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Pricing Tiers */}
      <section className="py-16">
        <div className="max-w-6xl mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">Pricing Plans</h2>

          <div className="grid md:grid-cols-2 gap-8">
            {tiers.map((tier) => (
              <div key={tier.name} className="card border-2 border-gray-200 hover:border-primary transition-colors">
                <h3 className="text-xl font-bold mb-2">{tier.name}</h3>
                <p className="text-3xl font-bold text-primary mb-6">{tier.price}</p>

                <ul className="space-y-2 mb-6">
                  {tier.features.map((feature) => (
                    <li key={feature} className="text-gray-700 flex items-center gap-2">
                      <span className="text-primary">✓</span>
                      {feature}
                    </li>
                  ))}
                </ul>

                <button className="btn-primary w-full">Get Started</button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="gradient-bg text-white py-16">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold mb-6">Ready to Transform Your Website?</h2>
          <p className="text-lg mb-8 opacity-90">
            Paste your website URL and let our AI analyze and generate professional marketing content in seconds.
          </p>
          <Link to="/scan" className="btn-primary text-lg">
            Analyze Your Website Now
          </Link>
        </div>
      </section>
    </div>
  )
}
