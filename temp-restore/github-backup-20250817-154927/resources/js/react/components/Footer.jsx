import React from 'react';

const Footer = () => {
  return (
    <footer className="bg-white border-t border-gray-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div className="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
          <div className="text-sm text-gray-500">
            © 2024 FIT - Football Intelligence & Tracking
          </div>
          
          <nav className="flex flex-wrap justify-center sm:justify-end space-x-6 text-sm">
            <a
              href="#"
              className="text-gray-500 hover:text-gray-700 transition-colors"
            >
              Change Language
            </a>
            <a
              href="#"
              className="text-gray-500 hover:text-gray-700 transition-colors"
            >
              Support
            </a>
            <a
              href="/admin"
              className="text-gray-500 hover:text-gray-700 transition-colors"
            >
              Admin Login
            </a>
            <a
              href="https://www.fifa.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-500 hover:text-gray-700 transition-colors"
            >
              FIFA Official Website
            </a>
            <a
              href="https://www.tbhc.uk"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-500 hover:text-gray-700 transition-colors"
            >
              The Blue HealthTech
            </a>
          </nav>
        </div>
      </div>
    </footer>
  );
};

export default Footer; 