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

const PerformanceScreen: React.FC = () => {
  const { performances, loading, refreshPlayerData } = usePlayerData();
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

  const getRatingColor = (rating: number) => {
    if (rating >= 8) return '#4CAF50';
    if (rating >= 6) return '#FF9800';
    return '#F44336';
  };

  const getPerformanceIcon = (goals: number, assists: number) => {
    if (goals > 0 && assists > 0) return 'emoji-events';
    if (goals > 0) return 'sports-soccer';
    if (assists > 0) return 'assistant';
    return 'sports';
  };

  const calculateSeasonStats = () => {
    if (!performances || performances.length === 0) {
      return { totalGoals: 0, totalAssists: 0, avgRating: 0, totalMatches: 0 };
    }

    const totalGoals = performances.reduce((sum, perf) => sum + (perf.goals || 0), 0);
    const totalAssists = performances.reduce((sum, perf) => sum + (perf.assists || 0), 0);
    const totalRating = performances.reduce((sum, perf) => sum + (perf.rating || 0), 0);
    const avgRating = totalRating / performances.length;
    const totalMatches = performances.length;

    return { totalGoals, totalAssists, avgRating, totalMatches };
  };

  if (loading && !performances) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.loadingText}>Loading performance data...</Text>
      </View>
    );
  }

  const seasonStats = calculateSeasonStats();

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
        <Text style={styles.title}>Performance Analytics</Text>
        <Text style={styles.subtitle}>Match performance and statistics</Text>
      </View>

      {/* Season Stats Overview */}
      <View style={styles.statsContainer}>
        <Text style={styles.statsTitle}>Season Overview</Text>
        <View style={styles.statsGrid}>
          <View style={styles.statCard}>
            <Icon name="sports-soccer" size={24} color="#4CAF50" />
            <Text style={styles.statNumber}>{seasonStats.totalGoals}</Text>
            <Text style={styles.statLabel}>Goals</Text>
          </View>
          <View style={styles.statCard}>
            <Icon name="assistant" size={24} color="#2196F3" />
            <Text style={styles.statNumber}>{seasonStats.totalAssists}</Text>
            <Text style={styles.statLabel}>Assists</Text>
          </View>
          <View style={styles.statCard}>
            <Icon name="trending-up" size={24} color="#FF9800" />
            <Text style={styles.statNumber}>{seasonStats.avgRating.toFixed(1)}</Text>
            <Text style={styles.statLabel}>Avg Rating</Text>
          </View>
          <View style={styles.statCard}>
            <Icon name="event" size={24} color="#9C27B0" />
            <Text style={styles.statNumber}>{seasonStats.totalMatches}</Text>
            <Text style={styles.statLabel}>Matches</Text>
          </View>
        </View>
      </View>

      {/* Match Performance List */}
      <View style={styles.matchesSection}>
        <Text style={styles.sectionTitle}>Recent Matches</Text>
        {performances && performances.length > 0 ? (
          performances.map((performance) => (
            <View key={performance.id} style={styles.matchCard}>
              <View style={styles.matchHeader}>
                <View style={styles.matchInfo}>
                  <Text style={styles.opponent}>{performance.opponent}</Text>
                  <Text style={styles.matchDate}>
                    {new Date(performance.match_date).toLocaleDateString()}
                  </Text>
                </View>
                <View style={styles.scoreContainer}>
                  <Text style={styles.score}>{performance.score}</Text>
                  <View style={[
                    styles.ratingBadge,
                    { backgroundColor: getRatingColor(performance.rating || 0) }
                  ]}>
                    <Text style={styles.ratingText}>{performance.rating}</Text>
                  </View>
                </View>
              </View>

              <View style={styles.performanceStats}>
                <View style={styles.statItem}>
                  <Icon name="sports-soccer" size={16} color="#4CAF50" />
                  <Text style={styles.statLabel}>Goals: {performance.goals}</Text>
                </View>
                <View style={styles.statItem}>
                  <Icon name="assistant" size={16} color="#2196F3" />
                  <Text style={styles.statLabel}>Assists: {performance.assists}</Text>
                </View>
                <View style={styles.statItem}>
                  <Icon name="star" size={16} color="#FF9800" />
                  <Text style={styles.statLabel}>Rating: {performance.rating}</Text>
                </View>
              </View>

              <TouchableOpacity style={styles.viewDetailsButton}>
                <Text style={styles.viewDetailsText}>View Match Details</Text>
                <Icon name="arrow-forward" size={16} color="#2E86AB" />
              </TouchableOpacity>
            </View>
          ))
        ) : (
          <View style={styles.emptyState}>
            <Icon name="sports" size={64} color="#ccc" />
            <Text style={styles.emptyTitle}>No Match Data</Text>
            <Text style={styles.emptySubtitle}>
              Your match performance will appear here once games are recorded.
            </Text>
          </View>
        )}
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
  statsContainer: {
    backgroundColor: '#fff',
    margin: 15,
    borderRadius: 10,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statsTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 15,
  },
  statsGrid: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    flexWrap: 'wrap',
  },
  statCard: {
    alignItems: 'center',
    width: '22%',
    marginBottom: 15,
  },
  statNumber: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
    marginTop: 8,
  },
  statLabel: {
    fontSize: 12,
    color: '#666',
    marginTop: 4,
  },
  matchesSection: {
    margin: 15,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 15,
  },
  matchCard: {
    backgroundColor: '#fff',
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  matchHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  matchInfo: {
    flex: 1,
  },
  opponent: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  matchDate: {
    fontSize: 14,
    color: '#666',
  },
  scoreContainer: {
    alignItems: 'center',
  },
  score: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2E86AB',
    marginBottom: 8,
  },
  ratingBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  ratingText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#fff',
  },
  performanceStats: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 15,
  },
  statItem: {
    flexDirection: 'row',
    alignItems: 'center',
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

export default PerformanceScreen;

