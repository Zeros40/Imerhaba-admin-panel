'use client';

import { useEffect } from 'react';
import { onAuthStateChanged } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { auth, db } from '@/lib/firebase';
import { useStore } from '@/lib/store';

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const { setUser, setLoading, setCredits, setSubscription } = useStore();

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (user) => {
      setUser(user);

      if (user) {
        // Fetch user data from Firestore
        const userDoc = await getDoc(doc(db, 'users', user.uid));
        if (userDoc.exists()) {
          const userData = userDoc.data();
          setCredits(userData.credits || 0);
          setSubscription(userData.subscription || 'free');
        }
      }

      setLoading(false);
    });

    return () => unsubscribe();
  }, [setUser, setLoading, setCredits, setSubscription]);

  return <>{children}</>;
}
