'use client';

import { useState } from 'react';
import { useStore } from '@/lib/store';
import { FiCopy, FiDownload, FiRefreshCw } from 'react-icons/fi';

interface AIToolLayoutProps {
  title: string;
  description: string;
  icon: React.ElementType;
  color: string;
  children: React.ReactNode;
  output: string;
  loading: boolean;
  onGenerate: () => void;
  onCopy?: () => void;
  onDownload?: () => void;
}

export default function AIToolLayout({
  title,
  description,
  icon: Icon,
  color,
  children,
  output,
  loading,
  onGenerate,
  onCopy,
  onDownload,
}: AIToolLayoutProps) {
  const { credits } = useStore();

  const handleCopy = () => {
    navigator.clipboard.writeText(output);
    if (onCopy) onCopy();
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div className={`w-16 h-16 rounded-xl bg-gradient-to-r ${color} flex items-center justify-center`}>
            <Icon className="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 className="text-3xl font-bold">{title}</h1>
            <p className="text-gray-400">{description}</p>
          </div>
        </div>
        <div className="text-right">
          <p className="text-sm text-gray-400">Credits Available</p>
          <p className="text-2xl font-bold">{credits}</p>
        </div>
      </div>

      {/* Main Content */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Input Section */}
        <div className="glass rounded-xl p-6">
          <h2 className="text-xl font-bold mb-4">Input</h2>
          {children}
          <button
            onClick={onGenerate}
            disabled={loading || credits <= 0}
            className="w-full mt-4 py-3 bg-gradient-purple rounded-lg font-semibold hover:opacity-90 transition disabled:opacity-50 flex items-center justify-center space-x-2"
          >
            {loading ? (
              <>
                <div className="spinner w-5 h-5 border-2"></div>
                <span>Generating...</span>
              </>
            ) : (
              <>
                <FiRefreshCw />
                <span>Generate</span>
              </>
            )}
          </button>
        </div>

        {/* Output Section */}
        <div className="glass rounded-xl p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-xl font-bold">Output</h2>
            {output && (
              <div className="flex space-x-2">
                <button
                  onClick={handleCopy}
                  className="p-2 rounded-lg hover:bg-white/5 transition"
                  title="Copy to clipboard"
                >
                  <FiCopy className="w-5 h-5" />
                </button>
                {onDownload && (
                  <button
                    onClick={onDownload}
                    className="p-2 rounded-lg hover:bg-white/5 transition"
                    title="Download"
                  >
                    <FiDownload className="w-5 h-5" />
                  </button>
                )}
              </div>
            )}
          </div>
          <div className="min-h-[400px] bg-black/20 rounded-lg p-4">
            {loading ? (
              <div className="flex items-center justify-center h-full">
                <div className="spinner"></div>
              </div>
            ) : output ? (
              <div className="whitespace-pre-wrap text-gray-200">{output}</div>
            ) : (
              <div className="flex items-center justify-center h-full text-gray-500">
                Your generated content will appear here
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
