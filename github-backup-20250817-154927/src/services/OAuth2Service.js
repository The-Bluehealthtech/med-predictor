const axios = require('axios');
const { OAuthToken } = require('../models/OAuthToken');
const { getServiceConfig } = require('../config/oauth2');
const logger = require('../utils/logger');

class OAuth2Service {
  constructor() {
    this.stateStore = new Map(); // In production, use Redis
  }

  // Generate authorization URL for a service
  generateAuthUrl(service, playerId, redirectUri = null) {
    try {
      const config = getServiceConfig(service);
      const state = this.generateState(playerId, service);
      
      const params = new URLSearchParams({
        client_id: config.clientId,
        redirect_uri: redirectUri || config.callbackURL,
        response_type: 'code',
        scope: config.scope,
        state: state
      });

      const authUrl = `${config.authorizationURL}?${params.toString()}`;
      
      logger.info(`Generated OAuth2 auth URL for ${service}`, {
        playerId,
        service,
        state
      });

      return {
        authUrl,
        state
      };
    } catch (error) {
      logger.error(`Failed to generate auth URL for ${service}:`, error);
      throw error;
    }
  }

  // Handle OAuth2 callback
  async handleCallback(service, code, state, playerId) {
    try {
      const config = getServiceConfig(service);
      
      // Validate state
      if (!this.validateState(state, playerId, service)) {
        throw new Error('Invalid OAuth2 state');
      }

      // Exchange code for tokens
      const tokenResponse = await this.exchangeCodeForTokens(service, code, config);
      
      // Store tokens
      const tokenData = {
        playerId,
        service,
        accessToken: tokenResponse.access_token,
        refreshToken: tokenResponse.refresh_token,
        tokenType: tokenResponse.token_type || 'Bearer',
        expiresAt: new Date(Date.now() + (tokenResponse.expires_in * 1000)),
        scope: tokenResponse.scope || config.scope
      };

      const oauthToken = new OAuthToken(tokenData);
      await oauthToken.save();

      // Clear state
      this.clearState(state);

      logger.info(`OAuth2 callback completed for ${service}`, {
        playerId,
        service,
        tokenId: oauthToken.id
      });

      return oauthToken;
    } catch (error) {
      logger.error(`OAuth2 callback failed for ${service}:`, error);
      throw error;
    }
  }

  // Exchange authorization code for tokens
  async exchangeCodeForTokens(service, code, config) {
    try {
      const tokenData = {
        client_id: config.clientId,
        client_secret: config.clientSecret,
        code: code,
        grant_type: 'authorization_code',
        redirect_uri: config.callbackURL
      };

      const response = await axios.post(config.tokenURL, tokenData, {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        timeout: 10000
      });

      return response.data;
    } catch (error) {
      logger.error(`Token exchange failed for ${service}:`, error.response?.data || error.message);
      throw new Error(`Failed to exchange code for tokens: ${error.message}`);
    }
  }

  // Refresh access token
  async refreshToken(service, playerId) {
    try {
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      
      if (!oauthToken) {
        throw new Error('No OAuth token found for this player and service');
      }

      const config = getServiceConfig(service);
      const refreshToken = oauthToken.getRefreshToken();

      const tokenData = {
        client_id: config.clientId,
        client_secret: config.clientSecret,
        refresh_token: refreshToken,
        grant_type: 'refresh_token'
      };

      const response = await axios.post(config.tokenURL, tokenData, {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        timeout: 10000
      });

      const newTokenData = response.data;
      
      // Update token
      await oauthToken.refresh(
        newTokenData.access_token,
        newTokenData.refresh_token || refreshToken,
        new Date(Date.now() + (newTokenData.expires_in * 1000))
      );

      logger.info(`Token refreshed for ${service}`, {
        playerId,
        service,
        tokenId: oauthToken.id
      });

      return oauthToken;
    } catch (error) {
      logger.error(`Token refresh failed for ${service}:`, error);
      
      // Mark token as error if refresh fails
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      if (oauthToken) {
        await oauthToken.addError(error);
      }
      
      throw error;
    }
  }

