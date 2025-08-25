import React from 'react';
import { useTranslation } from 'react-i18next';

const Footer = () => {
  const { t } = useTranslation();
  return (
    <footer className="bg-gray-50 border-t border-gray-200 mt-auto">
      <div className="max-w-7xl mx-auto px-6 py-8">
        <div className="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <div className="flex flex-wrap justify-center md:justify-start space-x-6 text-sm">
            <a 
              href="https://www.fifa.com" 
              target="_blank" 
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-800 transition-colors duration-200"
            >
              {t('footer.fifa')}
            </a>
            <a 
              href="https://www.tbhc.uk" 
              target="_blank" 
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-800 transition-colors duration-200"
            >
              {t('footer.tbhc')}
            </a>
            <button className="text-gray-600 hover:text-gray-800 transition-colors duration-200">
              {t('footer.language')}
            </button>
          </div>
          
          <div className="flex flex-wrap justify-center md:justify-end space-x-6 text-sm">
            <button className="text-gray-600 hover:text-gray-800 transition-colors duration-200">
              {t('footer.admin_login')}
            </button>
            <button className="text-gray-600 hover:text-gray-800 transition-colors duration-200">
              {t('footer.support')}
            </button>
          </div>
        </div>
        
        <div className="mt-6 pt-6 border-t border-gray-200 text-center">
          <p className="text-xs text-gray-500">
            {t('footer.copyright')}
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer; 