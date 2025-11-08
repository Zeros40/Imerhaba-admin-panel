import { create } from 'zustand';
import { User } from 'firebase/auth';

interface UserState {
  user: User | null;
  loading: boolean;
  theme: 'light' | 'dark';
  language: 'en' | 'ar' | 'bs';
  credits: number;
  subscription: string;
  setUser: (user: User | null) => void;
  setLoading: (loading: boolean) => void;
  toggleTheme: () => void;
  setLanguage: (language: 'en' | 'ar' | 'bs') => void;
  setCredits: (credits: number) => void;
  setSubscription: (subscription: string) => void;
}

export const useStore = create<UserState>((set) => ({
  user: null,
  loading: true,
  theme: 'dark',
  language: 'en',
  credits: 0,
  subscription: 'free',
  setUser: (user) => set({ user }),
  setLoading: (loading) => set({ loading }),
  toggleTheme: () => set((state) => ({
    theme: state.theme === 'light' ? 'dark' : 'light'
  })),
  setLanguage: (language) => set({ language }),
  setCredits: (credits) => set({ credits }),
  setSubscription: (subscription) => set({ subscription }),
}));
