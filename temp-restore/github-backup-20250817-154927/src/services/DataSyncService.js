const axios = require('axios');
const { DeviceData } = require('../models/DeviceData');
const { Player } = require('../models/Player');
const OAuth2Service = require('./OAuth2Service');
const { oauth2Config } = require('../config/oauth2');
const logger = require('../utils/logger');
const NodeCache = require('node-cache');

class DataSyncService {
  constructor() {
    this.cache = new NodeCache({ stdTTL: 300 }); // 5 minutes cache
    this.syncInProgress = new Set();
  }

  // Fetch data from Catapult Connect
  async fetchCatapultData(playerId, deviceId, startDate, endDate) {
    const accessToken = await OAuth2Service.getValidAccessToken('catapult', playerId, deviceId);
    
    try {
      const response = await axios.get('https://connect.catapultsports.com/api/v1/activities', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'User-Agent': oauth2Config.catapult.common.userAgent
        },
        params: {
          device_id: deviceId,
          start_date: startDate.toISOString(),
          end_date: endDate.toISOString(),
          include_gps: true,
          include_biometrics: true
        },
        timeout: oauth2Config.catapult.common.timeout
      });

      return this.normalizeCatapultData(response.data);
    } catch (error) {
      logger.error('Failed to fetch Catapult data:', error.response?.data || error.message);
      throw new Error(`Catapult data fetch failed: ${error.message}`);
    }
  }

  // Fetch data from Apple HealthKit
  async fetchAppleData(playerId, deviceId, startDate, endDate) {
    const accessToken = await OAuth2Service.getValidAccessToken('apple', playerId, deviceId);
    
    try {
      const response = await axios.get('https://api.apple.com/health/activities', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'User-Agent': oauth2Config.apple.common.userAgent
        },
        params: {
          device_id: deviceId,
          start_date: startDate.toISOString(),
          end_date: endDate.toISOString(),
          data_types: 'heart_rate,heart_rate_variability,steps,distance,calories,location'
        },
        timeout: oauth2Config.apple.common.timeout
      });

      return this.normalizeAppleData(response.data);
    } catch (error) {
      logger.error('Failed to fetch Apple HealthKit data:', error.response?.data || error.message);
      throw new Error(`Apple HealthKit data fetch failed: ${error.message}`);
    }
  }

  // Fetch data from Garmin Connect
  async fetchGarminData(playerId, deviceId, startDate, endDate) {
    const accessToken = await OAuth2Service.getValidAccessToken('garmin', playerId, deviceId);
    
    try {
      const response = await axios.get('https://connect.garmin.com/modern/proxy/activitylist-service/activities', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'User-Agent': oauth2Config.garmin.common.userAgent
        },
        params: {
          device_id: deviceId,
          start_date: startDate.toISOString(),
          end_date: endDate.toISOString(),
          include_gps: true,
          include_heart_rate: true
        },
        timeout: oauth2Config.garmin.common.timeout
      });

      return this.normalizeGarminData(response.data);
    } catch (error) {
      logger.error('Failed to fetch Garmin data:', error.response?.data || error.message);
      throw new Error(`Garmin data fetch failed: ${error.message}`);
    }
  }

  // Normalize Catapult data to common schema
  normalizeCatapultData(rawData) {
    return rawData.activities.map(activity => ({
      sessionId: activity.id,
      activityType: this.mapActivityType(activity.type),
      startTime: new Date(activity.start_time),
      endTime: new Date(activity.end_time),
      recordedAt: new Date(activity.created_at),
      
      biometrics: {
        heartRate: {
          current: activity.heart_rate?.current || null,
          average: activity.heart_rate?.average || null,
          max: activity.heart_rate?.max || null,
          min: activity.heart_rate?.min || null,
          zones: activity.heart_rate?.zones || {}
        },
        hrv: {
          current: activity.hrv?.current || null,
          average: activity.hrv?.average || null,
          rmssd: activity.hrv?.rmssd || null,
          sdnn: activity.hrv?.sdnn || null
        },
        calories: {
          total: activity.calories?.total || 0,
          active: activity.calories?.active || 0,
          resting: activity.calories?.resting || 0
        },
        steps: activity.steps || 0,
        cadence: activity.cadence || null,
        strideLength: activity.stride_length || null
      },
      
      location: {
        coordinates: activity.gps_coordinates?.map(coord => ({
          latitude: coord.latitude,
          longitude: coord.longitude,
          altitude: coord.altitude,
          accuracy: coord.accuracy,
          timestamp: new Date(coord.timestamp)
        })) || [],
        totalDistance: activity.distance || 0,
        averageSpeed: activity.average_speed || 0,
        maxSpeed: activity.max_speed || 0,
        elevationGain: activity.elevation_gain || 0,
        elevationLoss: activity.elevation_loss || 0
      },
      
      performance: {
        load: activity.load || 0,
        intensity: activity.intensity || 0,
        impact: activity.impact || 0,
        sprints: activity.sprints || 0,
        accelerations: activity.accelerations || 0,
        decelerations: activity.decelerations || 0
      }
    }));
  }

  // Normalize Apple HealthKit data to common schema
  normalizeAppleData(rawData) {
    return rawData.activities.map(activity => ({
      sessionId: activity.uuid,
      activityType: this.mapActivityType(activity.workoutActivityType),
      startTime: new Date(activity.startDate),
      endTime: new Date(activity.endDate),
      recordedAt: new Date(activity.creationDate),
      
      biometrics: {
        heartRate: {
          current: activity.heartRate?.current || null,
          average: activity.heartRate?.average || null,
          max: activity.heartRate?.max || null,
          min: activity.heartRate?.min || null,
          zones: activity.heartRate?.zones || {}
        },
        hrv: {
          current: activity.heartRateVariability?.current || null,
          average: activity.heartRateVariability?.average || null,
          rmssd: activity.heartRateVariability?.rmssd || null,
          sdnn: activity.heartRateVariability?.sdnn || null
        },
        calories: {
          total: activity.activeEnergyBurned || 0,
          active: activity.activeEnergyBurned || 0,
          resting: activity.basalEnergyBurned || 0
        },
        steps: activity.stepCount || 0,
        cadence: activity.cadence || null,
        strideLength: activity.strideLength || null
      },
      
      location: {
        coordinates: activity.locations?.map(loc => ({
          latitude: loc.latitude,
          longitude: loc.longitude,
          altitude: loc.altitude,
          accuracy: loc.accuracy,
          timestamp: new Date(loc.timestamp)
        })) || [],
        totalDistance: activity.totalDistance || 0,
        averageSpeed: activity.averageSpeed || 0,
        maxSpeed: activity.maxSpeed || 0,
        elevationGain: activity.elevationGain || 0,
        elevationLoss: activity.elevationLoss || 0
      },
      
      performance: {
        load: this.calculateLoad(activity),
        intensity: activity.intensity || 0,
        impact: 0, // Apple doesn't provide impact data
        sprints: activity.sprints || 0,
        accelerations: activity.accelerations || 0,
        decelerations: activity.decelerations || 0
      }
    }));
  }

  // Normalize Garmin data to common schema
  normalizeGarminData(rawData) {
    return rawData.activities.map(activity => ({
      sessionId: activity.activityId,
      activityType: this.mapActivityType(activity.activityType),
      startTime: new Date(activity.startTime),
      endTime: new Date(activity.endTime),
      recordedAt: new Date(activity.uploadDate),
      
      biometrics: {
        heartRate: {
          current: activity.heartRate?.current || null,
          average: activity.heartRate?.average || null,
          max: activity.heartRate?.max || null,
          min: activity.heartRate?.min || null,
          zones: activity.heartRate?.zones || {}
        },
        hrv: {
          current: activity.hrv?.current || null,
          average: activity.hrv?.average || null,
          rmssd: activity.hrv?.rmssd || null,
          sdnn: activity.hrv?.sdnn || null
        },
        calories: {
          total: activity.calories || 0,
          active: activity.activeCalories || 0,
          resting: activity.restingCalories || 0
        },
        steps: activity.steps || 0,
        cadence: activity.cadence || null,
        strideLength: activity.strideLength || null
      },
      
      location: {
        coordinates: activity.gpsCoordinates?.map(coord => ({
          latitude: coord.latitude,
          longitude: coord.longitude,
          altitude: coord.altitude,
          accuracy: coord.accuracy,
          timestamp: new Date(coord.timestamp)
        })) || [],
        totalDistance: activity.distance || 0,
        averageSpeed: activity.averageSpeed || 0,
        maxSpeed: activity.maxSpeed || 0,
        elevationGain: activity.elevationGain || 0,
        elevationLoss: activity.elevationLoss || 0
      },
      
      performance: {
        load: activity.load || 0,
        intensity: activity.intensity || 0,
        impact: activity.impact || 0,
        sprints: activity.sprints || 0,
        accelerations: activity.accelerations || 0,
        decelerations: activity.decelerations || 0
      }
    }));
  }

  // Map activity types to common schema
  mapActivityType(serviceType) {
    const typeMapping = {
      // Catapult types
      'training': 'training',
      'match': 'match',
      'recovery': 'recovery',
      
      // Apple types
      'HKWorkoutActivityTypeRunning': 'training',
      'HKWorkoutActivityTypeSoccer': 'match',
      'HKWorkoutActivityTypeWalking': 'recovery',
      
      // Garmin types
      'running': 'training',
      'soccer': 'match',
      'walking': 'recovery'
    };

    return typeMapping[serviceType] || 'other';
  }

  // Calculate training load for Apple data
  calculateLoad(activity) {
    if (!activity.heartRate?.average || !activity.duration) return 0;
    
    const durationHours = activity.duration / (1000 * 60 * 60);
    const intensity = activity.heartRate.average / 200; // Assuming max HR of 200
    
    return durationHours * intensity * 100;
  }

  // Sync data for a specific player and device
  async syncPlayerData(playerId, deviceId, service, options = {}) {
    const syncKey = `${playerId}-${deviceId}-${service}`;
    
    if (this.syncInProgress.has(syncKey)) {
      throw new Error('Sync already in progress for this player and device');
    }

    this.syncInProgress.add(syncKey);

    try {
      const player = await Player.findById(playerId);
      if (!player) {
        throw new Error('Player not found');
      }

      // Determine sync period
      const endDate = new Date();
      const startDate = options.startDate || new Date(endDate.getTime() - (24 * 60 * 60 * 1000)); // Default: last 24 hours

      // Fetch data from service
      let rawData;
      switch (service) {
        case 'catapult':
          rawData = await this.fetchCatapultData(playerId, deviceId, startDate, endDate);
          break;
        case 'apple':
          rawData = await this.fetchAppleData(playerId, deviceId, startDate, endDate);
          break;
        case 'garmin':
          rawData = await this.fetchGarminData(playerId, deviceId, startDate, endDate);
          break;
        default:
          throw new Error(`Unsupported service: ${service}`);
      }

      // Store normalized data
      const savedData = [];
      for (const data of rawData) {
        const deviceData = new DeviceData({
          playerId,
          fifaConnectId: player.fifaConnectId,
          service,
          deviceId,
          deviceType: this.getDeviceType(service),
          ...data
        });

        await deviceData.save();
        savedData.push(deviceData);
      }

      // Update player stats
      await this.updatePlayerStats(playerId);

      // Update device last sync
      player.updateLastSync(service, deviceId);
      await player.save();

      logger.info(`Data sync completed for ${service} - Player: ${playerId}, Device: ${deviceId}, Records: ${savedData.length}`);

      return {
        success: true,
        recordsProcessed: savedData.length,
        startDate,
        endDate,
        service
      };

    } catch (error) {
      logger.error(`Data sync failed for ${service} - Player: ${playerId}, Device: ${deviceId}:`, error);
      throw error;
    } finally {
      this.syncInProgress.delete(syncKey);
    }
  }

  // Get device type for service
  getDeviceType(service) {
    const deviceTypes = {
      catapult: 'catapult_vector',
      apple: 'apple_watch',
      garmin: 'garmin_device'
    };
    return deviceTypes[service] || 'unknown';
  }

  // Update player statistics
  async updatePlayerStats(playerId) {
    const player = await Player.findById(playerId);
    if (!player) return;

    const stats = await DeviceData.getPlayerStats(playerId, 
      new Date(Date.now() - 30 * 24 * 60 * 60 * 1000), // Last 30 days
      new Date()
    );

    if (stats.length > 0) {
      const stat = stats[0];
      player.stats = {
        totalSessions: stat.totalSessions,
        totalDistance: stat.totalDistance,
        totalDuration: stat.totalDuration,
        averageHeartRate: Math.round(stat.averageHeartRate),
        lastActivity: new Date()
      };
      await player.save();
    }
  }

  // Sync all active players
  async syncAllPlayers() {
    const activePlayers = await Player.findActivePlayers();
    const results = [];

    for (const player of activePlayers) {
      const activeDevices = player.getActiveDevices();
      
      for (const device of activeDevices) {
        try {
          const result = await this.syncPlayerData(
            player._id,
            device.deviceId,
            device.service
          );
          results.push({
            playerId: player._id,
            deviceId: device.deviceId,
            service: device.service,
            ...result
          });
        } catch (error) {
          logger.error(`Failed to sync player ${player._id} device ${device.deviceId}:`, error);
          results.push({
            playerId: player._id,
            deviceId: device.deviceId,
            service: device.service,
            success: false,
            error: error.message
          });
        }
      }
    }

    return results;
  }

  // Get sync status for a player
  async getSyncStatus(playerId) {
    const player = await Player.findById(playerId);
    if (!player) {
      throw new Error('Player not found');
    }

    const activeDevices = player.getActiveDevices();
    const status = [];

    for (const device of activeDevices) {
      const lastData = await DeviceData.findOne({
        playerId,
        service: device.service,
        deviceId: device.deviceId
      }).sort({ startTime: -1 });

      status.push({
        service: device.service,
        deviceId: device.deviceId,
        deviceName: device.deviceName,
        isActive: device.isActive,
        lastSync: device.lastSync,
        lastDataRecord: lastData?.startTime,
        syncFrequency: device.syncFrequency
      });
    }

    return status;
  }

  // Get sync history
  async getSyncHistory(playerId, options = {}) {
    const query = { playerId };
    
    if (options.startDate) {
      query.startTime = { $gte: new Date(options.startDate) };
    }
    
    if (options.endDate) {
      query.startTime = { ...query.startTime, $lte: new Date(options.endDate) };
    }
    
    if (options.service) {
      query.service = options.service;
    }

    const history = await DeviceData.find(query)
      .select('service deviceId startTime endTime activityType location.totalDistance biometrics.calories.total')
      .sort({ startTime: -1 })
      .limit(options.limit || 50);

    return history;
  }
}

module.exports = DataSyncService; 