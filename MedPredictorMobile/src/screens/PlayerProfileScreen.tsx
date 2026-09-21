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
import { useAuth } from '../contexts/AuthContext';
import { useTenant } from '../contexts/TenantContext';
import { usePlayerData } from '../contexts/PlayerDataContext';
import Icon from 'react-native-vector-icons/MaterialIcons';

const { width } = Dimensions.get('window');

const PlayerProfileScreen: React.FC = () => {
  const { user } = useAuth();
  const { currentTenant } = useTenant();
  const { playerData, loading, refreshPlayerData } = usePlayerData();
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

  const getHealthScoreColor = (score: number) => {
    if (score >= 80) return '#4CAF50';
    if (score >= 60) return '#FF9800';
    return '#F44336';
  };

  if (loading && !playerData) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.loadingText}>Loading profile...</Text>
      </View>
    );
  }

  const player = playerData?.identity;

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
      {/* Header Section */}
      <View style={styles.header}>
        <View style={styles.profileImageContainer}>
          <Image
            source={{
              uri: player?.player_picture || 'https://via.placeholder.com/120x120/2E86AB/FFFFFF?text=ABS'
            }}
            style={styles.profileImage}
          />
        </View>
        <Text style={styles.playerName}>
          {player?.first_name} {player?.last_name}
        </Text>
        <Text style={styles.playerPosition}>{player?.position}</Text>
        <Text style={styles.playerJersey}>#{player?.jersey_number}</Text>
      </View>

      {/* Personal Information */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Personal Information</Text>
        <View style={styles.infoGrid}>
          <View style={styles.infoItem}>
            <Icon name="email" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Email</Text>
            <Text style={styles.infoValue}>{player?.email}</Text>
          </View>
          <View style={styles.infoItem}>
            <Icon name="phone" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Phone</Text>
            <Text style={styles.infoValue}>{player?.phone}</Text>
          </View>
          <View style={styles.infoItem}>
            <Icon name="cake" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Date of Birth</Text>
            <Text style={styles.infoValue}>{player?.date_of_birth}</Text>
          </View>
          <View style={styles.infoItem}>
            <Icon name="public" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Nationality</Text>
            <Text style={styles.infoValue}>{player?.nationality}</Text>
          </View>
          <View style={styles.infoItem}>
            <Icon name="height" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Height</Text>
            <Text style={styles.infoValue}>{player?.height} cm</Text>
          </View>
          <View style={styles.infoItem}>
            <Icon name="fitness-center" size={20} color="#2E86AB" />
            <Text style={styles.infoLabel}>Weight</Text>
            <Text style={styles.infoValue}>{player?.weight} kg</Text>
          </View>
        </View>
      </View>

      {/* Health Scores */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Health Scores</Text>
        <View style={styles.scoreGrid}>
          <View style={styles.scoreItem}>
            <Text style={styles.scoreLabel}>Overall</Text>
            <View style={[styles.scoreCircle, { borderColor: getHealthScoreColor(player?.ghs_overall_score || 0) }]}>
              <Text style={[styles.scoreValue, { color: getHealthScoreColor(player?.ghs_overall_score || 0) }]}>
                {player?.ghs_overall_score}
              </Text>
            </View>
          </View>
          <View style={styles.scoreItem}>
            <Text style={styles.scoreLabel}>Physical</Text>
            <View style={[styles.scoreCircle, { borderColor: getHealthScoreColor(player?.ghs_physical_score || 0) }]}>
              <Text style={[styles.scoreValue, { color: getHealthScoreColor(player?.ghs_physical_score || 0) }]}>
                {player?.ghs_physical_score}
              </Text>
            </View>
          </View>
          <View style={styles.scoreItem}>
            <Text style={styles.scoreLabel}>Mental</Text>
            <View style={[styles.scoreCircle, { borderColor: getHealthScoreColor(player?.ghs_mental_score || 0) }]}>
              <Text style={[styles.scoreValue, { color: getHealthScoreColor(player?.ghs_mental_score || 0) }]}>
                {player?.ghs_mental_score}
              </Text>
            </View>
          </View>
          <View style={styles.scoreItem}>
            <Text style={styles.scoreLabel}>Sleep</Text>
            <View style={[styles.scoreCircle, { borderColor: getHealthScoreColor(player?.ghs_sleep_score || 0) }]}>
              <Text style={[styles.scoreValue, { color: getHealthScoreColor(player?.ghs_sleep_score || 0) }]}>
                {player?.ghs_sleep_score}
              </Text>
            </View>
          </View>
        </View>
      </View>

      {/* Injury Risk */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Injury Risk Assessment</Text>
        <View style={styles.riskContainer}>
          <View style={[
            styles.riskIndicator,
            { backgroundColor: getHealthScoreColor(player?.injury_risk_level === 'Low' ? 85 : player?.injury_risk_level === 'Medium' ? 65 : 45) }
          ]} />
          <Text style={styles.riskLevel}>{player?.injury_risk_level} Risk</Text>
        </View>
      </View>

      {/* Action Buttons */}
      <View style={styles.actionButtons}>
        <TouchableOpacity style={styles.actionButton}>
          <Icon name="edit" size={20} color="#fff" />
          <Text style={styles.actionButtonText}>Edit Profile</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.actionButton, styles.secondaryButton]}>
          <Icon name="share" size={20} color="#2E86AB" />
          <Text style={[styles.actionButtonText, styles.secondaryButtonText]}>Share Profile</Text>
        </TouchableOpacity>
      </View>

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
    alignItems: 'center',
    paddingTop: 40,
  },
  profileImageContainer: {
    marginBottom: 15,
  },
  profileImage: {
    width: 120,
    height: 120,
    borderRadius: 60,
    borderWidth: 4,
    borderColor: '#fff',
  },
  playerName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 5,
  },
  playerPosition: {
    fontSize: 16,
    color: '#E8F4FD',
    marginBottom: 5,
  },
  playerJersey: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#fff',
  },
  section: {
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
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 15,
  },
  infoGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  infoItem: {
    width: '48%',
    marginBottom: 15,
    alignItems: 'center',
  },
  infoLabel: {
    fontSize: 12,
    color: '#666',
    marginTop: 5,
    marginBottom: 3,
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '500',
    color: '#333',
    textAlign: 'center',
  },
  scoreGrid: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    flexWrap: 'wrap',
  },
  scoreItem: {
    alignItems: 'center',
    marginBottom: 15,
  },
  scoreLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 8,
  },
  scoreCircle: {
    width: 60,
    height: 60,
    borderRadius: 30,
    borderWidth: 3,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
  },
  scoreValue: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  riskContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  riskIndicator: {
    width: 20,
    height: 20,
    borderRadius: 10,
    marginRight: 10,
  },
  riskLevel: {
    fontSize: 16,
    fontWeight: '500',
    color: '#333',
  },
  actionButtons: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingHorizontal: 15,
    marginBottom: 20,
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#2E86AB',
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: 25,
    flex: 0.45,
    justifyContent: 'center',
  },
  secondaryButton: {
    backgroundColor: '#fff',
    borderWidth: 2,
    borderColor: '#2E86AB',
  },
  actionButtonText: {
    color: '#fff',
    fontWeight: '500',
    marginLeft: 8,
  },
  secondaryButtonText: {
    color: '#2E86AB',
  },
  bottomSpacing: {
    height: 20,
  },
});

export default PlayerProfileScreen;

