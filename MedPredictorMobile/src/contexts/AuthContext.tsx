import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import WebAppAuthService from '../services/WebAppAuthService';
import { User, Tenant, LoginCredentials, AuthContextType } from '../types';

const AuthContext = createContext<AuthContextType | undefined>(undefined);

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [tenant, setTenant] = useState<Tenant | null>(null);
  const [loading, setLoading] = useState(true);
  const [authService] = useState(() => new WebAppAuthService());

  useEffect(() => {
    initializeAuth();
  }, []);

  const initializeAuth = async () => {
    try {
      await authService.initialize();
      
      const currentUser = authService.getCurrentUser();
      const currentTenant = authService.getCurrentTenant();
      
      if (currentUser && authService.isAuthenticated()) {
        setUser(currentUser);
        setTenant(currentTenant);
      }
    } catch (error) {
      console.error('Auth initialization error:', error);
    } finally {
      setLoading(false);
    }
  };

  const login = async (credentials: LoginCredentials): Promise<{ success: boolean; error?: string }> => {
    try {
      console.log('🔑 AuthContext: Starting login process...');
      setLoading(true);
      console.log('🔑 AuthContext: Calling authService.login...');
      const result = await authService.login(credentials);
      console.log('🔑 AuthContext: Received result from authService:', result);
      
      if (result.success) {
        console.log('🔑 AuthContext: Login successful, getting user data...');
        const currentUser = authService.getCurrentUser();
        const currentTenant = authService.getCurrentTenant();
        console.log('🔑 AuthContext: Retrieved user and tenant:', { currentUser, currentTenant });
        
        if (currentUser) {
          console.log('🔑 AuthContext: Setting user and tenant in context...');
          setUser(currentUser);
          setTenant(currentTenant);
          console.log('🔑 AuthContext: Context updated successfully');
        } else {
          console.log('❌ AuthContext: No current user found after successful login');
        }
      } else {
        console.log('❌ AuthContext: Login failed:', result.error);
      }
      
      console.log('🔑 AuthContext: Returning result:', result);
      return result;
    } catch (error) {
      console.error('❌ AuthContext: Critical login error:', error);
      return { success: false, error: 'An unexpected error occurred' };
    } finally {
      setLoading(false);
      console.log('🔑 AuthContext: Login process completed, loading set to false');
    }
  };

  const logout = async (): Promise<void> => {
    try {
      setLoading(true);
      await authService.logout();
      setUser(null);
      setTenant(null);
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setLoading(false);
    }
  };

  const hasPermission = (permission: string): boolean => {
    return authService.hasPermission(permission);
  };

  const refreshToken = async (): Promise<boolean> => {
    try {
      const success = await authService.refreshAuthToken();
      if (success) {
        const currentUser = authService.getCurrentUser();
        const currentTenant = authService.getCurrentTenant();
        setUser(currentUser);
        setTenant(currentTenant);
      }
      return success;
    } catch (error) {
      console.error('Token refresh error:', error);
      return false;
    }
  };

  const value: AuthContextType = {
    user,
    tenant,
    loading,
    login,
    logout,
    hasPermission,
    refreshToken,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

