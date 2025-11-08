'use client';

import { useState } from 'react';
import { FiMessageSquare } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function ChatbotScriptPage() {
  const [businessType, setBusinessType] = useState('');
  const [purpose, setPurpose] = useState('customer-support');
  const [tone, setTone] = useState('friendly');
  const [scenarios, setScenarios] = useState('');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!businessType) {
      alert('Please specify your business type');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'chatbot-script',
          prompt: `Create a comprehensive chatbot script for:
Business Type: ${businessType}
Purpose: ${purpose}
Tone: ${tone}
${scenarios ? `Scenarios to handle: ${scenarios}` : ''}

Include:
- Welcome message
- Common questions and responses
- Error handling
- Escalation paths
- Closing messages`,
        }),
      });

      const data = await response.json();
      setOutput(data.content);

      if (user) {
        await updateDoc(doc(db, 'users', user.uid), {
          credits: increment(-1),
        });
        setCredits(credits - 1);
      }
    } catch (error) {
      console.error('Error generating content:', error);
      alert('Failed to generate content. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <AIToolLayout
      title="Chatbot Script Generator"
      description="Build intelligent chatbot conversations"
      icon={FiMessageSquare}
      color="from-green-500 to-emerald-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">
            Business Type <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={businessType}
            onChange={(e) => setBusinessType(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., E-commerce Store, SaaS Product"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Purpose</label>
          <select
            value={purpose}
            onChange={(e) => setPurpose(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="customer-support">Customer Support</option>
            <option value="sales">Sales & Lead Generation</option>
            <option value="faq">FAQ Assistant</option>
            <option value="booking">Appointment Booking</option>
            <option value="onboarding">User Onboarding</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Tone</label>
          <select
            value={tone}
            onChange={(e) => setTone(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="friendly">Friendly</option>
            <option value="professional">Professional</option>
            <option value="casual">Casual</option>
            <option value="helpful">Helpful</option>
            <option value="enthusiastic">Enthusiastic</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Common Scenarios (Optional)
          </label>
          <textarea
            value={scenarios}
            onChange={(e) => setScenarios(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[100px]"
            placeholder="List common customer questions or situations..."
          />
        </div>
      </div>
    </AIToolLayout>
  );
}
