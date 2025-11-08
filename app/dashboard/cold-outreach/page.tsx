'use client';

import { useState } from 'react';
import { FiMail } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function ColdOutreachPage() {
  const [recipientName, setRecipientName] = useState('');
  const [company, setCompany] = useState('');
  const [purpose, setPurpose] = useState('sales');
  const [productService, setProductService] = useState('');
  const [cta, setCta] = useState('');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!productService) {
      alert('Please describe your product/service');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'cold-outreach',
          prompt: `Write a compelling cold outreach email:
${recipientName ? `Recipient: ${recipientName}` : ''}
${company ? `Company: ${company}` : ''}
Purpose: ${purpose}
Product/Service: ${productService}
${cta ? `Call-to-Action: ${cta}` : ''}

Make it personalized, concise, and persuasive. Include subject line.`,
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
      title="Cold Outreach Writer"
      description="Craft persuasive emails that convert"
      icon={FiMail}
      color="from-purple-500 to-indigo-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">Recipient Name (Optional)</label>
          <input
            type="text"
            value={recipientName}
            onChange={(e) => setRecipientName(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., John Smith"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Company (Optional)</label>
          <input
            type="text"
            value={company}
            onChange={(e) => setCompany(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Acme Corp"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Purpose</label>
          <select
            value={purpose}
            onChange={(e) => setPurpose(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="sales">Sales</option>
            <option value="partnership">Partnership</option>
            <option value="collaboration">Collaboration</option>
            <option value="job-application">Job Application</option>
            <option value="networking">Networking</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Product/Service Description <span className="text-red-500">*</span>
          </label>
          <textarea
            value={productService}
            onChange={(e) => setProductService(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[100px]"
            placeholder="Describe what you're offering..."
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Call-to-Action (Optional)</label>
          <input
            type="text"
            value={cta}
            onChange={(e) => setCta(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Schedule a 15-minute call"
          />
        </div>
      </div>
    </AIToolLayout>
  );
}
