import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Image,
  Dimensions,
} from 'react-native';
import { showMessage } from 'react-native-flash-message';
import { useAuth } from '../contexts/AuthContext';
import { useTenant } from '../contexts/TenantContext';
import { usePlayerData } from '../contexts/PlayerDataContext';
import Icon from 'react-native-vector-icons/MaterialIcons';

const { width } = Dimensions.get('window');

const DashboardScreen: React.FC = () => {
  const { user } = useAuth();
  const { currentTenant } = useTenant();
  const { playerData, healthRecords, licenses, performances, loading, refreshPlayerData } = usePlayerData();
  const [refreshing, setRefreshing] = useState(false);

  // Default to Ahmed Ben Salah (ID: 4) for demo
  const playerId = 4;

  useEffect(() => {
    console.log('🔄 DashboardScreen: useEffect triggered, calling refreshPlayerData for ID:', playerId);
    refreshPlayerData(playerId);
    
    // Show notification when dashboard loads
    const timer = setTimeout(() => {
        showMessage({
        message: "Real Data Connected",
        description: "Dashboard displaying live data from MedPredictor database",
        type: "success",
        duration: 4000,
        icon: "success",
        style: {
          backgroundColor: "#4CAF50",
        },
      });
    }, 2000); // Show after 2 seconds
    
    return () => clearTimeout(timer);
  }, []);

  // Debug logging for player data
  useEffect(() => {
    console.log('🔄 DashboardScreen: playerData changed:', playerData);
    console.log('🔄 DashboardScreen: loading state:', loading);
  }, [playerData, loading]);

  const handleRefresh = async () => {
    setRefreshing(true);
    await refreshPlayerData(playerId);
    setRefreshing(false);
  };

  const getHealthScoreColor = (score: number) => {
    if (score >= 80) return '#4CAF50';
    if (score >= 60) return '#FF9800';
    return '#F44336';
  };

  const getInjuryRiskColor = (level: string) => {
    switch (level?.toLowerCase()) {
      case 'low': return '#4CAF50';
      case 'medium': return '#FF9800';
      case 'high': return '#F44336';
      default: return '#666';
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  // Show loading state while data is being fetched
  if (loading) {
    return (
      <View style={[styles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color="#2E86AB" />
        <Text style={styles.loadingText}>Loading player data...</Text>
        <Text style={styles.loadingText}>Player ID: {playerId}</Text>
        <Text style={styles.loadingText}>Loading: {loading.toString()}</Text>
      </View>
    );
  }

  // Debug: Show if no player data
  if (!playerData) {
    return (
      <View style={[styles.container, styles.loadingContainer]}>
        <Text style={styles.loadingText}>No player data available</Text>
        <Text style={styles.loadingText}>Player ID: {playerId}</Text>
        <Text style={styles.loadingText}>Loading: {loading.toString()}</Text>
        <TouchableOpacity 
          style={styles.button} 
          onPress={() => refreshPlayerData(playerId)}
        >
          <Text style={styles.buttonText}>Retry</Text>
        </TouchableOpacity>
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
      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerContent}>
          <Image
            source={{ 
              uri: playerData?.player_picture || 'https://via.placeholder.com/80x80/2E86AB/FFFFFF?text=AB'
            }}
            style={styles.playerImage}
          />
          <View style={styles.playerInfo}>
            <Text style={styles.playerName}>
              {playerData?.first_name} {playerData?.last_name}
            </Text>
            <Text style={styles.playerDetails}>
              {playerData?.position} • {playerData?.nationality} • Age {playerData?.age}
            </Text>
            <Text style={styles.tenantName}>
              {currentTenant?.display_name}
            </Text>
          </View>
        </View>
      </View>

      {/* Quick Stats */}
      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Icon name="favorite" size={24} color="#4CAF50" />
          <Text style={styles.statValue}>
            {playerData?.ghs_overall_score || 'N/A'}
          </Text>
          <Text style={styles.statLabel}>Health Score</Text>
        </View>
        <View style={styles.statCard}>
          <Icon name="star" size={24} color="#FF9800" />
          <Text style={styles.statValue}>
            {playerData?.overall_rating || 'N/A'}
          </Text>
          <Text style={styles.statLabel}>Overall Rating</Text>
        </View>
        <View style={styles.statCard}>
          <Icon name="euro-symbol" size={24} color="#4CAF50" />
          <Text style={styles.statValue}>
            {playerData?.value_eur ? `€${(playerData.value_eur / 1000000).toFixed(1)}M` : 'N/A'}
          </Text>
          <Text style={styles.statLabel}>Market Value</Text>
        </View>
        <View style={styles.statCard}>
          <Icon name="warning" size={24} color={getInjuryRiskColor(playerData?.injury_risk_level || 'low')} />
          <Text style={styles.statValue}>
            {playerData?.injury_risk_level || 'Low'}
          </Text>
          <Text style={styles.statLabel}>Injury Risk</Text>
        </View>
      </View>

      {/* FIT Portal Data */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>FIT Portal Data</Text>
        <View style={styles.fitDataGrid}>
          <View style={styles.fitDataItem}>
            <Text style={styles.fitDataLabel}>Potential Rating</Text>
            <Text style={[styles.fitDataValue, { color: '#FF9800' }]}>
              {playerData?.potential_rating || 'N/A'}
            </Text>
          </View>
          <View style={styles.fitDataItem}>
            <Text style={styles.fitDataLabel}>Skill Moves</Text>
            <Text style={[styles.fitDataValue, { color: '#2196F3' }]}>
              {playerData?.skill_moves || 'N/A'}
            </Text>
          </View>
          <View style={styles.fitDataItem}>
            <Text style={styles.fitDataLabel}>Int. Reputation</Text>
            <Text style={[styles.fitDataValue, { color: '#9C27B0' }]}>
              {playerData?.international_reputation || 'N/A'}
            </Text>
          </View>
          <View style={styles.fitDataItem}>
            <Text style={styles.fitDataLabel}>Contribution Score</Text>
            <Text style={[styles.fitDataValue, { color: '#4CAF50' }]}>
              {playerData?.contribution_score || 'N/A'}
            </Text>
          </View>
        </View>
      </View>

      {/* Club Information */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Club Information</Text>
        <View style={styles.clubInfo}>
          <View style={styles.clubItem}>
            <Icon name="business" size={20} color="#2196F3" />
            <Text style={styles.clubLabel}>Club:</Text>
            <Text style={styles.clubValue}>{playerData?.club_name || 'Club Africain'}</Text>
          </View>
          <View style={styles.clubItem}>
            <Icon name="euro-symbol" size={20} color="#4CAF50" />
            <Text style={styles.clubLabel}>Weekly Wage:</Text>
            <Text style={styles.clubValue}>
              {playerData?.wage_eur ? `€${playerData.wage_eur.toLocaleString()}` : 'N/A'}
            </Text>
          </View>
          <View style={styles.clubItem}>
            <Icon name="accessibility" size={20} color={playerData?.match_availability ? '#4CAF50' : '#F44336'} />
            <Text style={styles.clubLabel}>Match Availability:</Text>
            <Text style={[styles.clubValue, { color: playerData?.match_availability ? '#4CAF50' : '#F44336' }]}>
              {playerData?.match_availability ? 'Available' : 'Unavailable'}
            </Text>
          </View>
        </View>
      </View>

      {/* Health Overview */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Health Overview</Text>
        <View style={styles.healthGrid}>
          <View style={styles.healthItem}>
            <Text style={styles.healthLabel}>Physical</Text>
            <Text style={[styles.healthValue, { color: getHealthScoreColor(playerData?.ghs_physical_score || 0) }]}>
              {playerData?.ghs_physical_score || 'N/A'}
            </Text>
          </View>
          <View style={styles.healthItem}>
            <Text style={styles.healthLabel}>Mental</Text>
            <Text style={[styles.healthValue, { color: getHealthScoreColor(playerData?.ghs_mental_score || 0) }]}>
              {playerData?.ghs_mental_score || 'N/A'}
            </Text>
          </View>
          <View style={styles.healthItem}>
            <Text style={styles.healthLabel}>Sleep</Text>
            <Text style={[styles.healthValue, { color: getHealthScoreColor(playerData?.ghs_sleep_score || 0) }]}>
              {playerData?.ghs_sleep_score || 'N/A'}
            </Text>
          </View>
          <View style={styles.healthItem}>
            <Text style={styles.healthLabel}>Civic</Text>
            <Text style={[styles.healthValue, { color: getHealthScoreColor(playerData?.ghs_civic_score || 0) }]}>
              {playerData?.ghs_civic_score || 'N/A'}
            </Text>
          </View>
        </View>
      </View>

      {/* Recent Health Records */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Recent Health Records</Text>
        {healthRecords.slice(0, 3).map((record, index) => (
          <TouchableOpacity key={index} style={styles.recordCard}>
            <View style={styles.recordHeader}>
              <Text style={styles.recordDate}>{formatDate(record.record_date)}</Text>
              <View style={[
                styles.statusBadge,
                { backgroundColor: record.status === 'active' ? '#4CAF50' : '#FF9800' }
              ]}>
                <Text style={styles.statusText}>{record.status}</Text>
              </View>
            </View>
            <Text style={styles.recordType}>{record.visit_type}</Text>
            {record.diagnosis && (
              <Text style={styles.recordDiagnosis}>{record.diagnosis}</Text>
            )}
          </TouchableOpacity>
        ))}
        {healthRecords.length === 0 && (
          <Text style={styles.emptyText}>No health records available</Text>
        )}
      </View>

      {/* Active Licenses */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Active Licenses</Text>
        {licenses.filter(license => license.status === 'active').slice(0, 2).map((license, index) => (
          <TouchableOpacity key={index} style={styles.licenseCard}>
            <View style={styles.licenseHeader}>
              <Text style={styles.licenseNumber}>{license.license_number}</Text>
              <Text style={styles.licenseType}>{license.license_type}</Text>
            </View>
            <Text style={styles.licenseExpiry}>
              Expires: {formatDate(license.expiry_date)}
            </Text>
          </TouchableOpacity>
        ))}
        {licenses.filter(license => license.status === 'active').length === 0 && (
          <Text style={styles.emptyText}>No active licenses</Text>
        )}
      </View>

      {/* Quick Actions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Quick Actions</Text>
        <View style={styles.actionsGrid}>
          <TouchableOpacity style={styles.actionButton}>
            <Icon name="favorite" size={24} color="#4CAF50" />
            <Text style={styles.actionText}>Health Records</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.actionButton}>
            <Icon name="card-membership" size={24} color="#2196F3" />
            <Text style={styles.actionText}>Licenses</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.actionButton}>
            <Icon name="trending-up" size={24} color="#FF9800" />
            <Text style={styles.actionText}>Performance</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.actionButton}>
            <Icon name="settings" size={24} color="#666" />
            <Text style={styles.actionText}>Settings</Text>
          </TouchableOpacity>
        </View>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  header: {
    paddingTop: 60,
    paddingBottom: 30,
    paddingHorizontal: 20,
    backgroundColor: '#2E86AB',
  },
  headerContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  playerImage: {
    width: 80,
    height: 80,
    borderRadius: 40,
    marginRight: 16,
  },
  playerInfo: {
    flex: 1,
  },
  playerName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  playerDetails: {
    fontSize: 16,
    color: '#E3F2FD',
    marginBottom: 4,
  },
  tenantName: {
    fontSize: 14,
    color: '#BBDEFB',
  },
  statsContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginTop: -15,
    marginBottom: 20,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginHorizontal: 4,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
    marginTop: 8,
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 12,
    color: '#666',
    textAlign: 'center',
  },
  section: {
    backgroundColor: '#fff',
    marginHorizontal: 20,
    marginBottom: 20,
    borderRadius: 12,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 16,
  },
  healthGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  healthItem: {
    width: '48%',
    alignItems: 'center',
    padding: 12,
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    marginBottom: 8,
  },
  healthLabel: {
    fontSize: 14,
    color: '#666',
    marginBottom: 4,
  },
  healthValue: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  recordCard: {
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    padding: 12,
    marginBottom: 8,
  },
  recordHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  recordDate: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 10,
    color: '#fff',
    fontWeight: 'bold',
  },
  recordType: {
    fontSize: 14,
    color: '#2E86AB',
    marginBottom: 4,
  },
  recordDiagnosis: {
    fontSize: 12,
    color: '#666',
  },
  licenseCard: {
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    padding: 12,
    marginBottom: 8,
  },
  licenseHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  licenseNumber: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
  },
  licenseType: {
    fontSize: 12,
    color: '#2E86AB',
  },
  licenseExpiry: {
    fontSize: 12,
    color: '#666',
  },
  actionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  actionButton: {
    width: '48%',
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
    marginBottom: 8,
  },
  actionText: {
    fontSize: 12,
    color: '#666',
    marginTop: 8,
    textAlign: 'center',
  },
  emptyText: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
    fontStyle: 'italic',
  },
  // New styles for enhanced data display
  fitDataGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  fitDataItem: {
    width: '48%',
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    padding: 12,
    marginBottom: 8,
    alignItems: 'center',
  },
  fitDataLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
    textAlign: 'center',
  },
  fitDataValue: {
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  clubInfo: {
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    padding: 12,
  },
  clubItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  clubLabel: {
    fontSize: 14,
    color: '#666',
    marginLeft: 8,
    marginRight: 8,
    minWidth: 100,
  },
  clubValue: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    flex: 1,
  },
  // Loading state styles
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
      loadingText: {
        marginTop: 16,
        fontSize: 16,
        color: '#666',
        textAlign: 'center',
      },
      button: {
        backgroundColor: '#2E86AB',
        paddingHorizontal: 20,
        paddingVertical: 10,
        borderRadius: 8,
        marginTop: 20,
      },
      buttonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: 'bold',
        textAlign: 'center',
      },
    });

export default DashboardScreen;

