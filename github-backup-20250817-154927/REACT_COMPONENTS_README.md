# FIT React Components

This document describes the React-based user flow components for the FIT (Football Intelligence & Tracking) platform.

## Overview

The React components provide a modern, accessible user interface for the initial user flow of the FIT platform, allowing users to select their football format and user profile.

## Components Structure

```
resources/js/react/
├── App.jsx                    # Main React application with routing
├── contexts/
│   └── FootballTypeContext.jsx # Context for managing football type selection
├── components/
│   ├── FootballTypeSelector.jsx # Football format selection page
│   ├── ProfileSelector.jsx      # User profile selection page
│   └── Footer.jsx              # Footer component with links
└── assets/
    ├── fit-logo.svg           # FIT logo placeholder
    └── tbhc-logo.svg          # TBHC logo placeholder
```

## Features

### 1. FootballTypeSelector.jsx

-   **Purpose**: Landing page for football format selection
-   **Features**:
    -   4 clickable cards for different football formats:
        -   11-a-side Football (Men's)
        -   Women's Football
        -   Futsal
        -   Beach Soccer
    -   Responsive grid layout
    -   Hover effects with scale and shadow
    -   Header with FIT and TBHC logos
    -   Welcome text and branding

### 2. ProfileSelector.jsx

-   **Purpose**: User role selection after football type selection
-   **Features**:
    -   Displays selected football type
    -   4 profile options:
        -   Player
        -   Coach
        -   Referee
        -   Administrator
    -   Back button to return to football type selection
    -   Error handling for missing football type selection

### 3. FootballTypeContext.jsx

-   **Purpose**: Global state management for football type selection
-   **Features**:
    -   localStorage persistence
    -   Context provider for React components
    -   Functions for selecting and clearing football type

### 4. Footer.jsx

-   **Purpose**: Consistent footer across all pages
-   **Links**:
    -   Change Language
    -   Support
    -   Admin Login
    -   FIFA Official Website
    -   The Blue HealthTech

## Technical Details

### Dependencies

-   React 18+
-   React Router DOM 6+
-   TailwindCSS for styling
-   Vite for build tooling

### Routing

-   `/select-football` → FootballTypeSelector
-   `/select-profile` → ProfileSelector
-   `/` → Redirects to `/select-football`

### State Management

-   Uses React Context API
-   localStorage for persistence
-   No external state management libraries required

### Styling

-   TailwindCSS utility classes
-   Responsive design
-   Classic web app aesthetics
-   No experimental animations

## Usage

### Accessing the React App

Visit: `http://localhost:8000/react-app`

### Development

1. Start Laravel server: `php artisan serve`
2. Start Vite dev server: `npm run dev`
3. Build for production: `npm run build`

### Integration with Laravel

-   React app is served through Laravel Blade view
-   Uses Laravel's Vite integration
-   Can be extended to integrate with Laravel backend APIs

## Future Enhancements

1. **Logo Integration**: Replace placeholder logos with actual SVG assets
2. **API Integration**: Connect to Laravel backend for user authentication
3. **Internationalization**: Add multi-language support
4. **Accessibility**: Enhance ARIA labels and keyboard navigation
5. **Testing**: Add unit and integration tests

## Browser Support

-   Modern browsers with ES6+ support
-   React 18 features
-   CSS Grid and Flexbox for layout

## Performance

-   Code splitting with React Router
-   Optimized builds with Vite
-   Minimal bundle size
-   Fast loading times
