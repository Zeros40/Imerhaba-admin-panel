'use client';

import { useState } from 'react';
import { FiUsers } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function CRMFollowUpPage() {
  const [clientName, setClientName] = useState('');
  const [lastContact, setLastContact] = useState('');
  const [purpose, setPurpose] = useState('check-in');
  const [context, setContext] = useState('');
  const [tone, setTone] = useState('professional');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!context) {
      alert('Please provide context for the follow-up');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'crm-followup',
          prompt: `Write a CRM follow-up message:
${clientName ? `Client: ${clientName}` : ''}
${lastContact ? `Last Contact: ${lastContact}` : ''}
Purpose: ${purpose}
Context: ${context}
Tone: ${tone}

Make it personalized, professional, and action-oriented.`,
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
      title="CRM Follow-Up Writer"
      description="Automate your client communications"
      icon={FiUsers}
      color="from-pink-500 to-rose-500"
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
            placeholder="e.g., John Smith"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Last Contact (Optional)</label>
          <input
            type="text"
            value={lastContact}
            onChange={(e) => setLastContact(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., 2 weeks ago, after initial meeting"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Follow-Up Purpose</label>
          <select
            value={purpose}
            onChange={(e) => setPurpose(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="check-in">General Check-In</option>
            <option value="proposal-follow-up">Proposal Follow-Up</option>
            <option value="meeting-request">Meeting Request</option>
            <option value="payment-reminder">Payment Reminder</option>
            <option value="project-update">Project Update</option>
            <option value="re-engagement">Re-Engagement</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Context/Notes <span className="text-red-500">*</span>
          </label>
          <textarea
            value={context}
            onChange={(e) => setContext(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[100px]"
            placeholder="Provide context about the client relationship and what you want to achieve..."
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Tone</label>
          <select
            value={tone}
            onChange={(e) => setTone(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="professional">Professional</option>
            <option value="friendly">Friendly</option>
            <option value="casual">Casual</option>
            <option value="formal">Formal</option>
          </select>
        </div>
      </div>
    </AIToolLayout>
  );
}
