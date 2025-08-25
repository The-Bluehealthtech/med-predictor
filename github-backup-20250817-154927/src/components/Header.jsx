import React from 'react';
import { useTranslation } from 'react-i18next';

const Header = ({ footballType = "11-a-side Football" }) => {
  const { t } = useTranslation();
  return (
    <header className="bg-white border-b border-gray-200 px-6 py-4">
      <div className="max-w-7xl mx-auto">
        <div className="flex items-center justify-between">
          {/* Left: FIT Logo */}
          <div className="flex items-center">
            <img 
              src="https://via.placeholder.com/120x40/1e40af/ffffff?text=FIT" 
              alt="FIT Logo" 
              className="h-10 w-auto"
            />
          </div>

          {/* Center: Title and Subtext */}
          <div className="flex flex-col items-center text-center">
            <h1 className="text-2xl font-bold text-gray-800 mb-1">
              {t('header.title')}
            </h1>
            <p className="text-sm text-gray-600 font-medium">
              {footballType}
            </p>
          </div>

          {/* Right: The Blue HealthTech Logo */}
          <div className="flex items-center">
            <img 
              src="https://via.placeholder.com/140x40/059669/ffffff?text=TBHC" 
              alt="The Blue HealthTech Logo" 
              className="h-10 w-auto"
            />
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header; 