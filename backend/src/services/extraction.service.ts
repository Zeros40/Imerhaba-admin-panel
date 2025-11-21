import Anthropic from '@anthropic-ai/sdk';
import { PrismaClient } from '@prisma/client';

const client = new Anthropic({
  apiKey: process.env.CLAUDE_API_KEY,
});

const prisma = new PrismaClient();

export interface BusinessProfile {
  businessName?: string;
  industry?: string;
  description?: string;
  mainProduct?: string;
  secondaryProducts?: string[];
  services?: string[];
  pricing?: string;
  guarantees?: string[];
  keyBenefits?: string[];
  features?: string[];
  brandTone?: string;
  brandColors?: string[];
  ctaPatterns?: string[];
  targetAudience?: string;
  painPoints?: string[];
  testimonials?: string[];
  offers?: string[];
  serviceAreas?: string[];
  socialLinks?: string[];
  contactInfo?: string;
  competitors?: string[];
  seoKeywords?: string[];
  metaTitles?: string[];
  metaDescriptions?: string[];
  googleSearchIssues?: string[];
  layoutStructure?: string;
  mistakes?: string[];
  opportunities?: string[];
}

const EXTRACTION_PROMPT = `You are an expert business analyst. Extract the following information from the provided website content.

Return ONLY valid JSON with these fields (include only what you find, omit empty fields):
{
  "businessName": "string",
  "industry": "string",
  "description": "string",
  "mainProduct": "string",
  "secondaryProducts": ["string"],
  "services": ["string"],
  "pricing": "string",
  "guarantees": ["string"],
  "keyBenefits": ["string"],
  "features": ["string"],
  "brandTone": "string",
  "brandColors": ["string"],
  "ctaPatterns": ["string"],
  "targetAudience": "string (inferred if not explicit)",
  "painPoints": ["string (inferred)"],
  "testimonials": ["string"],
  "offers": ["string"],
  "serviceAreas": ["string"],
  "socialLinks": ["string"],
  "contactInfo": "string",
  "competitors": ["string (inferred)"],
  "seoKeywords": ["string"],
  "metaTitles": ["string"],
  "metaDescriptions": ["string"],
  "googleSearchIssues": ["string (inferred)"],
  "layoutStructure": "string (description)",
  "mistakes": ["string (identified)"],
  "opportunities": ["string (identified)"]
}

Extract intelligently. For missing information, use domain expertise to infer reasonable values.`;

export async function extractFromWebsite(
  websiteUrl: string,
  htmlContent: string,
  projectId: string
): Promise<BusinessProfile> {
  try {
    const message = await client.messages.create({
      model: 'claude-3-5-sonnet-20241022',
      max_tokens: 4096,
      messages: [
        {
          role: 'user',
          content: `${EXTRACTION_PROMPT}\n\nWebsite URL: ${websiteUrl}\n\nWebsite Content:\n${htmlContent}`,
        },
      ],
    });

    const responseText =
      message.content[0].type === 'text' ? message.content[0].text : '';

    // Parse JSON from response
    const jsonMatch = responseText.match(/\{[\s\S]*\}/);
    if (!jsonMatch) {
      throw new Error('Failed to parse JSON from extraction response');
    }

    const profile: BusinessProfile = JSON.parse(jsonMatch[0]);

    // Save to database
    await prisma.businessProfile.update(
      {
        where: { projectId },
        data: {
          ...profile,
          rawExtraction: profile,
        },
      },
      {
        // Handle if record doesn't exist
      }
    ).catch(async () => {
      // Create if doesn't exist
      await prisma.businessProfile.create({
        data: {
          projectId,
          ...profile,
          rawExtraction: profile,
        },
      });
    });

    return profile;
  } catch (error) {
    console.error('Extraction error:', error);
    throw error;
  }
}

export async function getExtractedProfile(projectId: string): Promise<BusinessProfile | null> {
  const profile = await prisma.businessProfile.findUnique({
    where: { projectId },
  });

  if (!profile) {
    return null;
  }

  return {
    businessName: profile.businessName || undefined,
    industry: profile.industry || undefined,
    description: profile.description || undefined,
    mainProduct: profile.mainProduct || undefined,
    secondaryProducts: profile.secondaryProducts as string[],
    services: profile.services as string[],
    pricing: profile.pricing || undefined,
    guarantees: profile.guarantees as string[],
    keyBenefits: profile.keyBenefits as string[],
    features: profile.features as string[],
    brandTone: profile.brandTone || undefined,
    brandColors: profile.brandColors as string[],
    ctaPatterns: profile.ctaPatterns as string[],
    targetAudience: profile.targetAudience || undefined,
    painPoints: profile.painPoints as string[],
    testimonials: profile.testimonials as string[],
    offers: profile.offers as string[],
    serviceAreas: profile.serviceAreas as string[],
    socialLinks: profile.socialLinks as string[],
    contactInfo: profile.contactInfo || undefined,
    competitors: profile.competitors as string[],
    seoKeywords: profile.seoKeywords as string[],
    metaTitles: profile.metaTitles as string[],
    metaDescriptions: profile.metaDescriptions as string[],
    googleSearchIssues: profile.googleSearchIssues as string[],
    layoutStructure: profile.layoutStructure || undefined,
    mistakes: profile.mistakes as string[],
    opportunities: profile.opportunities as string[],
  };
}
