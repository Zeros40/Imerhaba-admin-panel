export type Language = 'en' | 'ar' | 'bs';

export type Plan = 'TIER1' | 'TIER2' | 'TIER3' | 'TIER4';

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

export interface Project {
  id: string;
  name: string;
  websiteUrl: string;
  businessProfile?: BusinessProfile;
  createdAt: string;
  updatedAt: string;
}

export interface Output {
  id: string;
  projectId: string;
  type: string;
  title: string;
  content: string;
  language: string;
  format: string;
  tier: Plan;
  createdAt: string;
  updatedAt: string;
}

export interface OptionalDetails {
  targetAudience?: string;
  mainOffer?: string;
  price?: string;
  brandTone?: string;
  mainGoal?: string;
}
