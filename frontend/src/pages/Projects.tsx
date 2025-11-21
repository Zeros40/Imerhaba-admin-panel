import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { listProjects, deleteProject } from '../services/api'
import { Project } from '../types'

export default function Projects() {
  const [projects, setProjects] = useState<Project[]>([])
  const [loading, setLoading] = useState(true)
  const navigate = useNavigate()

  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const data = await listProjects()
        setProjects(data)
      } catch (err) {
        console.error('Failed to fetch projects:', err)
      } finally {
        setLoading(false)
      }
    }
    fetchProjects()
  }, [])

  const handleDelete = async (projectId: string) => {
    if (!confirm('Are you sure you want to delete this project?')) return
    try {
      await deleteProject(projectId)
      setProjects((prev) => prev.filter((p) => p.id !== projectId))
    } catch (err) {
      console.error('Delete error:', err)
      alert('Failed to delete project')
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
          <h1 className="text-3xl font-bold">My Projects</h1>
          <button onClick={() => navigate('/scan')} className="btn-primary">
            New Project
          </button>
        </div>

        {projects.length === 0 ? (
          <div className="card text-center py-12">
            <p className="text-gray-500 mb-6">No projects yet. Create your first one!</p>
            <button onClick={() => navigate('/scan')} className="btn-primary">
              Start Analyzing
            </button>
          </div>
        ) : (
          <div className="grid md:grid-cols-2 gap-6">
            {projects.map((project) => (
              <div key={project.id} className="card">
                <h3 className="font-bold text-lg mb-2">{project.name}</h3>
                <p className="text-sm text-gray-600 mb-4 truncate">{project.websiteUrl}</p>

                <div className="text-sm text-gray-500 mb-4">
                  Created: {new Date(project.createdAt).toLocaleDateString()}
                </div>

                <div className="flex gap-2">
                  <button
                    onClick={() => navigate(`/projects/${project.id}/results`)}
                    className="btn-primary flex-1 text-sm"
                  >
                    View
                  </button>
                  <button
                    onClick={() => navigate(`/projects/${project.id}/review`)}
                    className="btn-secondary flex-1 text-sm"
                  >
                    Review
                  </button>
                  <button
                    onClick={() => handleDelete(project.id)}
                    className="btn-secondary text-sm px-3"
                  >
                    Delete
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
