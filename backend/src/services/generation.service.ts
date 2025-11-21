import Anthropic from '@anthropic-ai/sdk';
import { PrismaClient, Plan, OutputType } from '@prisma/client';
import { BusinessProfile } from './extraction.service';

const client = new Anthropic({
  apiKey: process.env.CLAUDE_API_KEY,
});

const prisma = new PrismaClient();

const TIER1_PROMPTS = {
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

  SOCIAL_POSTS: `Create 10 social media posts (100-150 words each) for:
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
};

export async function generateContent(
  outputType: OutputType,
  businessProfile: BusinessProfile,
  projectId: string,
  optionalDetails?: {
    targetAudience?: string;
    mainOffer?: string;
    price?: string;
    brandTone?: string;
    mainGoal?: string;
  },
  language: string = 'en'
): Promise<string> {
  try {
    const prompt =
      TIER1_PROMPTS[outputType as keyof typeof TIER1_PROMPTS] || `Generate a ${outputType}`;

    const profileSummary = `
Business Profile:
- Name: ${businessProfile.businessName || 'Unknown'}
- Industry: ${businessProfile.industry || 'Various'}
- Description: ${businessProfile.description || 'No description'}
- Main Product: ${businessProfile.mainProduct || 'Various products'}
- Services: ${businessProfile.services?.join(', ') || 'N/A'}
- Target Audience: ${optionalDetails?.targetAudience || businessProfile.targetAudience || 'General'}
- Brand Tone: ${optionalDetails?.brandTone || businessProfile.brandTone || 'Professional'}
- Key Benefits: ${businessProfile.keyBenefits?.join(', ') || 'N/A'}
- Pain Points: ${businessProfile.painPoints?.join(', ') || 'N/A'}
- Pricing: ${optionalDetails?.price || businessProfile.pricing || 'Custom'}
`;

    const message = await client.messages.create({
      model: 'claude-3-5-sonnet-20241022',
      max_tokens: 4096,
      messages: [
        {
          role: 'user',
          content: `${profileSummary}\n\n${prompt}\n\nLanguage: ${language}\n\nGenerate the content now:`,
        },
      ],
    });

    const content = message.content[0].type === 'text' ? message.content[0].text : '';

    // Save to database
    await prisma.output.create({
      data: {
        projectId,
        type: outputType,
        title: outputType.replace(/_/g, ' '),
        content,
        language,
        format: 'text',
        tier: 'TIER1' as Plan,
      },
    });

    return content;
  } catch (error) {
    console.error('Generation error:', error);
    throw error;
  }
}

export async function generateTier1Outputs(
  businessProfile: BusinessProfile,
  projectId: string,
  optionalDetails?: any
): Promise<Record<OutputType, string>> {
  const outputs: Record<OutputType, string> = {} as Record<OutputType, string>;

  const tier1Types: OutputType[] = [
    'LANDING_PAGE_SHORT',
    'HERO_SECTION',
    'PAIN_SOLUTION_BLOCK',
    'FAQ',
    'SOCIAL_POST',
    'PERSONA',
    'PRODUCT_DESCRIPTION',
    'BRAND_BASICS',
  ] as OutputType[];

  for (const type of tier1Types) {
    try {
      outputs[type] = await generateContent(type, businessProfile, projectId, optionalDetails);
      console.log(`✓ Generated ${type}`);
    } catch (error) {
      console.error(`✗ Failed to generate ${type}:`, error);
      outputs[type] = '';
    }
  }

  return outputs;
}

export async function getGeneratedOutputs(projectId: string): Promise<any[]> {
  return await prisma.output.findMany({
    where: { projectId },
    orderBy: { createdAt: 'desc' },
  });
}

export async function deleteOutput(outputId: string): Promise<void> {
  await prisma.output.delete({
    where: { id: outputId },
  });
}
