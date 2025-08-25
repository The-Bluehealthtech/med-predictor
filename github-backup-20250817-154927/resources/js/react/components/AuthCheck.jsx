import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const AuthCheck = ({ children }) => {
  const navigate = useNavigate();
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Check if user is authenticated by making a request to Laravel
    const checkAuth = async () => {
      try {
        const response = await fetch('/api/auth/check', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'include', // Include cookies
        });

        if (response.ok) {
          const data = await response.json();
          if (data.authenticated) {
            setIsAuthenticated(true);
          } else {
            // Not authenticated, redirect to login
            window.location.href = '/login';
          }
        } else {
          // Error response, redirect to login
          window.location.href = '/login';
        }
      } catch (error) {
        console.error('Auth check failed:', error);
        // Network error, redirect to login
        window.location.href = '/login';
      } finally {
        setIsLoading(false);
      }
    };

    checkAuth();
  }, [navigate]);

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p className="text-gray-600">Checking authentication...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return null; // Will redirect to login
  }

  return children;
};

export default AuthCheck; 