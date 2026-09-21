import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import WebAppAuthService from '../services/WebAppAuthService';
import { Tenant, TenantContextType } from '../types';
import { useAuth } from './AuthContext';

const TenantContext = createContext<TenantContextType | undefined>(undefined);

interface TenantProviderProps {
  children: ReactNode;
}

export const TenantProvider: React.FC<TenantProviderProps> = ({ children }) => {
  const [currentTenant, setCurrentTenant] = useState<Tenant | null>(null);
  const [availableTenants, setAvailableTenants] = useState<Tenant[]>([]);
  const [loading, setLoading] = useState(false);
  const { user, tenant } = useAuth();
  const [authService] = useState(() => new WebAppAuthService());

  useEffect(() => {
    if (user && tenant) {
      setCurrentTenant(tenant);
      loadAvailableTenants();
    }
  }, [user, tenant]);

  const loadAvailableTenants = async (): Promise<void> => {
    try {
      setLoading(true);
      const tenants = await authService.getAvailableTenants();
      setAvailableTenants(tenants);
    } catch (error) {
      console.error('Error loading available tenants:', error);
      // Set default player tenant for demo purposes
      setAvailableTenants([
        {
          id: 1,
          name: 'Ahmed Ben Salah',
          slug: 'ahmed-bensalah',
          display_name: 'Player Portal',
          type: 'player',
          status: 'active',
          country: 'TN',
          timezone: 'Africa/Tunis',
          language: 'en',
          logo_url: 'https://via.placeholder.com/100x50/2E86AB/FFFFFF?text=ABS',
          description: 'Your personal health and performance dashboard',
          settings: {},
          metadata: {},
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
        },
      ]);
    } finally {
      setLoading(false);
    }
  };

  const switchTenant = async (tenantId: number): Promise<{ success: boolean; error?: string }> => {
    try {
      setLoading(true);
      const result = await authService.switchTenant(tenantId);
      
      if (result.success) {
        const newTenant = availableTenants.find(t => t.id === tenantId);
        if (newTenant) {
          setCurrentTenant(newTenant);
        }
      }
      
      return result;
    } catch (error) {
      console.error('Switch tenant error:', error);
      return { success: false, error: 'Failed to switch tenant' };
    } finally {
      setLoading(false);
    }
  };

  const refreshTenants = async (): Promise<void> => {
    await loadAvailableTenants();
  };

  const value: TenantContextType = {
    currentTenant,
    availableTenants,
    loading,
    switchTenant,
    refreshTenants,
  };

  return (
    <TenantContext.Provider value={value}>
      {children}
    </TenantContext.Provider>
  );
};

export const useTenant = (): TenantContextType => {
  const context = useContext(TenantContext);
  if (context === undefined) {
    throw new Error('useTenant must be used within a TenantProvider');
  }
  return context;
};

