'use client';

import { useState } from 'react';
import { FiEdit3 } from 'react-icons/fi';
import AIToolLayout from '@/components/AIToolLayout';
import { useStore } from '@/lib/store';
import { doc, updateDoc, increment } from 'firebase/firestore';
import { db } from '@/lib/firebase';

export default function CaptionGeneratorPage() {
  const [platform, setPlatform] = useState('instagram');
  const [contentType, setContentType] = useState('product');
  const [description, setDescription] = useState('');
  const [tone, setTone] = useState('professional');
  const [includeHashtags, setIncludeHashtags] = useState(true);
  const [includeEmojis, setIncludeEmojis] = useState(true);
  const [output, setOutput] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, credits, setCredits } = useStore();

  const handleGenerate = async () => {
    if (!description) {
      alert('Please provide a description');
      return;
    }

    setLoading(true);

    try {
      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          tool: 'caption-generator',
          prompt: `Create an engaging ${platform} caption for:
Content Type: ${contentType}
Description: ${description}
Tone: ${tone}
Include Hashtags: ${includeHashtags ? 'Yes' : 'No'}
Include Emojis: ${includeEmojis ? 'Yes' : 'No'}

Make it compelling and optimized for ${platform}.`,
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
      title="Caption & Content Generator"
      description="Generate engaging captions for all social platforms"
      icon={FiEdit3}
      color="from-blue-500 to-cyan-500"
      output={output}
      loading={loading}
      onGenerate={handleGenerate}
    >
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-2">Platform</label>
          <select
            value={platform}
            onChange={(e) => setPlatform(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="instagram">Instagram</option>
            <option value="facebook">Facebook</option>
            <option value="twitter">Twitter/X</option>
            <option value="linkedin">LinkedIn</option>
            <option value="tiktok">TikTok</option>
            <option value="youtube">YouTube</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">Content Type</label>
          <select
            value={contentType}
            onChange={(e) => setContentType(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="product">Product/Service</option>
            <option value="educational">Educational</option>
            <option value="promotional">Promotional</option>
            <option value="inspirational">Inspirational</option>
            <option value="behind-the-scenes">Behind the Scenes</option>
            <option value="announcement">Announcement</option>
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium mb-2">
            Description <span className="text-red-500">*</span>
          </label>
          <textarea
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[100px]"
            placeholder="Describe what you're posting about..."
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
            <option value="casual">Casual</option>
            <option value="friendly">Friendly</option>
            <option value="humorous">Humorous</option>
            <option value="inspirational">Inspirational</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>

        <div className="flex items-center space-x-6">
          <label className="flex items-center space-x-2">
            <input
              type="checkbox"
              checked={includeHashtags}
              onChange={(e) => setIncludeHashtags(e.target.checked)}
              className="w-4 h-4"
            />
            <span className="text-sm">Include Hashtags</span>
          </label>

          <label className="flex items-center space-x-2">
            <input
              type="checkbox"
              checked={includeEmojis}
              onChange={(e) => setIncludeEmojis(e.target.checked)}
              className="w-4 h-4"
            />
            <span className="text-sm">Include Emojis</span>
          </label>
        </div>
      </div>
    </AIToolLayout>
  );
}
