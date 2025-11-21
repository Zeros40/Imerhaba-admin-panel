import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useAppStore } from '../context/store'
import { getOutputs, exportProject, downloadFile } from '../services/api'
import { Output } from '../types'

export default function Results() {
  const { projectId } = useParams<{ projectId: string }>()
  const navigate = useNavigate()
  const [outputs, setOutputs] = useState<Output[]>([])
  const [loading, setLoading] = useState(true)
  const [exportingFormat, setExportingFormat] = useState<string | null>(null)
  const [selectedOutput, setSelectedOutput] = useState<Output | null>(null)

  useEffect(() => {
    const fetchOutputs = async () => {
      if (!projectId) return
      try {
        const data = await getOutputs(projectId)
        setOutputs(data)
        if (data.length > 0) {
          setSelectedOutput(data[0])
        }
      } catch (err) {
        console.error('Failed to fetch outputs:', err)
      } finally {
        setLoading(false)
      }
    }
    fetchOutputs()
  }, [projectId])

  const handleExport = async (format: 'pdf' | 'html' | 'docx') => {
    if (!projectId) return
    try {
      setExportingFormat(format)
      const blob = await exportProject(projectId, format)
      downloadFile(blob, `zodiac13-${projectId}.${format}`)
    } catch (err) {
      console.error('Export error:', err)
      alert('Failed to export. Please try again.')
    } finally {
      setExportingFormat(null)
    }
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="loader"></div>
      </div>
    )
  }

  return (
    <div className="min-h-screen py-12">
      <div className="max-w-6xl mx-auto px-4">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">Generated Content</h1>

          {/* Export buttons */}
          <div className="flex gap-2">
            <button
              onClick={() => handleExport('pdf')}
              disabled={exportingFormat !== null}
              className="btn-secondary text-sm"
            >
              {exportingFormat === 'pdf' ? 'Exporting...' : 'PDF'}
            </button>
            <button
              onClick={() => handleExport('html')}
              disabled={exportingFormat !== null}
              className="btn-secondary text-sm"
            >
              {exportingFormat === 'html' ? 'Exporting...' : 'HTML'}
            </button>
            <button
              onClick={() => handleExport('docx')}
              disabled={exportingFormat !== null}
              className="btn-secondary text-sm"
            >
              {exportingFormat === 'docx' ? 'Exporting...' : 'DOCX'}
            </button>
          </div>
        </div>

        <div className="grid lg:grid-cols-4 gap-6">
          {/* List of outputs */}
          <div className="lg:col-span-1">
            <div className="card">
              <h2 className="font-bold mb-4">Outputs ({outputs.length})</h2>
              <div className="space-y-2 max-h-96 overflow-y-auto">
                {outputs.map((output) => (
                  <button
                    key={output.id}
                    onClick={() => setSelectedOutput(output)}
                    className={`w-full text-left p-2 rounded transition-colors text-sm ${
                      selectedOutput?.id === output.id
                        ? 'bg-primary text-white'
                        : 'bg-gray-100 hover:bg-gray-200'
                    }`}
                  >
                    {output.title}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Content viewer */}
          <div className="lg:col-span-3">
            {selectedOutput ? (
              <div className="card">
                <h2 className="font-bold text-xl mb-2">{selectedOutput.title}</h2>
                <p className="text-sm text-gray-500 mb-4">
                  Generated on {new Date(selectedOutput.createdAt).toLocaleDateString()}
                </p>

                <div className="border-t pt-4">
                  <div className="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700">
                    {selectedOutput.content}
                  </div>
                </div>

                <div className="mt-6 flex gap-2">
                  <button
                    onClick={() => {
                      navigator.clipboard.writeText(selectedOutput.content)
                      alert('Copied to clipboard!')
                    }}
                    className="btn-secondary"
                  >
                    Copy
                  </button>
                  <button
                    onClick={() => {
                      const element = document.createElement('a')
                      const file = new Blob([selectedOutput.content], { type: 'text/plain' })
                      element.href = URL.createObjectURL(file)
                      element.download = `${selectedOutput.title}.txt`
                      document.body.appendChild(element)
                      element.click()
                      document.body.removeChild(element)
                    }}
                    className="btn-secondary"
                  >
                    Download
                  </button>
                </div>
              </div>
            ) : (
              <div className="card text-center py-12">
                <p className="text-gray-500">Select an output to view its content</p>
              </div>
            )}
          </div>
        </div>

        {/* Actions */}
        <div className="flex gap-4 mt-8">
          <button
            onClick={() => navigate(`/projects/${projectId}/outputs`)}
            className="btn-secondary"
          >
            ← Back to Selection
          </button>
          <button
            onClick={() => navigate('/scan')}
            className="btn-primary"
          >
            Analyze Another Site →
          </button>
        </div>
      </div>
    </div>
  )
}
