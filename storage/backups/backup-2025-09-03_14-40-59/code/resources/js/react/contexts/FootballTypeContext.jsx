import React, { createContext, useContext, useState, useEffect } from 'react';

const FootballTypeContext = createContext();

export const useFootballType = () => {
  const context = useContext(FootballTypeContext);
  if (!context) {
    throw new Error('useFootballType must be used within a FootballTypeProvider');
  }
  return context;
};

export const FootballTypeProvider = ({ children }) => {
  const [selectedFootballType, setSelectedFootballType] = useState(() => {
    // Initialize from localStorage if available
    const stored = localStorage.getItem('selectedFootballType');
    return stored || null;
  });

  const selectFootballType = (type) => {
    setSelectedFootballType(type);
    localStorage.setItem('selectedFootballType', type);
  };

  const clearFootballType = () => {
    setSelectedFootballType(null);
    localStorage.removeItem('selectedFootballType');
  };

  const value = {
    selectedFootballType,
    selectFootballType,
    clearFootballType
  };

  return (
    <FootballTypeContext.Provider value={value}>
      {children}
    </FootballTypeContext.Provider>
  );
}; 