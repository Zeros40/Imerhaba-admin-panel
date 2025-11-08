'use client';

import { useState } from 'react';
import { FiVideo } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function YouTubeKitPage() {
  const [niche, setNiche] = useState('');
  const [topic, setTopic] = useState('');
  const [duration, setDuration] = useState('5-10 minutes');
  const [style, setStyle] = useState('educational');
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!niche || !topic) {
      alert('Please fill in all required fields');
      return;
    }

    setLoading(true);

    try {
      // Call API to generate YouTube video script
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'youtube-kit',
          prompt: `Create a detailed faceless YouTube video script for:
Niche: ${niche}
Topic: ${topic}
Duration: ${duration}
Style: ${style}

Include:
1. Attention-grabbing hook (first 15 seconds)
2. Introduction
3. Main content points (3-5 key sections)
4. B-roll suggestions for each section
5. Call-to-action
6. Outro

Make it engaging and optimized for retention.`,
        }),
      });

      const data = await response.json();
      setOutput(data.content);

      // Update credits
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
      title="Faceless YouTube Video Kit"
      description="Create viral YouTube scripts without showing your face"
      icon={FiVideo}
      color="from-red-500 to-pink-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">
            Niche <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={niche}
            onChange={(e) => setNiche(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Personal Finance, Tech Reviews"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Video Topic <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={topic}
            onChange={(e) => setTopic(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., 7 Passive Income Ideas"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Duration</label>
          <select
            value={duration}
            onChange={(e) => setDuration(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="30-60 seconds">30-60 seconds (Short)</option>
            <option value="3-5 minutes">3-5 minutes</option>
            <option value="5-10 minutes">5-10 minutes</option>
            <option value="10-15 minutes">10-15 minutes</option>
            <option value="15-20 minutes">15-20 minutes</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Style</label>
          <select
            value={style}
            onChange={(e) => setStyle(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="educational">Educational</option>
            <option value="entertaining">Entertaining</option>
            <option value="inspirational">Inspirational</option>
            <option value="story-driven">Story-Driven</option>
            <option value="list-based">List-Based</option>
          </select>
        </div>
      </div>
    </AIToolLayout>
  );
}