  // Revoke token
  async revokeToken(service, playerId) {
    try {
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      
      if (!oauthToken) {
        throw new Error('No OAuth token found for this player and service');
      }

      const config = getServiceConfig(service);
      const accessToken = oauthToken.getAccessToken();

      // Revoke token with service
      if (config.revokeURL) {
        await axios.post(config.revokeURL, {
          token: accessToken,
          client_id: config.clientId,
          client_secret: config.clientSecret
        }, {
          timeout: 10000
        });
      }

      // Mark as revoked in database
      await oauthToken.revoke();

      logger.info(`Token revoked for ${service}`, {
        playerId,
        service,
        tokenId: oauthToken.id
      });

      return true;
    } catch (error) {
      logger.error(`Token revocation failed for ${service}:`, error);
      throw error;
    }
  }

  // Validate token
  async validateToken(service, playerId) {
    try {
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      
      if (!oauthToken) {
        return { valid: false, reason: 'No token found' };
      }

      if (oauthToken.status !== 'active') {
        return { valid: false, reason: `Token status: ${oauthToken.status}` };
      }

      if (oauthToken.isExpired()) {
        return { valid: false, reason: 'Token expired' };
      }

      // Check if token needs refresh
      if (oauthToken.isExpiringSoon()) {
        try {
          await this.refreshToken(service, playerId);
        } catch (error) {
          return { valid: false, reason: 'Token refresh failed' };
        }
      }

      return { valid: true, token: oauthToken };
    } catch (error) {
      logger.error(`Token validation failed for ${service}:`, error);
      return { valid: false, reason: 'Validation error' };
    }
  }

  // Get user info from service
  async getUserInfo(service, playerId) {
    try {
      const validation = await this.validateToken(service, playerId);
      
      if (!validation.valid) {
        throw new Error(`Token validation failed: ${validation.reason}`);
      }

      const config = getServiceConfig(service);
      const accessToken = validation.token.getAccessToken();

      const response = await axios.get(config.userInfoURL, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'Content-Type': 'application/json'
        },
        timeout: 10000
      });

      // Update usage stats
      await validation.token.updateUsage(true);

      return response.data;
    } catch (error) {
      logger.error(`Failed to get user info for ${service}:`, error);
      
      // Update usage stats
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      if (oauthToken) {
        await oauthToken.updateUsage(false);
      }
      
      throw error;
    }
  }

  // Get devices from service
  async getDevices(service, playerId) {
    try {
      const validation = await this.validateToken(service, playerId);
      
      if (!validation.valid) {
        throw new Error(`Token validation failed: ${validation.reason}`);
      }

      const config = getServiceConfig(service);
      const accessToken = validation.token.getAccessToken();

      const response = await axios.get(config.devicesURL, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'Content-Type': 'application/json'
        },
        timeout: 10000
      });

      // Update usage stats
      await validation.token.updateUsage(true);

      return response.data;
    } catch (error) {
      logger.error(`Failed to get devices for ${service}:`, error);
      
      // Update usage stats
      const oauthToken = await OAuthToken.findByPlayerAndService(playerId, service);
      if (oauthToken) {
        await oauthToken.updateUsage(false);
      }
      
      throw error;
    }
  }

  // State management methods
  generateState(playerId, service) {
    const state = `${playerId}_${service}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    this.stateStore.set(state, { playerId, service, timestamp: Date.now() });
    return state;
  }

  validateState(state, playerId, service) {
    const stateData = this.stateStore.get(state);
    
    if (!stateData) {
      return false;
    }

    if (stateData.playerId !== playerId || stateData.service !== service) {
      return false;
    }

    // Check if state is not too old (15 minutes)
    const stateAge = Date.now() - stateData.timestamp;
    if (stateAge > 15 * 60 * 1000) {
      this.clearState(state);
      return false;
    }

    return true;
  }

  clearState(state) {
    this.stateStore.delete(state);
  }

  // Cleanup old states
  cleanupStates() {
    const now = Date.now();
    const maxAge = 15 * 60 * 1000; // 15 minutes

    for (const [state, data] of this.stateStore.entries()) {
      if (now - data.timestamp > maxAge) {
        this.stateStore.delete(state);
      }
    }
  }

  // Get token status for a player
  async getTokenStatus(playerId) {
    try {
      const tokens = await OAuthToken.find({ playerId });
      
      return tokens.map(token => ({
        service: token.service,
        status: token.status,
        expiresAt: token.expiresAt,
        isExpired: token.isExpired(),
        isExpiringSoon: token.isExpiringSoon(),
        usageStats: token.usageStats,
        lastError: token.errorHistory.length > 0 ? token.errorHistory[token.errorHistory.length - 1] : null
      }));
    } catch (error) {
      logger.error(`Failed to get token status for player ${playerId}:`, error);
      throw error;
    }
  }
}

module.exports = new OAuth2Service(); 