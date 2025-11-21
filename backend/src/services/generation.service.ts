import Anthropic from '@anthropic-ai/sdk';
import { PrismaClient, Plan, OutputType } from '@prisma/client';
import { BusinessProfile } from './extraction.service';
import { TIER_PROMPTS } from './prompts';

const client = new Anthropic({
  apiKey: process.env.CLAUDE_API_KEY,
});

const prisma = new PrismaClient();

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
      TIER_PROMPTS[outputType as keyof typeof TIER_PROMPTS] || `Generate a ${outputType}`;

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
