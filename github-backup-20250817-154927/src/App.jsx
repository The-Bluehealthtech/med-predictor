import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import FitDashboard from './components/FitDashboard';

// Placeholder components for each module
const MedicalModule = () => (
  <div className="min-h-screen bg-gray-50 flex items-center justify-center">
    <div className="text-center">
      <h1 className="text-4xl font-bold text-gray-800 mb-4">🏥 Medical Module</h1>
      <p className="text-gray-600 mb-6">Health and performance data tracking</p>
      <a href="/" className="text-blue-600 hover:text-blue-800 underline">
        ← Back to Dashboard
      </a>
    </div>
  </div>
);

const LicensingModule = () => (
  <div className="min-h-screen bg-gray-50 flex items-center justify-center">
    <div className="text-center">
      <h1 className="text-4xl font-bold text-gray-800 mb-4">📋 Licensing Module</h1>
      <p className="text-gray-600 mb-6">Player registrations and license management</p>
      <a href="/" className="text-blue-600 hover:text-blue-800 underline">
        ← Back to Dashboard
      </a>
    </div>
  </div>
);

const CompetitionsModule = () => (
  <div className="min-h-screen bg-gray-50 flex items-center justify-center">
    <div className="text-center">
      <h1 className="text-4xl font-bold text-gray-800 mb-4">🏆 Competitions</h1>
      <p className="text-gray-600 mb-6">Tournament and match management</p>
      <a href="/" className="text-blue-600 hover:text-blue-800 underline">
        ← Back to Dashboard
      </a>
    </div>
  </div>
);

const AnalyticsModule = () => (
  <div className="min-h-screen bg-gray-50 flex items-center justify-center">
    <div className="text-center">
      <h1 className="text-4xl font-bold text-gray-800 mb-4">📊 Analytics & Reports</h1>
      <p className="text-gray-600 mb-6">Statistical analysis and insights</p>
      <a href="/" className="text-blue-600 hover:text-blue-800 underline">
        ← Back to Dashboard
      </a>
    </div>
  </div>
);

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<FitDashboard />} />
        <Route path="/medical" element={<MedicalModule />} />
        <Route path="/licensing" element={<LicensingModule />} />
        <Route path="/competitions" element={<CompetitionsModule />} />
        <Route path="/analytics" element={<AnalyticsModule />} />
      </Routes>
    </Router>
  );
}

export default App; 