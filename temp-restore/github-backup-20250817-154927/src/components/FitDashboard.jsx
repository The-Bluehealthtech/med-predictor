import React from 'react';
import Header from './Header';
import Footer from './Footer';
import ModuleCard from './ModuleCard';
import { useTranslation } from 'react-i18next';

const FitDashboard = ({ footballType = "11-a-side Football" }) => {
  const { t } = useTranslation();
  const modules = [
    {
      title: t('modules.medical.title'),
      description: t('modules.medical.description'),
      icon: "🏥",
      link: "/medical"
    },
    {
      title: t('modules.licensing.title'),
      description: t('modules.licensing.description'),
      icon: "📋",
      link: "/licensing"
    },
    {
      title: t('modules.competitions.title'),
      description: t('modules.competitions.description'),
      icon: "🏆",
      link: "/competitions"
    },
    {
      title: t('modules.analytics.title'),
      description: t('modules.analytics.description'),
      icon: "📊",
      link: "/analytics"
    }
  ];

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Header footballType={footballType} />
      
      <main className="flex-grow px-6 py-8">
        <div className="max-w-7xl mx-auto">
          <div className="mb-8 text-center">
            <h2 className="text-3xl font-bold text-gray-800 mb-2">
              {t('dashboard.select_module')}
            </h2>
            <p className="text-gray-600 max-w-2xl mx-auto">
              {t('dashboard.description')}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            {modules.map((module, index) => (
              <ModuleCard
                key={index}
                title={module.title}
                description={module.description}
                icon={module.icon}
                link={module.link}
              />
            ))}
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default FitDashboard; 