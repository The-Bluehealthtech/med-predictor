import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const ModuleCard = ({ title, description, icon, link }) => {
  const { t } = useTranslation();
  return (
    <Link to={link} className="block">
      <div className="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group">
        <div className="flex flex-col items-center text-center h-full">
          <div className="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
            {icon}
          </div>
          <h3 className="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
            {title}
          </h3>
          <p className="text-gray-600 text-sm leading-relaxed flex-grow">
            {description}
          </p>
          <div className="mt-4 text-blue-500 font-medium text-sm group-hover:text-blue-600 transition-colors duration-300">
            {t('modules.access')}
          </div>
        </div>
      </div>
    </Link>
  );
};

export default ModuleCard; 