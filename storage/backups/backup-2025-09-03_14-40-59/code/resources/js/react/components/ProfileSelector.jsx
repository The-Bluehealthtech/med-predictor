import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useFootballType } from '../contexts/FootballTypeContext';
import Footer from './Footer';

const ProfileSelector = () => {
  const navigate = useNavigate();
  const { selectedFootballType, clearFootballType } = useFootballType();

  const getFootballTypeDisplayName = (type) => {
    const typeNames = {
      mens: '11-a-side Football',
      womens: 'Women\'s Football',
      futsal: 'Futsal',
      beach: 'Beach Soccer'
    };
    return typeNames[type] || type;
  };

  const handleBackClick = () => {
    clearFootballType();
    navigate('/select-football');
  };

  const handleProfileSelect = (profile) => {
    // Store the selected profile and football type in localStorage
    localStorage.setItem('selectedProfile', profile);
    localStorage.setItem('selectedFootballType', selectedFootballType);
    
    // Redirect to Laravel login page
    window.location.href = '/login';
  };

  if (!selectedFootballType) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            No Football Type Selected
          </h2>
          <p className="text-gray-600 mb-6">
            Please select a football format first.
          </p>
          <button
            onClick={() => navigate('/select-football')}
            className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Go Back to Selection
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-4">
              {/* FIT Logo */}
              <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-lg">FIT</span>
              </div>
              
              {/* TBHC Logo */}
              <div className="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-xs">TBHC</span>
              </div>
            </div>
            
            <div className="text-right">
              <h1 className="text-2xl font-bold text-gray-900">
                Profile Selection
              </h1>
              <p className="text-sm text-gray-600">
                Football Intelligence & Tracking
              </p>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 flex items-center justify-center py-12">
        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="bg-white rounded-lg shadow-lg p-8">
            <div className="text-center mb-8">
              <h2 className="text-3xl font-bold text-gray-900 mb-4">
                Select Your User Profile
              </h2>
              <p className="text-lg text-gray-600">
                Now select your user profile for{' '}
                <span className="font-semibold text-blue-600">
                  {getFootballTypeDisplayName(selectedFootballType)}
                </span>
              </p>
            </div>

            {/* Profile Options */}
            <div className="space-y-4">
              <button
                onClick={() => handleProfileSelect('player')}
                className="w-full bg-blue-50 border-2 border-blue-200 rounded-lg p-6 text-left hover:bg-blue-100 transition-colors"
              >
                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                  Player
                </h3>
                <p className="text-gray-600">
                  Access your personal dashboard, health records, and match statistics
                </p>
              </button>

              <button
                onClick={() => handleProfileSelect('coach')}
                className="w-full bg-green-50 border-2 border-green-200 rounded-lg p-6 text-left hover:bg-green-100 transition-colors"
              >
                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                  Coach
                </h3>
                <p className="text-gray-600">
                  Manage team lineups, player statistics, and match planning
                </p>
              </button>

              <button
                onClick={() => handleProfileSelect('referee')}
                className="w-full bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 text-left hover:bg-yellow-100 transition-colors"
              >
                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                  Referee
                </h3>
                <p className="text-gray-600">
                  Access match sheets, record events, and manage match proceedings
                </p>
              </button>

              <button
                onClick={() => handleProfileSelect('admin')}
                className="w-full bg-purple-50 border-2 border-purple-200 rounded-lg p-6 text-left hover:bg-purple-100 transition-colors"
              >
                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                  Administrator
                </h3>
                <p className="text-gray-600">
                  Manage competitions, clubs, and system administration
                </p>
              </button>
            </div>

            {/* Back Button */}
            <div className="mt-8 text-center">
              <button
                onClick={handleBackClick}
                className="text-gray-600 hover:text-gray-800 transition-colors"
              >
                ← Back to Football Type Selection
              </button>
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <Footer />
    </div>
  );
};

export default ProfileSelector; 