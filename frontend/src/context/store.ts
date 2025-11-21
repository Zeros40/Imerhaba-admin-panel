import { create } from 'zustand'
import { Project, BusinessProfile, OptionalDetails, Language } from '../types'

interface AppState {
  // Projects
  projects: Project[];
  currentProject: Project | null;
  selectedLanguage: Language;

  // Business Data
  businessProfile: BusinessProfile | null;
  optionalDetails: OptionalDetails;

  // UI State
  isLoading: boolean;
  scanInProgress: boolean;
  generationInProgress: boolean;
  error: string | null;

  // Actions
  setCurrentProject: (project: Project | null) => void;
  setProjects: (projects: Project[]) => void;
  setBusinessProfile: (profile: BusinessProfile) => void;
  setOptionalDetails: (details: OptionalDetails) => void;
  setIsLoading: (loading: boolean) => void;
  setScanInProgress: (scanning: boolean) => void;
  setGenerationInProgress: (generating: boolean) => void;
  setError: (error: string | null) => void;
  setSelectedLanguage: (lang: Language) => void;
  clearCurrentProject: () => void;
}

export const useAppStore = create<AppState>((set) => ({
  // Initial state
  projects: [],
  currentProject: null,
  selectedLanguage: 'en' as Language,
  businessProfile: null,
  optionalDetails: {},
  isLoading: false,
  scanInProgress: false,
  generationInProgress: false,
  error: null,

  // Actions
  setCurrentProject: (project) => set({ currentProject: project }),
  setProjects: (projects) => set({ projects }),
  setBusinessProfile: (profile) => set({ businessProfile: profile }),
  setOptionalDetails: (details) => set({ optionalDetails: details }),
  setIsLoading: (loading) => set({ isLoading: loading }),
  setScanInProgress: (scanning) => set({ scanInProgress: scanning }),
  setGenerationInProgress: (generating) => set({ generationInProgress: generating }),
  setError: (error) => set({ error }),
  setSelectedLanguage: (lang) => set({ selectedLanguage: lang }),
  clearCurrentProject: () => set({
    currentProject: null,
    businessProfile: null,
    optionalDetails: {},
    error: null,
  }),
}))
