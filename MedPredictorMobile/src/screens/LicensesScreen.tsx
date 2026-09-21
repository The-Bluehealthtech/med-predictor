import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import { usePlayerData } from '../contexts/PlayerDataContext';
import Icon from 'react-native-vector-icons/MaterialIcons';

const LicensesScreen: React.FC = () => {
  const { licenses, loading, refreshPlayerData } = usePlayerData();
  const [refreshing, setRefreshing] = useState(false);

  // Default to Ahmed Ben Salah (ID: 4) for demo
  const playerId = 4;

  useEffect(() => {
    refreshPlayerData(playerId);
  }, []);

  const handleRefresh = async () => {
    setRefreshing(true);
    await refreshPlayerData(playerId);
    setRefreshing(false);
  };

  const getStatusColor = (status: string) => {
    switch (status?.toLowerCase()) {
      case 'active': return '#4CAF50';
      case 'expired': return '#F44336';
      case 'pending': return '#FF9800';
      case 'suspended': return '#9C27B0';
      default: return '#666';
    }
  };

  const getLicenseTypeIcon = (licenseType: string) => {
    switch (licenseType?.toLowerCase()) {
      case 'professional': return 'work';
      case 'international': return 'public';
      case 'youth': return 'child-care';
      case 'amateur': return 'sports';
      default: return 'card-membership';
    }
  };

  const isExpiringSoon = (expiryDate: string) => {
    const expiry = new Date(expiryDate);
    const today = new Date();
    const diffTime = expiry.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays <= 30 && diffDays > 0;
  };

  if (loading && !licenses) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.loadingText}>Loading licenses...</Text>
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.container}
      refreshControl={
        <RefreshControl
          refreshing={refreshing}
          onRefresh={handleRefresh}
          colors={['#2E86AB']}
          tintColor="#2E86AB"
        />
      }
    >
      <View style={styles.header}>
        <Text style={styles.title}>Player Licenses</Text>
        <Text style={styles.subtitle}>Active and expired licenses</Text>
      </View>

      {licenses && licenses.length > 0 ? (
        licenses.map((license) => (
          <View key={license.id} style={styles.licenseCard}>
            <View style={styles.licenseHeader}>
              <View style={styles.licenseTypeContainer}>
                <Icon 
                  name={getLicenseTypeIcon(license.license_type)} 
                  size={24} 
                  color="#2E86AB" 
                />
                <Text style={styles.licenseType}>{license.license_type}</Text>
              </View>
              <View style={[
                styles.statusBadge,
                { backgroundColor: getStatusColor(license.status) }
              ]}>
                <Text style={styles.statusText}>{license.status}</Text>
              </View>
            </View>

            <View style={styles.licenseDetails}>
              <View style={styles.detailRow}>
                <Icon name="badge" size={16} color="#666" />
                <Text style={styles.detailLabel}>License Number:</Text>
                <Text style={styles.detailValue}>{license.license_number}</Text>
              </View>

              <View style={styles.detailRow}>
                <Icon name="event" size={16} color="#666" />
                <Text style={styles.detailLabel}>Issue Date:</Text>
                <Text style={styles.detailValue}>
                  {new Date(license.issue_date).toLocaleDateString()}
                </Text>
              </View>

              <View style={styles.detailRow}>
                <Icon name="event-busy" size={16} color="#666" />
                <Text style={styles.detailLabel}>Expiry Date:</Text>
                <Text style={[
                  styles.detailValue,
                  isExpiringSoon(license.expiry_date) && { color: '#FF9800', fontWeight: 'bold' }
                ]}>
                  {new Date(license.expiry_date).toLocaleDateString()}
                  {isExpiringSoon(license.expiry_date) && ' (Expires Soon!)'}
                </Text>
              </View>
            </View>

            {isExpiringSoon(license.expiry_date) && (
              <View style={styles.warningContainer}>
                <Icon name="warning" size={16} color="#FF9800" />
                <Text style={styles.warningText}>
                  This license expires soon. Please renew to maintain eligibility.
                </Text>
              </View>
            )}

            <TouchableOpacity style={styles.viewDetailsButton}>
              <Text style={styles.viewDetailsText}>View License Details</Text>
              <Icon name="arrow-forward" size={16} color="#2E86AB" />
            </TouchableOpacity>
          </View>
        ))
      ) : (
        <View style={styles.emptyState}>
          <Icon name="card-membership" size={64} color="#ccc" />
          <Text style={styles.emptyTitle}>No Licenses Found</Text>
          <Text style={styles.emptySubtitle}>
            Your licenses will appear here once they are registered.
          </Text>
        </View>
      )}

      <View style={styles.bottomSpacing} />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8f9fa',
  },
  loadingText: {
    fontSize: 16,
    color: '#666',
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
  licenseCard: {
    backgroundColor: '#fff',
    margin: 15,
    borderRadius: 10,
    padding: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  licenseHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  licenseTypeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  licenseType: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2E86AB',
    marginLeft: 8,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '500',
    color: '#fff',
  },
  licenseDetails: {
    marginBottom: 15,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  detailLabel: {
    fontSize: 14,
    color: '#666',
    marginLeft: 8,
    marginRight: 8,
    fontWeight: '500',
  },
  detailValue: {
    fontSize: 14,
    color: '#333',
    flex: 1,
  },
  warningContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFF3E0',
    padding: 10,
    borderRadius: 8,
    marginBottom: 15,
  },
  warningText: {
    fontSize: 12,
    color: '#FF9800',
    marginLeft: 8,
    flex: 1,
  },
  viewDetailsButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  viewDetailsText: {
    fontSize: 14,
    color: '#2E86AB',
    fontWeight: '500',
    marginRight: 4,
  },
  emptyState: {
    alignItems: 'center',
    padding: 40,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#666',
    marginTop: 16,
    marginBottom: 8,
  },
  emptySubtitle: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
    lineHeight: 20,
  },
  bottomSpacing: {
    height: 20,
  },
});

export default LicensesScreen;

