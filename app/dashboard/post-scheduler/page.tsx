'use client';

import { useState } from 'react';
import { FiCalendar } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function PostSchedulerPage() {
  const [niche, setNiche] = useState('');
  const [platforms, setPlatforms] = useState(['instagram']);
  const [frequency, setFrequency] = useState('daily');
  const [duration, setDuration] = useState('1-week');
  const [contentTypes, setContentTypes] = useState(['educational']);
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!niche) {
      alert('Please specify your niche');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'post-scheduler',
          prompt: `Create a content calendar for:
Niche: ${niche}
Platforms: ${platforms.join(', ')}
Posting Frequency: ${frequency}
Duration: ${duration}
Content Types: ${contentTypes.join(', ')}

Provide a detailed schedule with:
- Specific post ideas
- Best posting times
- Content format recommendations
- Engagement strategies`,
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

  const togglePlatform = (platform: string) => {
    setPlatforms(prev =>
      prev.includes(platform)
        ? prev.filter(p => p !== platform)
        : [...prev, platform]
    );
  };

  const toggleContentType = (type: string) => {
    setContentTypes(prev =>
      prev.includes(type)
        ? prev.filter(t => t !== type)
        : [...prev, type]
    );
  };

  return (
    <AIToolLayout
      title="Post Scheduler Assistant"
      description="Plan your content calendar strategically"
      icon={FiCalendar}
      color="from-teal-500 to-cyan-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">
            Niche/Industry <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            value={niche}
            onChange={(e) => setNiche(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="e.g., Fitness, Marketing, Real Estate"
          />
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Platforms</label>
          <div className="grid grid-cols-2 gap-2">
            {['instagram', 'facebook', 'twitter', 'linkedin', 'tiktok', 'youtube'].map(platform => (
              <label key={platform} className="flex items-center space-x-2 p-2 rounded hover:bg-white/5">
                <input
                  type="checkbox"
                  checked={platforms.includes(platform)}
                  onChange={() => togglePlatform(platform)}
                  className="w-4 h-4"
                />
                <span className="text-sm capitalize">{platform}</span>
              </label>
            ))}
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Posting Frequency</label>
          <select
            value={frequency}
            onChange={(e) => setFrequency(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="daily">Daily</option>
            <option value="3-times-week">3 Times/Week</option>
            <option value="weekly">Weekly</option>
            <option value="bi-weekly">Bi-Weekly</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Duration</label>
          <select
            value={duration}
            onChange={(e) => setDuration(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="1-week">1 Week</option>
            <option value="2-weeks">2 Weeks</option>
            <option value="1-month">1 Month</option>
            <option value="3-months">3 Months</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Content Types</label>
          <div className="grid grid-cols-2 gap-2">
            {['educational', 'promotional', 'inspirational', 'behind-the-scenes', 'user-generated'].map(type => (
              <label key={type} className="flex items-center space-x-2 p-2 rounded hover:bg-white/5">
                <input
                  type="checkbox"
                  checked={contentTypes.includes(type)}
                  onChange={() => toggleContentType(type)}
                  className="w-4 h-4"
                />
                <span className="text-sm capitalize">{type.replace('-', ' ')}</span>
              </label>
            ))}
          </div>
        </div>
      </div>
    </AIToolLayout>
  );
}
