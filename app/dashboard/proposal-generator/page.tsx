'use client';

import { useState } from 'react';
import { FiFileText } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function ProposalGeneratorPage() {
  const [clientName, setClientName] = useState('');
  const [projectType, setProjectType] = useState('');
  const [scope, setScope] = useState('');
  const [budget, setBudget] = useState('');
  const [timeline, setTimeline] = useState('');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!projectType || !scope) {
      alert('Please fill in all required fields');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'proposal-generator',
          prompt: `Create a professional project proposal:
${clientName ? `Client: ${clientName}` : ''}
Project Type: ${projectType}
Scope: ${scope}
${budget ? `Budget Range: ${budget}` : ''}
${timeline ? `Timeline: ${timeline}` : ''}

Include:
- Executive summary
- Project objectives
- Scope of work
- Deliverables
- Timeline & milestones
- Investment/Pricing
- Terms & conditions
- Next steps`,
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
      title="Proposal Generator"
      description="Create winning client proposals"
      icon={FiFileText}
      color="from-violet-500 to-purple-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">Client Name (Optional)</label>
          <input
            type="text"
            value={clientName}
            onChange={(e) => setClientName(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Acme Corporation"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Project Type <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={projectType}
            onChange={(e) => setProjectType(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Website Development, Social Media Management"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Project Scope <span className="text-red-500">*</span>
          </label>
          <textarea
            value={scope}
            onChange={(e) => setScope(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[100px]"
            placeholder="Describe the project requirements and deliverables..."
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Budget Range (Optional)</label>
          <input
            type="text"
            value={budget}
            onChange={(e) => setBudget(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., $5,000 - $10,000"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Timeline (Optional)</label>
          <input
            type="text"
            value={timeline}
            onChange={(e) => setTimeline(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., 4-6 weeks"
          />
        </div>
      </div>
    </AIToolLayout>
  );
}
