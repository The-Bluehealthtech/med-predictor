import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import '../../css/app.css';

console.log('React main.jsx loaded successfully');
console.log('React version:', React.version);
console.log('createRoot available:', typeof createRoot);

// Wait for the DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM Content Loaded event fired');
  const container = document.getElementById('react-app');
  console.log('Container element:', container);
  console.log('Container innerHTML:', container?.innerHTML);
  
  if (container) {
    console.log('Container found, attempting to mount React app...');
    try {
      const root = createRoot(container);
      console.log('Root created successfully');
      root.render(<App />);
      console.log('React app mounted successfully');
    } catch (error) {
      console.error('Error mounting React app:', error);
    }
  } else {
    console.error('React app container not found!');
    console.log('Available elements with id:', document.querySelectorAll('[id]'));
  }
});

// Also try immediate mounting if DOM is already ready
if (document.readyState === 'loading') {
  console.log('Document still loading, waiting for DOMContentLoaded');
} else {
  console.log('Document already ready, mounting immediately');
  const container = document.getElementById('react-app');
  if (container) {
    console.log('Mounting React app immediately...');
    const root = createRoot(container);
    root.render(<App />);
    console.log('React app mounted immediately');
  }
} 