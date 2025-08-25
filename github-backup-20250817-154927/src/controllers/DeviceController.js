const DataSyncService = require('../services/DataSyncService');
const { Player } = require('../models/Player');
const { DeviceData } = require('../models/DeviceData');
const { validationResult } = require('express-validator');
const logger = require('../utils/logger');

class DeviceController {
  // List compatible connected devices
  async listDevices(req, res) {
    try {
      const { playerId } = req.params;
      const { service } = req.query;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      let devices = player.connectedDevices;

      // Filter by service if specified
      if (service) {
        devices = devices.filter(device => device.service === service);
      }

      // Add device compatibility info
      const devicesWithInfo = devices.map(device => ({
        ...device.toObject(),
        compatibility: this.getDeviceCompatibility(device.service, device.deviceType),
        lastDataSync: device.lastSync,
        syncStatus: this.getSyncStatus(device.lastSync)
      }));

      res.json({
        success: true,
        data: {
          player: {
            id: player._id,
            fifaConnectId: player.fifaConnectId,
            name: player.fullName
          },
          devices: devicesWithInfo,
          total: devicesWithInfo.length
        }
      });

    } catch (error) {
      logger.error('Failed to list devices:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve devices',
        details: error.message
      });
    }
  }

  // Associate device with player
  async associateDevice(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { playerId } = req.params;
      const { service, deviceId, deviceName, deviceType, syncFrequency } = req.body;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      // Check if device is already associated
      const existingDevice = player.connectedDevices.find(
        device => device.service === service && device.deviceId === deviceId
      );

      if (existingDevice) {
        return res.status(409).json({
          success: false,
          error: 'Device already associated with this player'
        });
      }

      // Add device to player
      player.addDevice({
        service,
        deviceId,
        deviceName,
        deviceType,
        syncFrequency: syncFrequency || 'standard'
      });

      await player.save();

      logger.info(`Device associated - Player: ${playerId}, Service: ${service}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'Device associated successfully',
        data: {
          playerId,
          service,
          deviceId,
          deviceName,
          deviceType
        }
      });

    } catch (error) {
      logger.error('Failed to associate device:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to associate device',
        details: error.message
      });
    }
  }

  // Disassociate device from player
  async disassociateDevice(req, res) {
    try {
      const { playerId, service, deviceId } = req.params;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      // Remove device from player
      player.removeDevice(service, deviceId);
      await player.save();

      logger.info(`Device disassociated - Player: ${playerId}, Service: ${service}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'Device disassociated successfully',
        data: {
          playerId,
          service,
          deviceId
        }
      });

    } catch (error) {
      logger.error('Failed to disassociate device:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to disassociate device',
        details: error.message
      });
    }
  }

  // Update device status
  async updateDeviceStatus(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { playerId } = req.params;
      const { service, deviceId, isActive, syncFrequency } = req.body;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      const device = player.connectedDevices.find(
        d => d.service === service && d.deviceId === deviceId
      );

      if (!device) {
        return res.status(404).json({
          success: false,
          error: 'Device not found'
        });
      }

      // Update device status
      if (typeof isActive === 'boolean') {
        device.isActive = isActive;
      }

      if (syncFrequency) {
        device.syncFrequency = syncFrequency;
      }

      await player.save();

      logger.info(`Device status updated - Player: ${playerId}, Service: ${service}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'Device status updated successfully',
        data: {
          playerId,
          service,
          deviceId,
          isActive: device.isActive,
          syncFrequency: device.syncFrequency
        }
      });

    } catch (error) {
      logger.error('Failed to update device status:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to update device status',
        details: error.message
      });
    }
  }

  // Trigger manual synchronization
  async triggerSync(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { playerId } = req.params;
      const { service, deviceId, startDate, endDate } = req.body;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      // Validate device exists and is active
      const device = player.connectedDevices.find(
        d => d.service === service && d.deviceId === deviceId
      );

      if (!device) {
        return res.status(404).json({
          success: false,
          error: 'Device not found'
        });
      }

      if (!device.isActive) {
        return res.status(400).json({
          success: false,
          error: 'Device is not active'
        });
      }

      // Trigger sync
      const syncOptions = {};
      if (startDate) syncOptions.startDate = new Date(startDate);
      if (endDate) syncOptions.endDate = new Date(endDate);

      const result = await DataSyncService.syncPlayerData(
        playerId,
        deviceId,
        service,
        syncOptions
      );

      logger.info(`Manual sync triggered - Player: ${playerId}, Service: ${service}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'Manual synchronization triggered successfully',
        data: result
      });

    } catch (error) {
      logger.error('Failed to trigger manual sync:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to trigger manual synchronization',
        details: error.message
      });
    }
  }

  // Get synchronization history
  async getSyncHistory(req, res) {
    try {
      const { playerId } = req.params;
      const { service, startDate, endDate, limit } = req.query;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      const options = {};
      if (service) options.service = service;
      if (startDate) options.startDate = startDate;
      if (endDate) options.endDate = endDate;
      if (limit) options.limit = parseInt(limit);

      const history = await DataSyncService.getSyncHistory(playerId, options);

      res.json({
        success: true,
        data: {
          player: {
            id: player._id,
            fifaConnectId: player.fifaConnectId,
            name: player.fullName
          },
          history,
          total: history.length
        }
      });

    } catch (error) {
      logger.error('Failed to get sync history:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve synchronization history',
        details: error.message
      });
    }
  }

  // Get player data
  async getPlayerData(req, res) {
    try {
      const { playerId } = req.params;
      const { startDate, endDate, activityType, limit } = req.query;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      const options = {};
      if (startDate) options.startDate = startDate;
      if (endDate) options.endDate = endDate;
      if (activityType) options.activityType = activityType;
      if (limit) options.limit = parseInt(limit);

      const data = await DeviceData.findByPlayer(playerId, options);

      res.json({
        success: true,
        data: {
          player: {
            id: player._id,
            fifaConnectId: player.fifaConnectId,
            name: player.fullName,
            stats: player.stats
          },
          records: data,
          total: data.length
        }
      });

    } catch (error) {
      logger.error('Failed to get player data:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve player data',
        details: error.message
      });
    }
  }

  // Get sync status for player
  async getSyncStatus(req, res) {
    try {
      const { playerId } = req.params;

      const player = await Player.findById(playerId);
      if (!player) {
        return res.status(404).json({
          success: false,
          error: 'Player not found'
        });
      }

      const status = await DataSyncService.getSyncStatus(playerId);

      res.json({
        success: true,
        data: {
          player: {
            id: player._id,
            fifaConnectId: player.fifaConnectId,
            name: player.fullName
          },
          syncStatus: status
        }
      });

    } catch (error) {
      logger.error('Failed to get sync status:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve synchronization status',
        details: error.message
      });
    }
  }

  // Get device compatibility information
  getDeviceCompatibility(service, deviceType) {
    const compatibility = {
      catapult: {
        catapult_vector: {
          supported: true,
          features: ['GPS', 'Heart Rate', 'Acceleration', 'Biometrics'],
          dataQuality: 'High',
          syncFrequency: 'Real-time'
        }
      },
      apple: {
        apple_watch: {
          supported: true,
          features: ['Heart Rate', 'GPS', 'Activity', 'Health Metrics'],
          dataQuality: 'High',
          syncFrequency: 'Near real-time'
        }
      },
      garmin: {
        garmin_device: {
          supported: true,
          features: ['GPS', 'Heart Rate', 'Activity', 'Performance'],
          dataQuality: 'High',
          syncFrequency: 'Standard'
        }
      }
    };

    return compatibility[service]?.[deviceType] || {
      supported: false,
      features: [],
      dataQuality: 'Unknown',
      syncFrequency: 'Unknown'
    };
  }

  // Get sync status based on last sync time
  getSyncStatus(lastSync) {
    if (!lastSync) return 'never';
    
    const now = new Date();
    const timeDiff = now.getTime() - lastSync.getTime();
    const hoursDiff = timeDiff / (1000 * 60 * 60);

    if (hoursDiff < 1) return 'recent';
    if (hoursDiff < 24) return 'today';
    if (hoursDiff < 168) return 'this_week'; // 7 days
    return 'old';
  }

  // Get device statistics
  async getDeviceStats(req, res) {
    try {
      const { Player } = require('../models/Player');
      const { DeviceData } = require('../models/DeviceData');

      // Total devices by service
      const deviceStats = await Player.aggregate([
        { $unwind: '$connectedDevices' },
        { $group: { _id: '$connectedDevices.service', count: { $sum: 1 } } }
      ]);

      // Active devices
      const activeDevices = await Player.aggregate([
        { $unwind: '$connectedDevices' },
        { $match: { 'connectedDevices.isActive': true } },
        { $group: { _id: '$connectedDevices.service', count: { $sum: 1 } } }
      ]);

      // Data records by service
      const dataStats = await DeviceData.aggregate([
        { $group: { _id: '$service', count: { $sum: 1 } } }
      ]);

      // Recent sync activity (last 24 hours)
      const recentSyncs = await DeviceData.countDocuments({
        startTime: { $gte: new Date(Date.now() - 24 * 60 * 60 * 1000) }
      });

      const stats = {
        totalDevices: deviceStats.reduce((acc, item) => {
          acc[item._id] = item.count;
          return acc;
        }, {}),
        activeDevices: activeDevices.reduce((acc, item) => {
          acc[item._id] = item.count;
          return acc;
        }, {}),
        dataRecords: dataStats.reduce((acc, item) => {
          acc[item._id] = item.count;
          return acc;
        }, {}),
        recentSyncs,
        lastUpdated: new Date()
      };

      res.json({
        success: true,
        data: stats
      });

    } catch (error) {
      logger.error('Failed to get device stats:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve device statistics',
        details: error.message
      });
    }
  }
}

module.exports = new DeviceController(); 