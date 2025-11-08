'use client';

import { useState } from 'react';
import { useStore } from '@/lib/store';
import { SUBSCRIPTION_PLANS } from '@/lib/stripe';
import { FiCheck, FiCreditCard } from 'react-icons/fi';
import { motion } from 'framer-motion';

export default function SubscriptionPage() {
  const { subscription, credits } = useStore();
  const [loading, setLoading] = useState<string | null>(null);

  const handleSubscribe = async (planKey: string) => {
    setLoading(planKey);

    try {
      // In production, create Stripe checkout session
      const response = await fetch('/api/create-checkout-session', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ plan: planKey }),
      });

      const { url } = await response.json();
      window.location.href = url;
    } catch (error) {
      console.error('Error creating checkout session:', error);
      alert('Failed to start checkout. Please try again.');
    } finally {
      setLoading(null);
    }
  };

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-4xl font-bold mb-2">Subscription & Billing</h1>
        <p className="text-gray-400">Manage your plan and billing information</p>
      </div>

      {/* Current Plan */}
      <div className="glass rounded-xl p-6">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-2xl font-bold capitalize">{subscription} Plan</h2>
            <p className="text-gray-400">Your current subscription</p>
          </div>
          <div className="text-right">
            <p className="text-sm text-gray-400">Available Credits</p>
            <p className="text-3xl font-bold">{credits}</p>
          </div>
        </div>
      </div>

      {/* All Plans */}
      <div>
        <h2 className="text-2xl font-bold mb-6">Available Plans</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {Object.entries(SUBSCRIPTION_PLANS).map(([key, plan], index) => (
            <motion.div
              key={key}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className={`glass rounded-xl p-6 ${
                subscription === key ? 'ring-2 ring-purple-500' : ''
              } ${key === 'pro' ? 'scale-105' : ''}`}
            >
              {key === 'pro' && (
                <div className="bg-gradient-purple text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                  Most Popular
                </div>
              )}
              {subscription === key && (
                <div className="bg-green-500 text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                  Current Plan
                </div>
              )}
              <h3 className="text-2xl font-bold mb-2">{plan.name}</h3>
              <div className="mb-6">
                <span className="text-5xl font-bold">${plan.price}</span>
                <span className="text-gray-400">/month</span>
              </div>
              <ul className="space-y-3 mb-8">
                {plan.features.map((feature, i) => (
                  <li key={i} className="flex items-start">
                    <FiCheck className="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                    <span className="text-sm">{feature}</span>
                  </li>
                ))}
              </ul>
              <button
                onClick={() => handleSubscribe(key)}
                disabled={subscription === key || loading !== null}
                className={`w-full py-3 rounded-lg font-semibold transition flex items-center justify-center space-x-2 ${
                  subscription === key
                    ? 'bg-gray-600 cursor-not-allowed'
                    : key === 'pro'
                    ? 'bg-gradient-purple hover:opacity-90'
                    : 'border-2 border-purple-500 hover:bg-purple-500/10'
                }`}
              >
                {loading === key ? (
                  <>
                    <div className="spinner w-5 h-5 border-2"></div>
                    <span>Processing...</span>
                  </>
                ) : subscription === key ? (
                  'Current Plan'
                ) : (
                  <>
                    <FiCreditCard />
                    <span>Upgrade Now</span>
                  </>
                )}
              </button>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Billing Info */}
      <div className="glass rounded-xl p-6">
        <h2 className="text-xl font-bold mb-4">Billing Information</h2>
        <div className="space-y-4">
          <div className="flex items-center justify-between p-4 bg-white/5 rounded-lg">
            <div>
              <p className="font-semibold">Payment Method</p>
              <p className="text-sm text-gray-400">•••• •••• •••• 4242</p>
            </div>
            <button className="text-purple-400 hover:text-purple-300 text-sm font-semibold">
              Update
            </button>
          </div>
          <div className="flex items-center justify-between p-4 bg-white/5 rounded-lg">
            <div>
              <p className="font-semibold">Next Billing Date</p>
              <p className="text-sm text-gray-400">
                {new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toLocaleDateString()}
              </p>
            </div>
            <button className="text-purple-400 hover:text-purple-300 text-sm font-semibold">
              View History
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
