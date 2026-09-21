import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Switch,
  Alert,
} from 'react-native';
import { useAuth } from '../contexts/AuthContext';
import { useTenant } from '../contexts/TenantContext';
import Icon from 'react-native-vector-icons/MaterialIcons';

const SettingsScreen: React.FC = () => {
  const { user, logout } = useAuth();
  const { currentTenant } = useTenant();
  const [notificationsEnabled, setNotificationsEnabled] = useState(true);
  const [biometricEnabled, setBiometricEnabled] = useState(false);
  const [darkModeEnabled, setDarkModeEnabled] = useState(false);

  const handleLogout = () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        {
          text: 'Cancel',
          style: 'cancel',
        },
        {
          text: 'Logout',
          style: 'destructive',
          onPress: logout,
        },
      ]
    );
  };

  const SettingItem = ({ 
    icon, 
    title, 
    subtitle, 
    onPress, 
    rightComponent,
    showArrow = true 
  }: {
    icon: string;
    title: string;
    subtitle?: string;
    onPress?: () => void;
    rightComponent?: React.ReactNode;
    showArrow?: boolean;
  }) => (
    <TouchableOpacity 
      style={styles.settingItem} 
      onPress={onPress}
      disabled={!onPress}
    >
      <View style={styles.settingLeft}>
        <Icon name={icon} size={24} color="#2E86AB" />
        <View style={styles.settingText}>
          <Text style={styles.settingTitle}>{title}</Text>
          {subtitle && <Text style={styles.settingSubtitle}>{subtitle}</Text>}
        </View>
      </View>
      <View style={styles.settingRight}>
        {rightComponent}
        {showArrow && onPress && (
          <Icon name="chevron-right" size={24} color="#ccc" />
        )}
      </View>
    </TouchableOpacity>
  );

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Settings</Text>
        <Text style={styles.subtitle}>Account and app preferences</Text>
      </View>

      {/* Profile Section */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Profile</Text>
        <SettingItem
          icon="person"
          title="Account Information"
          subtitle="View and edit your profile"
          onPress={() => Alert.alert('Info', 'Profile editing coming soon')}
        />
        <SettingItem
          icon="security"
          title="Security"
          subtitle="Change password and security settings"
          onPress={() => Alert.alert('Info', 'Security settings coming soon')}
        />
        <SettingItem
          icon="notifications"
          title="Notifications"
          subtitle="Manage your notification preferences"
          rightComponent={
            <Switch
              value={notificationsEnabled}
              onValueChange={setNotificationsEnabled}
              trackColor={{ false: '#ccc', true: '#2E86AB' }}
              thumbColor={notificationsEnabled ? '#fff' : '#f4f3f4'}
            />
          }
          showArrow={false}
        />
      </View>

      {/* App Preferences */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>App Preferences</Text>
        <SettingItem
          icon="dark-mode"
          title="Dark Mode"
          subtitle="Switch to dark theme"
          rightComponent={
            <Switch
              value={darkModeEnabled}
              onValueChange={setDarkModeEnabled}
              trackColor={{ false: '#ccc', true: '#2E86AB' }}
              thumbColor={darkModeEnabled ? '#fff' : '#f4f3f4'}
            />
          }
          showArrow={false}
        />
        <SettingItem
          icon="fingerprint"
          title="Biometric Login"
          subtitle="Use fingerprint or face ID"
          rightComponent={
            <Switch
              value={biometricEnabled}
              onValueChange={setBiometricEnabled}
              trackColor={{ false: '#ccc', true: '#2E86AB' }}
              thumbColor={biometricEnabled ? '#fff' : '#f4f3f4'}
            />
          }
          showArrow={false}
        />
        <SettingItem
          icon="language"
          title="Language"
          subtitle="English"
          onPress={() => Alert.alert('Info', 'Language selection coming soon')}
        />
      </View>

      {/* Data & Privacy */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Data & Privacy</Text>
        <SettingItem
          icon="download"
          title="Export Data"
          subtitle="Download your data"
          onPress={() => Alert.alert('Info', 'Data export coming soon')}
        />
        <SettingItem
          icon="delete"
          title="Delete Account"
          subtitle="Permanently delete your account"
          onPress={() => Alert.alert(
            'Delete Account',
            'This action cannot be undone. Are you sure?',
            [
              { text: 'Cancel', style: 'cancel' },
              { text: 'Delete', style: 'destructive' }
            ]
          )}
        />
      </View>

      {/* Support */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Support</Text>
        <SettingItem
          icon="help"
          title="Help Center"
          subtitle="Get help and support"
          onPress={() => Alert.alert('Info', 'Help center coming soon')}
        />
        <SettingItem
          icon="contact-support"
          title="Contact Support"
          subtitle="Get in touch with our team"
          onPress={() => Alert.alert('Info', 'Contact support coming soon')}
        />
        <SettingItem
          icon="info"
          title="About"
          subtitle="App version and information"
          onPress={() => Alert.alert(
            'About MedPredictor',
            'Version 1.0.0\n\nMedPredictor Mobile App\n© 2024 MedPredictor'
          )}
        />
      </View>

      {/* Current User Info */}
      <View style={styles.userInfo}>
        <Text style={styles.userInfoTitle}>Logged in as:</Text>
        <Text style={styles.userInfoText}>{user?.email}</Text>
        {currentTenant && (
          <Text style={styles.tenantInfoText}>{currentTenant.display_name}</Text>
        )}
      </View>

      {/* Logout Button */}
      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
        <Icon name="logout" size={20} color="#fff" />
        <Text style={styles.logoutButtonText}>Logout</Text>
      </TouchableOpacity>

      <View style={styles.bottomSpacing} />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  header: {
    backgroundColor: '#2E86AB',
    padding: 20,
    paddingTop: 40,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 16,
    color: '#E8F4FD',
  },
  section: {
    backgroundColor: '#fff',
    marginTop: 15,
    marginHorizontal: 15,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2E86AB',
    padding: 15,
    paddingBottom: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  settingItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#f8f9fa',
  },
  settingLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  settingText: {
    marginLeft: 15,
    flex: 1,
  },
  settingTitle: {
    fontSize: 16,
    fontWeight: '500',
    color: '#333',
  },
  settingSubtitle: {
    fontSize: 14,
    color: '#666',
    marginTop: 2,
  },
  settingRight: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  userInfo: {
    backgroundColor: '#fff',
    margin: 15,
    padding: 20,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  userInfoTitle: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  userInfoText: {
    fontSize: 16,
    fontWeight: '500',
    color: '#333',
    marginBottom: 4,
  },
  tenantInfoText: {
    fontSize: 14,
    color: '#2E86AB',
  },
  logoutButton: {
    backgroundColor: '#F44336',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 15,
    margin: 15,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  logoutButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
    marginLeft: 8,
  },
  bottomSpacing: {
    height: 20,
  },
});

export default SettingsScreen;

