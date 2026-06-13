import { useState, useCallback, useMemo, type ReactNode } from 'react';
import { AuthContext } from './AuthContext';
import type { User } from '../types/types';
import { login as apiLogin } from '../services/api';

export function AuthProvider({ children }: { children?: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);

  const login = useCallback(async (username: string, password: string) => {
    const user = await apiLogin(username, password);
    setUser(user);
  }, []);


  const logout = useCallback(() => {
    setUser(null);
  }, []);

  const contextValue = useMemo(
    () => ({ user, login, logout }),
    [user, login, logout]
  );

  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
}
