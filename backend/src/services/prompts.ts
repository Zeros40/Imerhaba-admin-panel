// Complete prompt templates for all tiers
export const TIER_PROMPTS = {
  // TIER 1
  LANDING_PAGE_SHORT: `Create a compelling short landing page (500-800 words) for the business with:
- Attention-grabbing hero section
- Key benefits (3-5 bullet points)
- Clear CTA
- Brief social proof

Use the business tone and target audience insights provided.`,

  LANDING_PAGE_LONG: `Create a comprehensive landing page (1500-2000 words) including:
- Hero section with compelling headline
- Problem/pain points section
- Solution presentation
- Features and benefits (detailed)
- Testimonials/social proof
- Pricing comparison
- Multiple CTAs
- FAQ section
- Footer with links

Match the brand tone and target audience.`,

  HERO_SECTION: `Create a powerful hero section (200-300 words) with:
- Headline (under 10 words)
- Subheadline (under 20 words)
- CTA button text
- Key visual description

Target the specified audience and address their pain points.`,

  FAQ: `Create an FAQ section (10-15 questions) covering:
- Product/service features
- Pricing and payment
- Shipping/delivery (if applicable)
- Technical support
- Guarantees and refunds

Use clear, customer-friendly language.`,

  PAIN_SOLUTION_BLOCK: `Create a problem/solution block with:
- 3-5 key pain points
- For each pain point: a solution statement
- Brief explanation of how the solution works

Make it compelling and relevant to the target audience.`,

  SOCIAL_POST: `Create 10 social media posts (100-150 words each) for:
- LinkedIn (2 posts)
- Twitter/X (2 posts)
- Instagram (3 posts)
- Facebook (3 posts)

Each post should highlight benefits, create engagement, and include relevant hashtags.`,

  PRODUCT_DESCRIPTION: `Create a detailed product description (300-500 words) including:
- What it is (1-2 sentences)
- Key features (5-7)
- Benefits to the customer
- Use cases
- Technical specs (if applicable)
- Warranty/guarantee info`,

  BRAND_BASICS: `Create brand guidelines document including:
- Brand name and tagline
- Brand story (100-150 words)
- Mission statement
- Core values (3-5)
- Brand voice description
- Visual identity notes`,

  PERSONA: `Create a detailed buyer persona including:
- Demographics (age, income, education, location)
- Psychographics (goals, challenges, values)
- Behavioral patterns
- How they consume information
- Key decision factors
- Preferred communication channels`,

  // TIER 2
  PROPOSAL: `Create a professional sales proposal (2000-2500 words) with:
- Executive summary
- Problem statement
- Proposed solution
- Timeline and deliverables
- ROI calculations
- Pricing breakdown
- Terms and conditions
- Call to action
- Appendices

Use business-professional tone. Make it persuasive and comprehensive.`,

  WHATSAPP_SCRIPT: `Create a WhatsApp conversation script (800-1200 words) with:
- 10-15 message exchanges
- Greeting and rapport building
- Problem identification questions
- Solution presentation
- Objection handling
- Closing techniques
- Follow-up strategy

Make it conversational and effective for sales.`,

  EMAIL_SEQUENCE: `Create a 5-email sequence (each 150-300 words) including:
1. Welcome email - introduce yourself
2. Value email - provide useful information
3. Problem email - highlight the pain point
4. Solution email - present your offering
5. Closing email - create urgency and CTA

Each email should:
- Have compelling subject line
- Include personalization
- Build trust progressively
- Include clear CTAs`,

  PRODUCT_SHEET: `Create a product/service datasheet (1000-1500 words) with:
- Overview and key features
- Technical specifications
- Benefits matrix
- Comparison with alternatives
- Use cases and applications
- Pricing tiers
- Implementation timeline
- Support and training
- Case study or testimonial`,

  SERVICES_PRESENTATION: `Create a comprehensive services presentation outline including:
- Services overview (5-7 services)
- For each service:
  - Description
  - Key deliverables
  - Timeline
  - Investment/pricing
  - Expected outcomes
- Process workflow
- Team expertise highlights
- Portfolio highlights
- Booking/contact information`,

  BUSINESS_PLAN: `Create a business plan (3000-4000 words) covering:
- Executive summary
- Company description
- Market analysis
- Organization and management
- Service/product description
- Marketing and sales strategy
- Financial projections (3-5 years)
- Funding request (if applicable)
- Risk analysis
- Appendices`,

  ROI_BREAKDOWN: `Create an ROI analysis (1000-1500 words) showing:
- Current costs/inefficiencies
- Proposed solution benefits
- Cost-benefit analysis
- Revenue impact
- Time to ROI calculation
- Long-term savings projections
- Qualitative benefits
- Implementation costs
- Break-even analysis
- Visual charts/tables (in text format)`,

  INVESTMENT_SUMMARY: `Create an investment summary (800-1200 words) with:
- Business overview
- Market opportunity
- Unique value proposition
- Business model
- Financial highlights
- Use of funds
- Growth projections
- Team credentials
- Key milestones
- Investment terms`,

  PITCH_DECK_OUTLINE: `Create a pitch deck outline (15-20 slides) with:
1. Title slide
2. Problem statement
3. Solution overview
4. Market opportunity
5-6. Product/Service features (2 slides)
7. Business model
8. Market traction
9. Financial projections
10. Team
11. Competitive advantage
12. Go-to-market strategy
13. Financial ask
14. Use of funds
15. Contact/next steps

Include speaker notes for each slide.`,

  COMPETITOR_ANALYSIS: `Create a detailed competitor analysis (1500-2000 words) including:
- Industry overview
- Direct competitors (3-5)
- For each competitor:
  - Company overview
  - Strengths
  - Weaknesses
  - Pricing strategy
  - Marketing approach
  - Customer base
- Market positioning analysis
- Competitive advantages (our business)
- Market gaps and opportunities
- Recommendations`,

  POSITIONING_MAP: `Create a competitive positioning map analysis (1000-1500 words) with:
- Two key differentiators identified
- Current market positioning
- Position of 3-5 competitors
- Target positioning strategy
- Visual map description (in text)
- Messaging recommendations
- Differentiation strategy
- Pricing positioning
- Implementation timeline`,

  CALENDAR_30DAY: `Create a 30-day marketing calendar (1000-1500 words) with:
- Daily/weekly action items
- Content posting schedule
- Social media calendar
- Email marketing schedule
- Product launches or promotions
- Events or webinars
- Customer engagement activities
- Performance metrics to track
- Week-by-week breakdown
- Success metrics`,

  // TIER 3
  FULL_PITCH_DECK: `Create a complete pitch deck (30-40 slide outline) with:
- Professional presentation structure
- 40 slides covering:
  1. Introduction
  2-3. Problem deep-dive
  4-5. Solution overview
  6-10. Product features and benefits
  11-12. Business model
  13-15. Market analysis
  16-18. Traction and milestones
  19-20. Financial highlights
  21-25. Marketing and growth strategy
  26-28. Team and expertise
  29-32. Competitive landscape
  33-35. Investment opportunity
  36-38. Roadmap and vision
  39. Social proof and testimonials
  40. Contact and next steps

Include detailed speaker notes, visual descriptions, and key talking points.`,

  INVESTOR_PROPOSAL: `Create a formal investor proposal (5000-7000 words) including:
- Executive summary
- Company vision and mission
- Market opportunity analysis
- Business model explanation
- Revenue streams
- Marketing and customer acquisition strategy
- Competitive landscape
- Team and organizational structure
- Financial projections (5-year)
- Risk analysis and mitigation
- Use of capital
- Timeline to profitability
- Exit strategy
- Key performance indicators
- Appendices (financial tables, market data, team bios)`,

  BRAND_STRATEGY: `Create a comprehensive brand strategy document (2000-3000 words) with:
- Brand purpose and values
- Target audience segmentation
- Brand positioning statement
- Unique value proposition
- Brand voice and tone guidelines
- Visual identity system
- Brand messaging hierarchy
- Brand personality
- Competitive differentiation
- Brand promise and values
- Customer journey mapping
- Brand experience strategy
- Implementation roadmap`,

  SIX_MONTH_PLAN: `Create a detailed 6-month growth plan (2500-3500 words) with:
- Goal setting and objectives
- Monthly milestones and KPIs
- For each month:
  - Key initiatives
  - Marketing activities
  - Product development
  - Sales targets
  - Resource allocation
  - Success metrics
- Team requirements
- Budget allocation
- Risk mitigation strategies
- Performance review schedule
- Contingency plans`,

  WEBSITE_REWRITE: `Create a complete website rewrite (5000+ words) including:
- Homepage (800-1000 words)
  - Hero section copy
  - Key benefits section
  - Features section
  - Testimonials section
  - CTA sections
- About page (400-600 words)
- Services/Products pages (1000-1500 words)
- FAQ page (800-1000 words)
- Blog post ideas (5 titles and outlines)
- Email signup copy
- Footer content

Make it SEO-optimized and conversion-focused.`,

  SEO_AUDIT: `Create a comprehensive SEO audit report (2000-2500 words) with:
- Current SEO status
- Keyword analysis
- On-page SEO review
- Technical SEO issues
- Backlink profile analysis
- Competitor SEO analysis
- Content optimization recommendations
- Site structure improvements
- Priority action items
- 90-day SEO roadmap
- Tools and resources
- Success metrics`,

  TECHNICAL_FLAGS: `Create a technical issues report (1000-1500 words) with:
- Page speed analysis
- Mobile responsiveness issues
- Broken links
- SSL/security concerns
- Outdated technology stack
- Code quality issues
- Performance bottlenecks
- Scalability concerns
- Security vulnerabilities
- Recommended fixes with priority levels`,

  MARKET_MAP: `Create a market mapping analysis (1500-2000 words) with:
- Market size and growth
- Market segmentation
- Customer distribution
- Geographic analysis
- Demographic breakdown
- Psychographic segments
- Market trends
- Emerging opportunities
- Market threats
- White space opportunities
- Visual descriptions of market maps`,

  PRICING_BENCHMARK: `Create a pricing analysis (1500-2000 words) with:
- Current pricing analysis
- Competitor pricing comparison
- Value-based pricing recommendations
- Pricing model options
- Tiered pricing structure
- Psychological pricing techniques
- Discount strategies
- Payment terms recommendations
- Price elasticity analysis
- Implementation timeline`,

  CONTRACT: `Create a professional service contract template (1500-2000 words) with:
- Service description
- Scope of work
- Deliverables
- Timeline and milestones
- Pricing and payment terms
- Confidentiality clause
- Intellectual property rights
- Limitation of liability
- Termination clause
- Dispute resolution
- Governing law
- Signature blocks`,

  INVOICE: `Create a professional invoice template with:
- Header with business information
- Invoice number and date
- Client information
- Service/product line items with descriptions
- Pricing breakdown
- Subtotal, taxes, and total
- Payment terms and due date
- Payment methods
- Terms and conditions
- Notes section`,

  SOP: `Create standard operating procedures (2500-3500 words) for key processes:
- Process overview
- Step-by-step procedures
- Roles and responsibilities
- Quality standards
- Error handling
- Documentation requirements
- Training requirements
- Measurement and metrics
- Continuous improvement
- Emergency procedures
- Create 5-7 SOPs for critical business processes`,

  // Enhanced features
  AUTOMATION_FLOW: `Create business automation workflow documentation (1500-2000 words) with:
- Process automation opportunities identified
- For each automation:
  - Current manual process
  - Automation solution
  - Tools required
  - Implementation steps
  - Time/cost savings
- Workflow diagrams (described in text)
- Integration points
- Error handling procedures
- Monitoring and reporting
- Success metrics`,

  CRM_WORKFLOW: `Create a CRM workflow design (1500-2000 words) with:
- Sales pipeline stages
- Lead scoring model
- Automated workflows
- Email sequences
- Task assignments
- Reporting dashboards
- Integration with tools
- User training guide
- Implementation timeline
- Expected ROI`,

  MULTI_LANGUAGE_PACK: `Create multilingual content strategy (2000-2500 words) with:
- Target markets and languages
- For each language/market:
  - Cultural adaptations
  - Key messaging
  - Landing page copy
  - Marketing materials
  - Technical considerations
- SEO for each language
- Localization checklist
- Tool recommendations
- Timeline for implementation`,

  ADS_PACK: `Create a comprehensive ads strategy (2000-2500 words) with:
- Google Ads campaigns
  - Search ads
  - Display ads
  - Shopping ads
  - Keywords and targeting
- Social media ads
  - Facebook/Instagram strategy
  - LinkedIn strategy
  - Platform-specific copy
- Ad copy variations
- Landing page recommendations
- Budget allocation
- Performance metrics`,

  SWOT: `Create a SWOT analysis (1000-1500 words) with:
- Strengths (5-7 items)
- Weaknesses (5-7 items)
- Opportunities (5-7 items)
- Threats (5-7 items)
- Strategic implications for each quadrant
- Action items based on analysis`,

  PESTEL: `Create a PESTEL analysis (1500-2000 words) covering:
- Political factors
- Economic factors
- Social/cultural factors
- Technological factors
- Environmental factors
- Legal factors
- Impact assessment for each
- Opportunity identification
- Risk mitigation strategies`,

  RISK_ANALYSIS: `Create a comprehensive risk analysis (1500-2000 words) with:
- Identified risks (business, market, operational, financial)
- Risk assessment matrix
- For each significant risk:
  - Description
  - Probability assessment
  - Impact assessment
  - Mitigation strategy
  - Contingency plan
- Risk monitoring procedures
- Review schedule`,
}

export type PromptKey = keyof typeof TIER_PROMPTS;
