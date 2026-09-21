import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Image,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { RootStackParamList } from '../types/navigation';
import { useAuth } from '../contexts/AuthContext';
import { LoginCredentials } from '../types';
import Icon from 'react-native-vector-icons/MaterialIcons';

type LoginScreenNavigationProp = StackNavigationProp<RootStackParamList, 'Login'>;

const LoginScreen: React.FC = () => {
  const navigation = useNavigation<LoginScreenNavigationProp>();
  const { login, loading } = useAuth();
  const [credentials, setCredentials] = useState<LoginCredentials>({
    email: '',
    password: '',
  });
  const [showPassword, setShowPassword] = useState(false);

  const handleLogin = async () => {
    console.log('🔐 LoginScreen: handleLogin called with:', credentials);
    
    if (!credentials.email || !credentials.password) {
      console.log('❌ LoginScreen: Missing credentials');
      Alert.alert('Error', 'Please enter both email and password');
      return;
    }

    console.log('📞 LoginScreen: Calling login function...');
    const result = await login(credentials);
    console.log('📞 LoginScreen: Login result:', result);
    
    if (result.success) {
      console.log('✅ LoginScreen: Login successful, navigating to TenantSelection');
      // Navigate to tenant selection or main app
      navigation.replace('TenantSelection');
    } else {
      console.log('❌ LoginScreen: Login failed:', result.error);
      Alert.alert('Login Failed', result.error || 'Invalid credentials');
    }
  };

  const handleDemoLogin = () => {
    // Demo credentials for MedPredictor
    setCredentials({
      email: 'admin@medpredictor.com',
      password: 'admin123',
    });
  };

  return (
    <KeyboardAvoidingView 
      style={styles.container} 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.header}>
          <Image 
            source={{ uri: 'https://via.placeholder.com/120x120/2E86AB/FFFFFF?text=MP' }}
            style={styles.logo}
          />
          <Text style={styles.title}>Med Predictor</Text>
          <Text style={styles.subtitle}>Player Portal</Text>
        </View>

        <View style={styles.form}>
          <View style={styles.inputContainer}>
            <Icon name="email" size={20} color="#666" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Email"
              placeholderTextColor="#999"
              value={credentials.email}
              onChangeText={(text) => setCredentials({ ...credentials, email: text })}
              keyboardType="email-address"
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          <View style={styles.inputContainer}>
            <Icon name="lock" size={20} color="#666" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Password"
              placeholderTextColor="#999"
              value={credentials.password}
              onChangeText={(text) => setCredentials({ ...credentials, password: text })}
              secureTextEntry={!showPassword}
              autoCapitalize="none"
              autoCorrect={false}
            />
            <TouchableOpacity
              style={styles.eyeIcon}
              onPress={() => setShowPassword(!showPassword)}
            >
              <Icon 
                name={showPassword ? "visibility-off" : "visibility"} 
                size={20} 
                color="#666" 
              />
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            style={[styles.loginButton, loading && styles.loginButtonDisabled]}
            onPress={handleLogin}
            disabled={loading}
          >
            <Text style={styles.loginButtonText}>
              {loading ? 'Signing In...' : 'Sign In'}
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.demoButton}
            onPress={handleDemoLogin}
          >
            <Text style={styles.demoButtonText}>Use Demo Credentials</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.footer}>
          <Text style={styles.footerText}>
            Multi-Tenant Player Portal
          </Text>
          <Text style={styles.footerSubtext}>
            Access your player data across different organizations
          </Text>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  scrollContainer: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: 20,
  },
  header: {
    alignItems: 'center',
    marginBottom: 40,
  },
  logo: {
    width: 120,
    height: 120,
    borderRadius: 60,
    marginBottom: 20,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 18,
    color: '#666',
    fontWeight: '500',
  },
  form: {
    marginBottom: 40,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    borderRadius: 12,
    marginBottom: 16,
    paddingHorizontal: 16,
    paddingVertical: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    fontSize: 16,
    color: '#333',
    paddingVertical: 16,
  },
  eyeIcon: {
    padding: 8,
  },
  loginButton: {
    backgroundColor: '#2E86AB',
    borderRadius: 12,
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: 8,
    shadowColor: '#2E86AB',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 6,
  },
  loginButtonDisabled: {
    backgroundColor: '#ccc',
    shadowOpacity: 0,
    elevation: 0,
  },
  loginButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  demoButton: {
    backgroundColor: 'transparent',
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
    marginTop: 16,
    borderWidth: 1,
    borderColor: '#2E86AB',
  },
  demoButtonText: {
    color: '#2E86AB',
    fontSize: 16,
    fontWeight: '500',
  },
  footer: {
    alignItems: 'center',
    marginTop: 20,
  },
  footerText: {
    fontSize: 16,
    color: '#666',
    fontWeight: '500',
    marginBottom: 4,
  },
  footerSubtext: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
  },
});

export default LoginScreen;

