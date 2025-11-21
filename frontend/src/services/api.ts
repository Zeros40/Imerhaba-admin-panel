import axios from 'axios'
import { Project, BusinessProfile, Output, OptionalDetails } from '../types'

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:5000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
})

// Projects
export async function createProject(websiteUrl: string, name?: string): Promise<{ projectId: string }> {
  const response = await api.post('/projects', { websiteUrl, name })
  return response.data
}

export async function getProject(projectId: string): Promise<Project> {
  const response = await api.get(`/projects/${projectId}`)
  return response.data
}

export async function listProjects(): Promise<Project[]> {
  const response = await api.get('/projects')
  return response.data
}

export async function deleteProject(projectId: string): Promise<void> {
  await api.delete(`/projects/${projectId}`)
}

// Extraction
export async function scanWebsite(projectId: string): Promise<BusinessProfile> {
  const response = await api.post(`/projects/${projectId}/scan`)
  return response.data.profile
}

export async function getProfile(projectId: string): Promise<BusinessProfile | null> {
  try {
    const response = await api.get(`/projects/${projectId}/profile`)
    return response.data
  } catch {
    return null
  }
}

// Generation
export async function generateOutputs(
  projectId: string,
  outputTypes: string[],
  optionalDetails?: OptionalDetails,
  language: string = 'en'
): Promise<Record<string, string>> {
  const response = await api.post(`/projects/${projectId}/generate`, {
    outputTypes,
    optionalDetails,
    language,
  })
  return response.data.results
}

export async function getOutputs(projectId: string): Promise<Output[]> {
  const response = await api.get(`/projects/${projectId}/outputs`)
  return response.data
}

// Export
export async function exportProject(projectId: string, format: 'pdf' | 'html' | 'docx' = 'pdf'): Promise<Blob> {
  const response = await api.get(`/projects/${projectId}/export?format=${format}`, {
    responseType: 'blob',
  })
  return response.data
}

export function downloadFile(blob: Blob, filename: string): void {
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.setAttribute('download', filename)
  document.body.appendChild(link)
  link.click()
  link.parentNode?.removeChild(link)
  window.URL.revokeObjectURL(url)
}
