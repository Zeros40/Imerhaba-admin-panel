'use client';

import { useState } from 'react';
import { FiBook } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function LeadMagnetPage() {
  const [topic, setTopic] = useState('');
  const [type, setType] = useState('ebook');
  const [targetAudience, setTargetAudience] = useState('');
  const [length, setLength] = useState('5-10 pages');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!topic || !targetAudience) {
      alert('Please fill in all required fields');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'lead-magnet',
          prompt: `Create a ${type} outline for:
Topic: ${topic}
Target Audience: ${targetAudience}
Length: ${length}

Include:
- Compelling title
- Table of contents
- Key sections with main points
- Actionable takeaways
- Call-to-action`,
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
      title="Lead Magnet / eBook Builder"
      description="Create valuable lead magnets in minutes"
      icon={FiBook}
      color="from-orange-500 to-amber-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">Type</label>
          <select
            value={type}
            onChange={(e) => setType(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="ebook">eBook</option>
            <option value="checklist">Checklist</option>
            <option value="guide">Guide</option>
            <option value="workbook">Workbook</option>
            <option value="template">Template</option>
            <option value="cheat-sheet">Cheat Sheet</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Topic <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={topic}
            onChange={(e) => setTopic(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Social Media Marketing for Beginners"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Target Audience <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={targetAudience}
            onChange={(e) => setTargetAudience(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Small business owners"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Length</label>
          <select
            value={length}
            onChange={(e) => setLength(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="1-2 pages">1-2 pages (Quick Guide)</option>
            <option value="5-10 pages">5-10 pages (Short eBook)</option>
            <option value="10-20 pages">10-20 pages (Standard eBook)</option>
            <option value="20-30 pages">20-30 pages (Comprehensive Guide)</option>
          </select>
        </div>
      </div>
    </AIToolLayout>
  );
}
