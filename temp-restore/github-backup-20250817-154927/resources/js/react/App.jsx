import React from 'react';
import { HashRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { FootballTypeProvider } from './contexts/FootballTypeContext';
import FootballTypeSelector from './components/FootballTypeSelector';
import ProfileSelector from './components/ProfileSelector';
import AuthCheck from './components/AuthCheck';

const App = () => {
  console.log('App component rendering');
  
  return (
    <FootballTypeProvider>
      <Router>
        <div className="App">
          <Routes>
            <Route path="/select-football" element={<FootballTypeSelector />} />
            <Route 
              path="/select-profile" 
              element={
                <AuthCheck>
                  <ProfileSelector />
                </AuthCheck>
              } 
            />
            <Route path="/" element={<Navigate to="/select-football" replace />} />
            {/* Catch-all route for any undefined routes */}
            <Route path="*" element={<Navigate to="/select-football" replace />} />
          </Routes>
        </div>
      </Router>
    </FootballTypeProvider>
  );
};

export default App; 