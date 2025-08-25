const OAuth2Service = require('../services/OAuth2Service');
const { validationResult } = require('express-validator');
const logger = require('../utils/logger');

class OAuth2Controller {
  // Generate OAuth2 authorization URL
  async generateAuthUrl(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { service, playerId, deviceId } = req.body;

      const authUrl = OAuth2Service.generateAuthUrl(service, playerId, deviceId);

      logger.info(`OAuth2 auth URL generated for ${service} - Player: ${playerId}, Device: ${deviceId}`);

      res.json({
        success: true,
        data: {
          authUrl,
          service,
          playerId,
          deviceId,
          expiresIn: 600 // 10 minutes
        }
      });

    } catch (error) {
      logger.error('OAuth2 auth URL generation failed:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to generate OAuth2 authorization URL',
        details: error.message
      });
    }
  }

  // Handle OAuth2 callback
  async handleCallback(req, res) {
    try {
      const { service } = req.params;
      const { code, state, error } = req.query;

      if (error) {
        logger.error(`OAuth2 callback error for ${service}:`, error);
        return res.status(400).json({
          success: false,
          error: 'OAuth2 authorization failed',
          details: error
        });
      }

      if (!code || !state) {
        return res.status(400).json({
          success: false,
          error: 'Missing authorization code or state'
        });
      }

      // Validate state and get state data
      const stateData = await OAuth2Service.validateAndGetState(state);

      // Exchange code for tokens
      const result = await OAuth2Service.exchangeCodeForTokens(service, code, stateData);

      logger.info(`OAuth2 callback successful for ${service} - Player: ${stateData.playerId}`);

      // Redirect to success page or return JSON
      if (req.headers.accept && req.headers.accept.includes('application/json')) {
        res.json({
          success: true,
          message: 'OAuth2 authorization successful',
          data: result
        });
      } else {
        // Redirect to success page
        res.redirect(`/oauth2/success?service=${service}&playerId=${stateData.playerId}`);
      }

    } catch (error) {
      logger.error('OAuth2 callback handling failed:', error);
      
      if (req.headers.accept && req.headers.accept.includes('application/json')) {
        res.status(500).json({
          success: false,
          error: 'OAuth2 callback processing failed',
          details: error.message
        });
      } else {
        // Redirect to error page
        res.redirect(`/oauth2/error?error=${encodeURIComponent(error.message)}`);
      }
    }
  }

  // Get OAuth2 tokens for a player
  async getTokens(req, res) {
    try {
      const { playerId } = req.params;
      const { service, deviceId } = req.query;

      if (service && deviceId) {
        // Get specific token
        const token = await OAuth2Service.getValidAccessToken(service, playerId, deviceId);
        res.json({
          success: true,
          data: {
            service,
            deviceId,
            hasValidToken: true
          }
        });
      } else {
        // Get all tokens for player
        const tokens = await OAuth2Service.getTokenStatus(playerId);
        res.json({
          success: true,
          data: tokens
        });
      }

    } catch (error) {
      logger.error('Failed to get OAuth2 tokens:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve OAuth2 tokens',
        details: error.message
      });
    }
  }

  // Refresh OAuth2 tokens
  async refreshTokens(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { service, playerId, deviceId } = req.body;

      const result = await OAuth2Service.refreshTokens(service, playerId, deviceId);

      logger.info(`OAuth2 tokens refreshed for ${service} - Player: ${playerId}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'OAuth2 tokens refreshed successfully',
        data: result
      });

    } catch (error) {
      logger.error('OAuth2 token refresh failed:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to refresh OAuth2 tokens',
        details: error.message
      });
    }
  }

  // Revoke OAuth2 tokens
  async revokeTokens(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({
          success: false,
          errors: errors.array()
        });
      }

      const { service, playerId, deviceId } = req.body;

      const result = await OAuth2Service.revokeTokens(service, playerId, deviceId);

      logger.info(`OAuth2 tokens revoked for ${service} - Player: ${playerId}, Device: ${deviceId}`);

      res.json({
        success: true,
        message: 'OAuth2 tokens revoked successfully',
        data: result
      });

    } catch (error) {
      logger.error('OAuth2 token revocation failed:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to revoke OAuth2 tokens',
        details: error.message
      });
    }
  }

  // Validate OAuth2 tokens
  async validateTokens(req, res) {
    try {
      const { service, playerId, deviceId } = req.params;

      const result = await OAuth2Service.validateTokens(service, playerId, deviceId);

      res.json({
        success: true,
        data: result
      });

    } catch (error) {
      logger.error('OAuth2 token validation failed:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to validate OAuth2 tokens',
        details: error.message
      });
    }
  }

  // Get user info from OAuth2 service
  async getUserInfo(req, res) {
    try {
      const { service, playerId, deviceId } = req.params;

      const userInfo = await OAuth2Service.getUserInfo(service, playerId, deviceId);

      res.json({
        success: true,
        data: userInfo
      });

    } catch (error) {
      logger.error('Failed to get user info:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve user information',
        details: error.message
      });
    }
  }

  // Get connected devices from OAuth2 service
  async getConnectedDevices(req, res) {
    try {
      const { service, playerId, deviceId } = req.params;

      const devices = await OAuth2Service.getConnectedDevices(service, playerId, deviceId);

      res.json({
        success: true,
        data: devices
      });

    } catch (error) {
      logger.error('Failed to get connected devices:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve connected devices',
        details: error.message
      });
    }
  }

  // List configured OAuth2 services
  async listServices(req, res) {
    try {
      const services = [
        {
          name: 'Catapult Connect',
          key: 'catapult',
          description: 'Professional sports tracking and analytics',
          deviceTypes: ['Catapult Vector'],
          features: ['GPS tracking', 'Biometric monitoring', 'Performance analytics'],
          status: 'configured'
        },
        {
          name: 'Apple HealthKit',
          key: 'apple',
          description: 'Health and fitness data from Apple devices',
          deviceTypes: ['Apple Watch', 'iPhone'],
          features: ['Heart rate monitoring', 'Activity tracking', 'Health metrics'],
          status: 'configured'
        },
        {
          name: 'Garmin Connect',
          key: 'garmin',
          description: 'Fitness and sports tracking from Garmin devices',
          deviceTypes: ['Garmin watches', 'Garmin fitness trackers'],
          features: ['GPS tracking', 'Heart rate monitoring', 'Activity analysis'],
          status: 'configured'
        }
      ];

      res.json({
        success: true,
        data: services
      });

    } catch (error) {
      logger.error('Failed to list OAuth2 services:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve OAuth2 services',
        details: error.message
      });
    }
  }

  // Get OAuth2 connection statistics
  async getConnectionStats(req, res) {
    try {
      const { Player } = require('../models/Player');
      const { OAuthToken } = require('../models/OAuthToken');

      // Get total players with OAuth2 connections
      const totalPlayers = await Player.countDocuments({
        'connectedDevices.0': { $exists: true }
      });

      // Get active connections by service
      const activeConnections = await OAuthToken.aggregate([
        { $match: { status: 'active' } },
        { $group: { _id: '$service', count: { $sum: 1 } } }
      ]);

      // Get error connections
      const errorConnections = await OAuthToken.countDocuments({ status: 'error' });

      // Get expiring tokens (within 24 hours)
      const expiringTokens = await OAuthToken.countDocuments({
        status: 'active',
        expiresAt: { $lt: new Date(Date.now() + 24 * 60 * 60 * 1000) }
      });

      const stats = {
        totalPlayers,
        activeConnections: activeConnections.reduce((acc, item) => {
          acc[item._id] = item.count;
          return acc;
        }, {}),
        errorConnections,
        expiringTokens,
        lastUpdated: new Date()
      };

      res.json({
        success: true,
        data: stats
      });

    } catch (error) {
      logger.error('Failed to get OAuth2 connection stats:', error);
      res.status(500).json({
        success: false,
        error: 'Failed to retrieve OAuth2 connection statistics',
        details: error.message
      });
    }
  }
}

module.exports = new OAuth2Controller(); 