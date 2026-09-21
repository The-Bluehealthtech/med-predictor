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

const HealthRecordsScreen: React.FC = () => {
  const { healthRecords, loading, refreshPlayerData } = usePlayerData();
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
      case 'resolved': return '#2196F3';
      case 'ongoing': return '#FF9800';
      default: return '#666';
    }
  };

  const getVisitTypeIcon = (visitType: string) => {
    switch (visitType?.toLowerCase()) {
      case 'regular checkup': return 'health-and-safety';
      case 'injury assessment': return 'healing';
      case 'medical clearance': return 'verified-user';
      case 'vaccination': return 'vaccines';
      default: return 'medical-services';
    }
  };

  if (loading && !healthRecords) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.loadingText}>Loading health records...</Text>
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
        <Text style={styles.title}>Health Records</Text>
        <Text style={styles.subtitle}>Medical history and assessments</Text>
      </View>

      {healthRecords && healthRecords.length > 0 ? (
        healthRecords.map((record) => (
          <View key={record.id} style={styles.recordCard}>
            <View style={styles.recordHeader}>
              <View style={styles.visitTypeContainer}>
                <Icon 
                  name={getVisitTypeIcon(record.visit_type)} 
                  size={24} 
                  color="#2E86AB" 
                />
                <Text style={styles.visitType}>{record.visit_type}</Text>
              </View>
              <View style={[
                styles.statusBadge,
                { backgroundColor: getStatusColor(record.status) }
              ]}>
                <Text style={styles.statusText}>{record.status}</Text>
              </View>
            </View>

            <View style={styles.recordDetails}>
              <View style={styles.detailRow}>
                <Icon name="event" size={16} color="#666" />
                <Text style={styles.detailLabel}>Date:</Text>
                <Text style={styles.detailValue}>
                  {new Date(record.record_date).toLocaleDateString()}
                </Text>
              </View>

              <View style={styles.detailRow}>
                <Icon name="diagnosis" size={16} color="#666" />
                <Text style={styles.detailLabel}>Diagnosis:</Text>
                <Text style={styles.detailValue}>{record.diagnosis}</Text>
              </View>

              {record.notes && (
                <View style={styles.notesContainer}>
                  <Icon name="notes" size={16} color="#666" />
                  <Text style={styles.detailLabel}>Notes:</Text>
                  <Text style={styles.notesText}>{record.notes}</Text>
                </View>
              )}
            </View>

            <TouchableOpacity style={styles.viewDetailsButton}>
              <Text style={styles.viewDetailsText}>View Full Details</Text>
              <Icon name="arrow-forward" size={16} color="#2E86AB" />
            </TouchableOpacity>
          </View>
        ))
      ) : (
        <View style={styles.emptyState}>
          <Icon name="medical-services" size={64} color="#ccc" />
          <Text style={styles.emptyTitle}>No Health Records</Text>
          <Text style={styles.emptySubtitle}>
            Your medical history will appear here once records are added.
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
  recordCard: {
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
  recordHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  visitTypeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  visitType: {
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
  recordDetails: {
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
  notesContainer: {
    marginTop: 8,
  },
  notesText: {
    fontSize: 14,
    color: '#333',
    marginLeft: 24,
    fontStyle: 'italic',
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

export default HealthRecordsScreen;

