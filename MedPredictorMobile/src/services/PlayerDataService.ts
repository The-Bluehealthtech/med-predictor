import WebAppAuthService from './WebAppAuthService';
import { 
  Player, 
  HealthRecord, 
  PlayerLicense, 
  PlayerPerformance, 
  PlayerDataResponse,
  ApiResponse 
} from '../types';
import { showMessage } from 'react-native-flash-message';

class PlayerDataService {
  private authService: WebAppAuthService;
  private baseURL: string;

  constructor(authService: WebAppAuthService) {
    this.authService = authService;
    // Connect to MedPredictor Laravel API (running on port 80)
    this.baseURL = 'http://localhost/api';
  }

  // Show notification when using mock data
  private showMockDataNotification() {
    showMessage({
      message: "Demo Mode Active",
      description: "Displaying mock data - Real database not connected",
      type: "warning",
      duration: 5000,
      icon: "warning",
      style: {
        backgroundColor: "#FF9800",
      },
    });
  }

  // Get complete player profile with all data
  async getPlayerData(playerId: number): Promise<PlayerDataResponse['data'] | null> {
    try {
      console.log(`🔍 PlayerDataService: Fetching data for player ID ${playerId}`);
      console.log(`🔍 PlayerDataService: API URL: ${this.baseURL}/players/${playerId}`);
      
      // Try to fetch real data from MedPredictor Laravel API
      console.log(`🔍 PlayerDataService: Attempting to fetch real data from API...`);
      const response = await fetch(`${this.baseURL}/players/${playerId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      });

      console.log(`🔍 PlayerDataService: Response status: ${response.status}`);
      console.log(`🔍 PlayerDataService: Response ok: ${response.ok}`);

      if (response.ok) {
        const apiData = await response.json();
        console.log(`🔍 PlayerDataService: API response:`, apiData);
        
        if (apiData.success && apiData.data) {
          const playerData = apiData.data;
          console.log(`✅ PlayerDataService: Successfully fetched real data for player ${playerId}`);
          
          // Map API data to our expected format with ALL available data
          const mappedData: PlayerDataResponse['data'] = {
            identity: {
              id: playerData.id,
              tenant_id: 1, // Default tenant ID
              name: `${playerData.first_name} ${playerData.last_name}`, // Required name field
              first_name: playerData.first_name,
              last_name: playerData.last_name,
              email: `${playerData.first_name.toLowerCase()}.${playerData.last_name.toLowerCase().replace(' ', '')}@clubafricain.tn`,
              phone: "+216 98 123 456", // Default phone
              date_of_birth: playerData.date_of_birth,
              nationality: playerData.nationality,
              position: playerData.position,
              jersey_number: playerData.id % 99 + 1, // Generate jersey number
              height: playerData.height || 180,
              weight: playerData.weight || 75,
              age: playerData.age,
              player_picture: `https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=${playerData.first_name.charAt(0)}${playerData.last_name.charAt(0)}`,
              
              // FIT Portal Data - All available fields
              ghs_overall_score: playerData.ghs_overall_score,
              ghs_physical_score: playerData.ghs_physical_score,
              ghs_mental_score: playerData.ghs_mental_score,
              ghs_sleep_score: 85, // Default value
              ghs_civic_score: 90, // Default value
              injury_risk_level: playerData.injury_risk_score < 0.3 ? "Low" : playerData.injury_risk_score < 0.7 ? "Medium" : "High",
              
              // Additional FIT Portal Data
              overall_rating: playerData.overall_rating,
              potential_rating: playerData.potential_rating,
              skill_moves: playerData.skill_moves,
              international_reputation: playerData.international_reputation,
              preferred_foot: playerData.preferred_foot,
              injury_risk_score: playerData.injury_risk_score,
              contribution_score: playerData.contribution_score,
              match_availability: playerData.match_availability,
              value_eur: playerData.value_eur,
              wage_eur: playerData.wage_eur,
              last_availability_update: playerData.last_availability_update,
              
              // Club Information
              club_id: playerData.club?.id,
              club_name: playerData.club?.name,
              club_logo_url: playerData.club?.logo_url,
              
              created_at: new Date().toISOString(),
              updated_at: new Date().toISOString()
            },
            health_records: [], // Will be fetched separately
            licenses: [], // Will be fetched separately  
            performances: [] // Will be fetched separately
          };

          console.log('✅ Successfully fetched real player data from API');
          return mappedData;
        }
      } else {
        console.warn(`❌ PlayerDataService: API request failed with status ${response.status}`);
        console.warn(`❌ PlayerDataService: Response status text: ${response.statusText}`);
        this.showMockDataNotification();
      }
      
      // Fall back to mock data for development
      console.log('🔄 PlayerDataService: Falling back to mock data');
      const mockData: PlayerDataResponse['data'] = {
        identity: {
          id: playerId,
          tenant_id: 1, // Default tenant ID
          name: "Ahmed Ben Salah", // Required name field
          first_name: "Ahmed",
          last_name: "Ben Salah",
          email: "ahmed.bensalah@example.com",
          phone: "+216 98 123 456",
          date_of_birth: "1990-05-15",
          nationality: "Tunisian",
          position: "Forward",
          jersey_number: 10,
          height: 180,
          weight: 75,
          age: 33,
          player_picture: "https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS",
          ghs_overall_score: 85,
          ghs_physical_score: 88,
          ghs_mental_score: 82,
          ghs_sleep_score: 80,
          ghs_civic_score: 87,
          injury_risk_level: "Low",
          created_at: "2023-01-01T00:00:00Z",
          updated_at: "2024-01-01T00:00:00Z"
        },
        health_records: [
          {
            id: 1,
            tenant_id: 1,
            user_id: 1,
            player_id: playerId,
            record_date: "2024-01-15",
            visit_type: "Regular Checkup",
            diagnosis: "Healthy",
            status: "active",
            notes: "Player in good health condition",
            created_at: "2024-01-15T00:00:00Z",
            updated_at: "2024-01-15T00:00:00Z"
          },
          {
            id: 2,
            tenant_id: 1,
            user_id: 1,
            player_id: playerId,
            record_date: "2024-02-10",
            visit_type: "Injury Assessment",
            diagnosis: "Minor muscle strain",
            status: "active",
            notes: "Recovered from minor strain",
            created_at: "2024-02-10T00:00:00Z",
            updated_at: "2024-02-10T00:00:00Z"
          }
        ],
        licenses: [
          {
            id: 1,
            tenant_id: 1,
            player_id: playerId,
            license_number: "TN2024001",
            license_type: "Professional",
            status: "active",
            issue_date: "2024-01-01",
            expiry_date: "2024-12-31",
            created_at: "2024-01-01T00:00:00Z",
            updated_at: "2024-01-01T00:00:00Z"
          },
          {
            id: 2,
            tenant_id: 1,
            player_id: playerId,
            license_number: "FIFA2024001",
            license_type: "International",
            status: "active",
            issue_date: "2024-01-01",
            expiry_date: "2024-12-31",
            created_at: "2024-01-01T00:00:00Z",
            updated_at: "2024-01-01T00:00:00Z"
          }
        ],
        performances: [
          {
            id: 1,
            player_id: playerId,
            match_date: "2024-01-20",
            opponent: "Club Africain",
            score: "2-1",
            goals: 1,
            assists: 1,
            rating: 8.5
          }
        ]
      };
      
          console.log('✅ PlayerDataService: Returning mock data:', mockData);
          return mockData;
        } catch (error) {
      console.error('❌ PlayerDataService: Critical error fetching player data:', error);
      console.error('❌ PlayerDataService: Error details:', {
        message: error instanceof Error ? error.message : 'Unknown error',
        stack: error instanceof Error ? error.stack : 'No stack trace',
        playerId: playerId
      });
      
      // Show notification that we're using mock data
      this.showMockDataNotification();
      
      // Always fall back to mock data for development
      console.log('🔄 PlayerDataService: Falling back to mock data due to error');
      const mockData: PlayerDataResponse['data'] = {
        identity: {
          id: playerId,
          first_name: "Ahmed",
          last_name: "Ben Salah",
          email: "ahmed.bensalah@example.com",
          phone: "+216 98 123 456",
          date_of_birth: "1990-05-15",
          nationality: "Tunisian",
          position: "Forward",
          jersey_number: 10,
          height: 180,
          weight: 75,
          age: 33,
          player_picture: "https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS",
          ghs_overall_score: 85,
          ghs_physical_score: 88,
          ghs_mental_score: 82,
          ghs_sleep_score: 80,
          ghs_civic_score: 87,
          injury_risk_level: "Low",
          created_at: "2023-01-01T00:00:00Z",
          updated_at: "2024-01-01T00:00:00Z"
        },
        health_records: [
          {
            id: 1,
            player_id: playerId,
            record_date: "2024-01-15",
            visit_type: "Regular Checkup",
            diagnosis: "Healthy",
            status: "active",
            notes: "Player in good health condition"
          }
        ],
        licenses: [
          {
            id: 1,
            player_id: playerId,
            license_number: "TN2024001",
            license_type: "Professional",
            status: "active",
            issue_date: "2024-01-01",
            expiry_date: "2024-12-31"
          }
        ],
        performances: [
          {
            id: 1,
            player_id: playerId,
            match_date: "2024-01-20",
            opponent: "Club Africain",
            score: "2-1",
            goals: 1,
            assists: 1,
            rating: 8.5
          }
        ]
      };
      
      console.log('✅ PlayerDataService: Returning mock data from catch block:', mockData);
      return mockData;
    }
  }

  // Get player basic information
  async getPlayerBasicInfo(playerId: number): Promise<Player | null> {
    try {
      // Return mock player data for development
      const mockPlayer: Player = {
        id: playerId,
        first_name: "Ahmed",
        last_name: "Ben Salah",
        email: "ahmed.bensalah@example.com",
        phone: "+216 98 123 456",
        date_of_birth: "1990-05-15",
        nationality: "Tunisian",
        position: "Forward",
        jersey_number: 10,
        height: 180,
        weight: 75,
        age: 33,
        player_picture: "https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS",
        ghs_overall_score: 85,
        ghs_physical_score: 88,
        ghs_mental_score: 82,
        ghs_sleep_score: 80,
        ghs_civic_score: 87,
        injury_risk_level: "Low",
        created_at: "2023-01-01T00:00:00Z",
        updated_at: "2024-01-01T00:00:00Z"
      };
      
      return mockPlayer;
    } catch (error) {
      console.error('Error fetching player basic info:', error);
      throw error;
    }
  }

  // Get player health records
  async getPlayerHealthRecords(playerId: number): Promise<HealthRecord[]> {
    try {
      // Return mock data for development
      const mockHealthRecords: HealthRecord[] = [
        {
          id: 1,
          player_id: playerId,
          record_date: "2024-01-15",
          visit_type: "Regular Checkup",
          diagnosis: "Healthy",
          status: "active",
          notes: "Player in good health condition"
        },
        {
          id: 2,
          player_id: playerId,
          record_date: "2024-02-10",
          visit_type: "Injury Assessment",
          diagnosis: "Minor muscle strain",
          status: "active",
          notes: "Recovered from minor strain"
        }
      ];
      
      return mockHealthRecords;
    } catch (error) {
      console.error('Error fetching health records:', error);
      throw error;
    }
  }

  // Get player licenses
  async getPlayerLicenses(playerId: number): Promise<PlayerLicense[]> {
    try {
      // Return mock data for development
      const mockLicenses: PlayerLicense[] = [
        {
          id: 1,
          player_id: playerId,
          license_number: "TN2024001",
          license_type: "Professional",
          status: "active",
          issue_date: "2024-01-01",
          expiry_date: "2024-12-31"
        },
        {
          id: 2,
          player_id: playerId,
          license_number: "FIFA2024001",
          license_type: "International",
          status: "active",
          issue_date: "2024-01-01",
          expiry_date: "2024-12-31"
        }
      ];
      
      return mockLicenses;
    } catch (error) {
      console.error('Error fetching licenses:', error);
      throw error;
    }
  }

  // Get player performances
  async getPlayerPerformances(playerId: number): Promise<PlayerPerformance[]> {
    try {
      // Return mock data for development
      const mockPerformances: PlayerPerformance[] = [
        {
          id: 1,
          player_id: playerId,
          match_date: "2024-01-20",
          opponent: "Club Africain",
          score: "2-1",
          goals: 1,
          assists: 1,
          rating: 8.5
        },
        {
          id: 2,
          player_id: playerId,
          match_date: "2024-01-27",
          opponent: "Espérance de Tunis",
          score: "1-0",
          goals: 0,
          assists: 1,
          rating: 7.5
        }
      ];
      
      return mockPerformances;
    } catch (error) {
      console.error('Error fetching performances:', error);
      throw error;
    }
  }

  // Get player statistics
  async getPlayerStatistics(playerId: number): Promise<any> {
    try {
      // Return mock statistics for development
      const mockStats = {
        total_matches: 25,
        goals: 12,
        assists: 8,
        average_rating: 7.8,
        minutes_played: 2150
      };
      
      return mockStats;
    } catch (error) {
      console.error('Error fetching statistics:', error);
      throw error;
    }
  }

  // Get player health score
  async getPlayerHealthScore(playerId: number): Promise<any> {
    try {
      // Return mock health score for development
      const mockHealthScore = {
        overall_score: 85,
        physical_score: 88,
        mental_score: 82,
        sleep_score: 80,
        civic_score: 87,
        injury_risk: 'Low'
      };
      
      return mockHealthScore;
    } catch (error) {
      console.error('Error fetching health score:', error);
      throw error;
    }
  }

  // Get player injury risk assessment
  async getPlayerInjuryRisk(playerId: number): Promise<any> {
    try {
      // Return mock injury risk for development
      const mockInjuryRisk = {
        level: 'Low',
        score: 85,
        factors: ['Good fitness level', 'No recent injuries', 'Proper rest'],
        recommendations: ['Continue current training', 'Monitor load carefully']
      };
      
      return mockInjuryRisk;
    } catch (error) {
      console.error('Error fetching injury risk:', error);
      throw error;
    }
  }

  // Get player recent activities
  async getPlayerRecentActivities(playerId: number): Promise<any[]> {
    try {
      // Return mock recent activities for development
      const mockActivities = [
        {
          id: 1,
          type: 'training',
          description: 'Morning training session',
          date: '2024-01-20',
          duration: 90
        },
        {
          id: 2,
          type: 'match',
          description: 'Match vs Club Africain',
          date: '2024-01-19',
          duration: 90
        }
      ];
      
      return mockActivities;
    } catch (error) {
      console.error('Error fetching recent activities:', error);
      throw error;
    }
  }

  // Get player notifications
  async getPlayerNotifications(playerId: number): Promise<any[]> {
    try {
      // Return mock notifications for development
      const mockNotifications = [
        {
          id: 1,
          title: 'Health Check Reminder',
          message: 'Your monthly health check is scheduled for tomorrow',
          date: '2024-01-21',
          read: false
        },
        {
          id: 2,
          title: 'Training Update',
          message: 'Training session moved to 10:00 AM',
          date: '2024-01-20',
          read: true
        }
      ];
      
      return mockNotifications;
    } catch (error) {
      console.error('Error fetching notifications:', error);
      throw error;
    }
  }

  // Update player profile (if user has permission)
  async updatePlayerProfile(playerId: number, updates: Partial<Player>): Promise<Player | null> {
    try {
      // Mock profile update for development
      console.log('Mock profile update:', updates);
      
      // Return updated player data
      const mockPlayer: Player = {
        id: playerId,
        first_name: updates.first_name || "Ahmed",
        last_name: updates.last_name || "Ben Salah",
        email: updates.email || "ahmed.bensalah@example.com",
        phone: updates.phone || "+216 98 123 456",
        date_of_birth: updates.date_of_birth || "1990-05-15",
        nationality: updates.nationality || "Tunisian",
        position: updates.position || "Forward",
        jersey_number: updates.jersey_number || 10,
        height: updates.height || 180,
        weight: updates.weight || 75,
        age: updates.age || 33,
        player_picture: updates.player_picture || "https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS",
        ghs_overall_score: updates.ghs_overall_score || 85,
        ghs_physical_score: updates.ghs_physical_score || 88,
        ghs_mental_score: updates.ghs_mental_score || 82,
        ghs_sleep_score: updates.ghs_sleep_score || 80,
        ghs_civic_score: updates.ghs_civic_score || 87,
        injury_risk_level: updates.injury_risk_level || "Low",
        created_at: "2023-01-01T00:00:00Z",
        updated_at: new Date().toISOString()
      };
      
      return mockPlayer;
    } catch (error) {
      console.error('Error updating player profile:', error);
      throw error;
    }
  }

  // Get tenant-specific player data (for multi-tenancy)
  async getTenantPlayerData(playerId: number, tenantId: number): Promise<PlayerDataResponse['data'] | null> {
    try {
      // Return same mock data for tenant-specific requests
      return await this.getPlayerData(playerId);
    } catch (error) {
      console.error('Error fetching tenant player data:', error);
      throw error;
    }
  }

  // Search players within tenant
  async searchTenantPlayers(query: string, tenantId?: number): Promise<Player[]> {
    try {
      // Return mock search results for development
      const mockPlayers: Player[] = [
        {
          id: 1,
          first_name: "Ahmed",
          last_name: "Ben Salah",
          email: "ahmed.bensalah@example.com",
          phone: "+216 98 123 456",
          date_of_birth: "1990-05-15",
          nationality: "Tunisian",
          position: "Forward",
          jersey_number: 10,
          height: 180,
          weight: 75,
          age: 33,
          player_picture: "https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS",
          ghs_overall_score: 85,
          ghs_physical_score: 88,
          ghs_mental_score: 82,
          ghs_sleep_score: 80,
          ghs_civic_score: 87,
          injury_risk_level: "Low",
          created_at: "2023-01-01T00:00:00Z",
          updated_at: "2024-01-01T00:00:00Z"
        }
      ];
      
      // Filter based on query
      return mockPlayers.filter(player => 
        player.first_name.toLowerCase().includes(query.toLowerCase()) ||
        player.last_name.toLowerCase().includes(query.toLowerCase())
      );
    } catch (error) {
      console.error('Error searching players:', error);
      throw error;
    }
  }

  // Get player dashboard data (optimized for mobile)
  async getPlayerDashboardData(playerId: number): Promise<any> {
    try {
      // Return mock dashboard data for development
      const mockDashboardData = {
        player: await this.getPlayerBasicInfo(playerId),
        health_records: await this.getPlayerHealthRecords(playerId),
        licenses: await this.getPlayerLicenses(playerId),
        performances: await this.getPlayerPerformances(playerId),
        statistics: await this.getPlayerStatistics(playerId),
        health_score: await this.getPlayerHealthScore(playerId),
        injury_risk: await this.getPlayerInjuryRisk(playerId),
        recent_activities: await this.getPlayerRecentActivities(playerId),
        notifications: await this.getPlayerNotifications(playerId)
      };
      
      return mockDashboardData;
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
      throw error;
    }
  }
}

export default PlayerDataService;

