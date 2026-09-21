import React, { createContext, useContext, useState, ReactNode } from 'react';
import WebAppAuthService from '../services/WebAppAuthService';
import PlayerDataService from '../services/PlayerDataService';
import { Player, HealthRecord, PlayerLicense, PlayerPerformance, PlayerDataContextType } from '../types';

const PlayerDataContext = createContext<PlayerDataContextType | undefined>(undefined);

interface PlayerDataProviderProps {
  children: ReactNode;
}

export const PlayerDataProvider: React.FC<PlayerDataProviderProps> = ({ children }) => {
  const [playerData, setPlayerData] = useState<Player | null>(null);
  const [healthRecords, setHealthRecords] = useState<HealthRecord[]>([]);
  const [licenses, setLicenses] = useState<PlayerLicense[]>([]);
  const [performances, setPerformances] = useState<PlayerPerformance[]>([]);
  const [loading, setLoading] = useState(false);
  const [isError, setIsError] = useState(false);
  
  const [authService] = useState(() => new WebAppAuthService());
  const [dataService] = useState(() => new PlayerDataService(authService));

  const refreshPlayerData = async (playerId: number): Promise<void> => {
    try {
      console.log('🔄 PlayerDataContext: Starting to refresh player data for ID:', playerId);
      setLoading(true);
      setIsError(false);
      const data = await dataService.getPlayerData(playerId);
      console.log('🔄 PlayerDataContext: Received data:', data);
      if (data?.identity) {
        console.log('✅ PlayerDataContext: Setting player data:', data.identity);
        setPlayerData(data.identity);
      } else {
        console.warn('❌ PlayerDataContext: No identity data received');
      }
    } catch (error) {
      console.error('❌ PlayerDataContext: Error refreshing player data:', error);
      setIsError(true);
    } finally {
      setLoading(false);
      console.log('🔄 PlayerDataContext: Loading set to false');
    }
  };

  const refreshHealthRecords = async (playerId: number): Promise<void> => {
    try {
      setLoading(true);
      const records = await dataService.getPlayerHealthRecords(playerId);
      setHealthRecords(records);
    } catch (error) {
      console.error('Error refreshing health records:', error);
    } finally {
      setLoading(false);
    }
  };

  const refreshLicenses = async (playerId: number): Promise<void> => {
    try {
      setLoading(true);
      const playerLicenses = await dataService.getPlayerLicenses(playerId);
      setLicenses(playerLicenses);
    } catch (error) {
      console.error('Error refreshing licenses:', error);
    } finally {
      setLoading(false);
    }
  };

  const refreshPerformances = async (playerId: number): Promise<void> => {
    try {
      setLoading(true);
      const playerPerformances = await dataService.getPlayerPerformances(playerId);
      setPerformances(playerPerformances);
    } catch (error) {
      console.error('Error refreshing performances:', error);
    } finally {
      setLoading(false);
    }
  };

  const value: PlayerDataContextType = {
    playerData,
    healthRecords,
    licenses,
    performances,
    loading,
    isError,
    refreshPlayerData,
    refreshHealthRecords,
    refreshLicenses,
    refreshPerformances,
  };

  return (
    <PlayerDataContext.Provider value={value}>
      {children}
    </PlayerDataContext.Provider>
  );
};

export const usePlayerData = (): PlayerDataContextType => {
  const context = useContext(PlayerDataContext);
  if (context === undefined) {
    throw new Error('usePlayerData must be used within a PlayerDataProvider');
  }
  return context;
};

