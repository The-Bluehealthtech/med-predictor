import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  FlatList,
  Image,
  Alert,
  RefreshControl,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { StackNavigationProp } from '@react-navigation/stack';
import { RootStackParamList } from '../types/navigation';
import { useTenant } from '../contexts/TenantContext';
import { Tenant } from '../types';
import Icon from 'react-native-vector-icons/MaterialIcons';

type TenantSelectionNavigationProp = StackNavigationProp<RootStackParamList, 'TenantSelection'>;

const TenantSelectionScreen: React.FC = () => {
  const navigation = useNavigation<TenantSelectionNavigationProp>();
  const { currentTenant, availableTenants, loading, switchTenant, refreshTenants } = useTenant();
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    refreshTenants();
  }, []);

  const handleTenantSelect = async (tenant: Tenant) => {
    try {
      const result = await switchTenant(tenant.id);
      
      if (result.success) {
        // Navigate to main app
        navigation.replace('MainTabs');
      } else {
        Alert.alert('Error', result.error || 'Failed to switch tenant');
      }
    } catch (error) {
      Alert.alert('Error', 'Failed to switch tenant');
    }
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await refreshTenants();
    setRefreshing(false);
  };

  const renderTenantItem = ({ item }: { item: Tenant }) => (
    <TouchableOpacity
      style={[
        styles.tenantCard,
        currentTenant?.id === item.id && styles.selectedTenantCard
      ]}
      onPress={() => handleTenantSelect(item)}
    >
      <View style={styles.tenantHeader}>
        <Image
          source={{ 
            uri: item.logo_url || 'https://via.placeholder.com/60x60/2E86AB/FFFFFF?text=' + item.name.charAt(0)
          }}
          style={styles.tenantLogo}
        />
        <View style={styles.tenantInfo}>
          <Text style={styles.tenantName}>{item.display_name}</Text>
          <Text style={styles.tenantType}>
            {item.type === 'player' ? '👤 Player Portal' : 
             item.type === 'club' ? '🏟️ Club' : 
             item.type === 'federation' ? '🏛️ Federation' : 
             item.type === 'association' ? '⚽ Association' : 
             '🏢 Organization'}
          </Text>
          <Text style={styles.tenantCountry}>📍 {item.country}</Text>
        </View>
        {currentTenant?.id === item.id && (
          <Icon name="check-circle" size={24} color="#2E86AB" />
        )}
      </View>
      
      {item.description && (
        <Text style={styles.tenantDescription}>{item.description}</Text>
      )}
      
      <View style={styles.tenantStatus}>
        <View style={[
          styles.statusIndicator,
          { backgroundColor: item.status === 'active' ? '#4CAF50' : '#FF9800' }
        ]} />
        <Text style={styles.statusText}>
          {item.status === 'active' ? 'Active' : 'Inactive'}
        </Text>
      </View>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Welcome Back!</Text>
        <Text style={styles.subtitle}>
          Access your player profile and data
        </Text>
      </View>

      {loading && !refreshing ? (
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>Loading organizations...</Text>
        </View>
      ) : (
        <FlatList
          data={availableTenants}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderTenantItem}
          contentContainerStyle={styles.listContainer}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={handleRefresh}
              colors={['#2E86AB']}
              tintColor="#2E86AB"
            />
          }
          showsVerticalScrollIndicator={false}
        />
      )}

      <View style={styles.footer}>
        <Text style={styles.footerText}>
          Player Portal
        </Text>
        <Text style={styles.footerSubtext}>
          Your personal health and performance dashboard
        </Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  header: {
    padding: 20,
    paddingTop: 60,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: '#666',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    fontSize: 16,
    color: '#666',
  },
  listContainer: {
    padding: 20,
  },
  tenantCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 20,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  selectedTenantCard: {
    borderColor: '#2E86AB',
    backgroundColor: '#f0f8ff',
  },
  tenantHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  tenantLogo: {
    width: 60,
    height: 60,
    borderRadius: 30,
    marginRight: 16,
  },
  tenantInfo: {
    flex: 1,
  },
  tenantName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  tenantType: {
    fontSize: 14,
    color: '#666',
    marginBottom: 2,
  },
  tenantCountry: {
    fontSize: 12,
    color: '#999',
  },
  tenantDescription: {
    fontSize: 14,
    color: '#666',
    marginBottom: 12,
    lineHeight: 20,
  },
  tenantStatus: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  statusIndicator: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginRight: 8,
  },
  statusText: {
    fontSize: 12,
    color: '#666',
    fontWeight: '500',
  },
  footer: {
    padding: 20,
    alignItems: 'center',
    backgroundColor: '#fff',
    borderTopWidth: 1,
    borderTopColor: '#e0e0e0',
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

export default TenantSelectionScreen;

