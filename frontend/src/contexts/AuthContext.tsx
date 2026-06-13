import { createContext, useContext } from 'react';
import type { User } from '../types/types';

export interface AuthObject {
  user: User | null;
  login: (username: string, password: string) => Promise<void>;
  logout: () => void;
}

export const AuthContext = createContext<AuthObject | null>(null);

export function useAuth(): AuthObject {
  const value = useContext(AuthContext);
  if (value == null) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return value;
}
