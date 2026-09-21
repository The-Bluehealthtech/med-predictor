import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { StatusBar } from 'react-native';
import { Provider as PaperProvider } from 'react-native-paper';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import FlashMessage from 'react-native-flash-message';
import Icon from 'react-native-vector-icons/MaterialIcons';

// Context Providers
import { AuthProvider } from './src/contexts/AuthContext';
import { TenantProvider } from './src/contexts/TenantContext';
import { PlayerDataProvider } from './src/contexts/PlayerDataContext';

// Screens
import LoginScreen from './src/screens/LoginScreen';
import TenantSelectionScreen from './src/screens/TenantSelectionScreen';
import DashboardScreen from './src/screens/DashboardScreen';
import PlayerProfileScreen from './src/screens/PlayerProfileScreen';
import HealthRecordsScreen from './src/screens/HealthRecordsScreen';
import LicensesScreen from './src/screens/LicensesScreen';
import PerformanceScreen from './src/screens/PerformanceScreen';
import SettingsScreen from './src/screens/SettingsScreen';

// Types
import { RootStackParamList, TabParamList } from './src/types/navigation';

const Stack = createStackNavigator<RootStackParamList>();
const Tab = createBottomTabNavigator<TabParamList>();
const queryClient = new QueryClient();

// Main Tab Navigator
const MainTabs = () => {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName: string;

          switch (route.name) {
            case 'Dashboard':
              iconName = 'dashboard';
              break;
            case 'Profile':
              iconName = 'person';
              break;
            case 'Health':
              iconName = 'favorite';
              break;
            case 'Licenses':
              iconName = 'card-membership';
              break;
            case 'Performance':
              iconName = 'trending-up';
              break;
            case 'Settings':
              iconName = 'settings';
              break;
            default:
              iconName = 'help';
          }

          return <Icon name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#2E86AB',
        tabBarInactiveTintColor: 'gray',
        headerShown: false,
      })}
    >
      <Tab.Screen name="Dashboard" component={DashboardScreen} />
      <Tab.Screen name="Profile" component={PlayerProfileScreen} />
      <Tab.Screen name="Health" component={HealthRecordsScreen} />
      <Tab.Screen name="Licenses" component={LicensesScreen} />
      <Tab.Screen name="Performance" component={PerformanceScreen} />
      <Tab.Screen name="Settings" component={SettingsScreen} />
    </Tab.Navigator>
  );
};

// Main App Component
const App: React.FC = () => {
  return (
    <QueryClientProvider client={queryClient}>
      <PaperProvider>
        <AuthProvider>
          <TenantProvider>
            <PlayerDataProvider>
              <NavigationContainer>
                <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
                <Stack.Navigator
                  screenOptions={{
                    headerShown: false,
                  }}
                >
                  <Stack.Screen name="Login" component={LoginScreen} />
                  <Stack.Screen name="TenantSelection" component={TenantSelectionScreen} />
                  <Stack.Screen name="MainTabs" component={MainTabs} />
                </Stack.Navigator>
                <FlashMessage position="top" />
              </NavigationContainer>
            </PlayerDataProvider>
          </TenantProvider>
        </AuthProvider>
      </PaperProvider>
    </QueryClientProvider>
  );
};

export default App;