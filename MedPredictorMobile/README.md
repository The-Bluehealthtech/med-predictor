# Med Predictor Mobile App

A React Native iOS application with multi-tenancy support for accessing player data from the Med Predictor web application.

## Features

-   **Multi-Tenant Support**: Switch between different organizations (Club Africain, Espérance Sportive, FTF)
-   **Player Dashboard**: View comprehensive player data including health scores, injury risk, and performance metrics
-   **Health Records**: Access medical records and health assessments
-   **Licenses**: View active and expired player licenses
-   **Performance Analytics**: Track performance metrics and trends
-   **Secure Authentication**: Connect to web app's RBAC system
-   **Offline Support**: Cache data for offline viewing

## Demo Player

The app is configured to display data for **Ahmed Ben Salah** (Player ID: 4) from **Club Africain** (Tenant ID: 1).

## Prerequisites

### For iOS Development:

-   **macOS** with Xcode 12+ installed
-   **Xcode Command Line Tools**: `xcode-select --install`
-   **CocoaPods**: `sudo gem install cocoapods`
-   **Node.js** 16+
-   **React Native CLI**: `npm install -g @react-native-community/cli`

### For Web Development (Alternative):

-   **Node.js** 16+
-   **Expo CLI**: `npm install -g @expo/cli`

## Installation

### Option 1: iOS Development (Requires Xcode)

1. **Install dependencies:**

    ```bash
    cd MedPredictorMobile
    npm install
    ```

2. **Install iOS dependencies:**

    ```bash
    cd ios
    pod install
    cd ..
    ```

3. **Start Metro bundler:**

    ```bash
    npm start
    ```

4. **Run on iOS Simulator:**
    ```bash
    npm run ios
    ```

### Option 2: Web Development (No Xcode Required)

1. **Install Expo CLI:**

    ```bash
    npm install -g @expo/cli
    ```

2. **Install dependencies:**

    ```bash
    cd MedPredictorMobile
    npm install
    ```

3. **Start Expo development server:**

    ```bash
    npx expo start --web
    ```

4. **Open in browser:**
    - The app will open at `http://localhost:19006`

## Configuration

### API Endpoints

The app connects to your local Med Predictor web application:

-   **Base URL**: `http://localhost/api/v1`
-   **Player Data**: `http://localhost/api/players/{id}/complete-profile`

### Demo Credentials

For testing, use these demo credentials:

-   **Email**: `ahmed.bensalah@clubafricain.tn`
-   **Password**: `demo123`

### Available Tenants

1. **Club Africain** (ID: 1) - Club
2. **Espérance Sportive** (ID: 2) - Club
3. **Fédération Tunisienne de Football** (ID: 3) - Federation

## Architecture

### Multi-Tenancy

The app implements multi-tenancy through:

-   **Tenant Context**: Manages current tenant and available tenants
-   **Tenant-Aware API Calls**: All API requests include tenant context
-   **Tenant Switching**: Users can switch between organizations seamlessly

### Authentication Flow

1. **Login**: Authenticate with web app credentials
2. **Tenant Selection**: Choose organization to access
3. **Data Access**: Fetch tenant-specific player data
4. **RBAC**: Respect web app's role-based permissions

### Data Services

-   **WebAppAuthService**: Handles authentication and token management
-   **PlayerDataService**: Fetches player data with tenant awareness
-   **Context Providers**: Manage app state and data flow

## Project Structure

```
MedPredictorMobile/
├── src/
│   ├── contexts/          # React Context providers
│   │   ├── AuthContext.tsx
│   │   ├── TenantContext.tsx
│   │   └── PlayerDataContext.tsx
│   ├── screens/           # App screens
│   │   ├── LoginScreen.tsx
│   │   ├── TenantSelectionScreen.tsx
│   │   ├── DashboardScreen.tsx
│   │   ├── PlayerProfileScreen.tsx
│   │   ├── HealthRecordsScreen.tsx
│   │   ├── LicensesScreen.tsx
│   │   ├── PerformanceScreen.tsx
│   │   └── SettingsScreen.tsx
│   ├── services/          # API services
│   │   ├── WebAppAuthService.ts
│   │   └── PlayerDataService.ts
│   └── types/             # TypeScript definitions
│       ├── index.ts
│       └── navigation.ts
├── ios/                   # iOS configuration
├── App.tsx                # Main app component
└── package.json
```

## Key Components

### Dashboard Screen

-   **Health Overview**: Physical, mental, sleep, and civic scores
-   **Injury Risk**: Current risk level and assessment
-   **Recent Records**: Latest health records and licenses
-   **Quick Actions**: Navigate to different sections

### Multi-Tenant Features

-   **Tenant Selection**: Choose between Club Africain, Espérance, and FTF
-   **Data Isolation**: Each tenant sees only their organization's data
-   **RBAC Integration**: Respects web app's role-based permissions
-   **Tenant-Aware API**: All requests include tenant context

## Security Features

-   **JWT Token Authentication**: Secure API communication
-   **Token Refresh**: Automatic token renewal
-   **Biometric Authentication**: Optional fingerprint/face ID
-   **Secure Storage**: Encrypted local data storage
-   **RBAC Integration**: Respects web app permissions

## Development

### Adding New Features

1. **Create Service**: Add new API service in `src/services/`
2. **Update Types**: Add TypeScript definitions in `src/types/`
3. **Create Screen**: Add new screen in `src/screens/`
4. **Update Navigation**: Add routes in `App.tsx`
5. **Test**: Verify functionality across tenants

### Testing Multi-Tenancy

1. **Login**: Use demo credentials
2. **Switch Tenants**: Test switching between organizations
3. **Data Isolation**: Verify tenant-specific data access
4. **Permissions**: Test different user roles

## Troubleshooting

### Common Issues

1. **Xcode not found**: Install Xcode from App Store and run `xcode-select --install`
2. **Metro bundler not starting**: Clear cache with `npx react-native start --reset-cache`
3. **iOS build fails**: Run `cd ios && pod install && cd ..`
4. **API connection fails**: Ensure web app is running on `http://localhost`
5. **Authentication errors**: Check web app RBAC configuration

### Debug Mode

Enable debug mode by setting `__DEV__ = true` in the app configuration.

## Production Deployment

### iOS App Store

1. **Configure signing**: Set up Apple Developer account
2. **Build release**: Create production build
3. **TestFlight**: Deploy to TestFlight for testing
4. **App Store**: Submit for App Store review

### Environment Configuration

-   **Development**: `http://localhost`
-   **Staging**: `https://staging.medpredictor.com`
-   **Production**: `https://api.medpredictor.com`

## Support

For technical support or questions:

-   **Email**: support@medpredictor.com
-   **Documentation**: [Web App Documentation](../README.md)
-   **Issues**: Create GitHub issue

## License

© 2024 Med Predictor. All rights reserved.
