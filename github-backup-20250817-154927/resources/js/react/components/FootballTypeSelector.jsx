import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useFootballType } from '../contexts/FootballTypeContext';
import Footer from './Footer';

const FootballTypeSelector = () => {
  const navigate = useNavigate();
  const { selectFootballType } = useFootballType();

  const footballTypes = [
    {
      id: 'mens',
      title: '11-a-side Football',
      description: 'Classic men\'s football format',
      emoji: '⚽',
      color: 'bg-blue-50 border-blue-200 hover:bg-blue-100'
    },
    {
      id: 'womens',
      title: 'Women\'s Football',
      description: 'Women\'s football format',
      emoji: '👩‍🦰',
      color: 'bg-pink-50 border-pink-200 hover:bg-pink-100'
    },
    {
      id: 'futsal',
      title: 'Futsal',
      description: 'Indoor football format',
      emoji: '🏐',
      color: 'bg-green-50 border-green-200 hover:bg-green-100'
    },
    {
      id: 'beach',
      title: 'Beach Soccer',
      description: 'Beach football format',
      emoji: '🏖️',
      color: 'bg-yellow-50 border-yellow-200 hover:bg-yellow-100'
    }
  ];

  const handleCardClick = (footballType) => {
    selectFootballType(footballType);
    navigate('/select-profile');
  };

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
                Welcome to FIT
              </h1>
              <p className="text-sm text-gray-600">
                Football Intelligence & Tracking
              </p>
              <p className="text-xs text-gray-500">
                Powered by The Blue HealthTech
              </p>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 flex items-center justify-center py-12">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">
              Select Your Football Format
            </h2>
            <p className="text-lg text-gray-600">
              Choose the football environment you'll be working with
            </p>
          </div>

          {/* Football Type Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {footballTypes.map((type) => (
              <div
                key={type.id}
                onClick={() => handleCardClick(type.id)}
                className={`${type.color} border-2 rounded-lg p-8 cursor-pointer transition-all duration-200 transform hover:scale-105 hover:shadow-lg`}
              >
                <div className="text-center">
                  <div className="text-6xl mb-4">{type.emoji}</div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">
                    {type.title}
                  </h3>
                  <p className="text-gray-600">
                    {type.description}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </main>

      {/* Footer */}
      <Footer />
    </div>
  );
};

export default FootballTypeSelector; 