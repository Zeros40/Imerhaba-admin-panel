import { loadStripe, Stripe } from '@stripe/stripe-js';

let stripePromise: Promise<Stripe | null>;

export const getStripe = () => {
  if (!stripePromise) {
    stripePromise = loadStripe(process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!);
  }
  return stripePromise;
};

export const SUBSCRIPTION_PLANS = {
  free: {
    name: 'Free',
    price: 0,
    credits: 10,
    features: [
      '10 AI generations per month',
      'Access to 2 tools',
      'Basic support',
      'Community access',
    ],
  },
  starter: {
    name: 'Starter',
    price: 29,
    priceId: 'price_starter',
    credits: 100,
    features: [
      '100 AI generations per month',
      'Access to all 8 tools',
      'Priority support',
      'Advanced analytics',
      'Export capabilities',
    ],
  },
  pro: {
    name: 'Pro',
    price: 79,
    priceId: 'price_pro',
    credits: 500,
    features: [
      '500 AI generations per month',
      'Access to all 8 tools',
      'Priority support',
      'Advanced analytics',
      'Unlimited exports',
      'API access',
      'White-label options',
    ],
  },
  enterprise: {
    name: 'Enterprise',
    price: 299,
    priceId: 'price_enterprise',
    credits: -1, // unlimited
    features: [
      'Unlimited AI generations',
      'Access to all 8 tools',
      'Dedicated support',
      'Custom integrations',
      'Team collaboration',
      'Priority API access',
      'Custom training',
      'SLA guarantee',
    ],
  },
};
