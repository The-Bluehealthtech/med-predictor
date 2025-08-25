const jwt = require('jsonwebtoken');
const speakeasy = require('speakeasy');
const rateLimit = require('express-rate-limit');
const { OAuthToken } = require('../models/OAuthToken');
const logger = require('../utils/logger');

// JWT configuration
const JWT_SECRET = process.env.JWT_SECRET || 'your-super-secret-jwt-key';
const JWT_REFRESH_SECRET = process.env.JWT_REFRESH_SECRET || 'your-super-secret-refresh-key';
const JWT_EXPIRES_IN = process.env.JWT_EXPIRES_IN || '1h';
const JWT_REFRESH_EXPIRES_IN = process.env.JWT_REFRESH_EXPIRES_IN || '7d';

// Role definitions
const ROLES = {
  ADMIN: 'admin',
  MANAGER: 'manager',
  COACH: 'coach',
  PLAYER: 'player',
  VIEWER: 'viewer'
};

const PERMISSIONS = {
  // OAuth2 permissions
  OAUTH2_MANAGE: 'oauth2:manage',
  OAUTH2_VIEW: 'oauth2:view',
  
  // Device permissions
  DEVICE_MANAGE: 'device:manage',
  DEVICE_VIEW: 'device:view',
  DEVICE_SYNC: 'device:sync',
  
  // Data permissions
  DATA_VIEW: 'data:view',
  DATA_EXPORT: 'data:export',
  DATA_DELETE: 'data:delete',
  
  // User permissions
  USER_MANAGE: 'user:manage',
  USER_VIEW: 'user:view'
};

// Role permissions mapping
const ROLE_PERMISSIONS = {
  [ROLES.ADMIN]: Object.values(PERMISSIONS),
  [ROLES.MANAGER]: [
    PERMISSIONS.OAUTH2_VIEW,
    PERMISSIONS.DEVICE_MANAGE,
    PERMISSIONS.DEVICE_VIEW,
    PERMISSIONS.DEVICE_SYNC,
    PERMISSIONS.DATA_VIEW,
    PERMISSIONS.DATA_EXPORT,
    PERMISSIONS.USER_VIEW
  ],
  [ROLES.COACH]: [
    PERMISSIONS.OAUTH2_VIEW,
    PERMISSIONS.DEVICE_VIEW,
    PERMISSIONS.DEVICE_SYNC,
    PERMISSIONS.DATA_VIEW
  ],
  [ROLES.PLAYER]: [
    PERMISSIONS.DEVICE_VIEW,
    PERMISSIONS.DATA_VIEW
  ],
  [ROLES.VIEWER]: [
    PERMISSIONS.DEVICE_VIEW,
    PERMISSIONS.DATA_VIEW
  ]
};

class AuthMiddleware {
  // Generate JWT token
  generateToken(payload) {
    return jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES_IN });
  }

  // Generate refresh token
  generateRefreshToken(payload) {
    return jwt.sign(payload, JWT_REFRESH_SECRET, { expiresIn: JWT_REFRESH_EXPIRES_IN });
  }

  // Verify JWT token
  verifyToken(token) {
    try {
      return jwt.verify(token, JWT_SECRET);
    } catch (error) {
      throw new Error('Invalid token');
    }
  }

  // Verify refresh token
  verifyRefreshToken(token) {
    try {
      return jwt.verify(token, JWT_REFRESH_SECRET);
    } catch (error) {
      throw new Error('Invalid refresh token');
    }
  }

  // Main authentication middleware
  authenticate(req, res, next) {
    try {
      const authHeader = req.headers.authorization;
      
      if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({
          success: false,
          error: 'Authorization header required'
        });
      }

      const token = authHeader.substring(7);
      const decoded = this.verifyToken(token);

      // Add user info to request
      req.user = {
        id: decoded.id,
        email: decoded.email,
        role: decoded.role,
        permissions: ROLE_PERMISSIONS[decoded.role] || []
      };

      logger.info('User authenticated', {
        userId: req.user.id,
        role: req.user.role
      });

      next();
    } catch (error) {
      logger.error('Authentication failed:', error);
      return res.status(401).json({
        success: false,
        error: 'Authentication failed'
      });
    }
  }

  // Role-based access control
  requireRole(roles) {
    return (req, res, next) => {
      if (!req.user) {
        return res.status(401).json({
          success: false,
          error: 'Authentication required'
        });
      }

      const userRole = req.user.role;
      const allowedRoles = Array.isArray(roles) ? roles : [roles];

      if (!allowedRoles.includes(userRole)) {
        logger.warn('Access denied - insufficient role', {
          userId: req.user.id,
          userRole,
          requiredRoles: allowedRoles
        });

        return res.status(403).json({
          success: false,
          error: 'Insufficient permissions'
        });
      }

      next();
    };
  }

  // Permission-based access control
  requirePermission(permissions) {
    return (req, res, next) => {
      if (!req.user) {
        return res.status(401).json({
          success: false,
          error: 'Authentication required'
        });
      }

      const userPermissions = req.user.permissions || [];
      const requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];

      const hasAllPermissions = requiredPermissions.every(permission => 
        userPermissions.includes(permission)
      );

      if (!hasAllPermissions) {
        logger.warn('Access denied - insufficient permissions', {
          userId: req.user.id,
          userPermissions,
          requiredPermissions
        });

        return res.status(403).json({
          success: false,
          error: 'Insufficient permissions'
        });
      }

      next();
    };
  }

  // 2FA verification middleware
  require2FA(req, res, next) {
    return (req, res, next) => {
      if (!req.user) {
        return res.status(401).json({
          success: false,
          error: 'Authentication required'
        });
      }

      const { twoFactorToken } = req.body;

      if (!twoFactorToken) {
        return res.status(400).json({
          success: false,
          error: '2FA token required'
        });
      }

      // Verify 2FA token (this would typically check against stored secret)
      try {
        // For demo purposes, we'll use a simple verification
        // In production, you'd verify against the user's stored 2FA secret
        const isValid = this.verify2FAToken(req.user.id, twoFactorToken);
        
        if (!isValid) {
          return res.status(401).json({
            success: false,
            error: 'Invalid 2FA token'
          });
        }

        next();
      } catch (error) {
        logger.error('2FA verification failed:', error);
        return res.status(401).json({
          success: false,
          error: '2FA verification failed'
        });
      }
    };
  }

  // Verify 2FA token
  verify2FAToken(userId, token) {
    // In production, you'd retrieve the user's 2FA secret from database
    // and verify using speakeasy.totp.verify()
    const secret = process.env.TWO_FACTOR_SECRET || 'your-2fa-secret';
    
    return speakeasy.totp.verify({
      secret: secret,
      encoding: 'base32',
      token: token,
      window: 2 // Allow 2 time steps in case of clock skew
    });
  }

  // Generate 2FA secret and QR code
  generate2FASecret(userId, userEmail) {
    const secret = speakeasy.generateSecret({
      name: `FIT Platform (${userEmail})`,
      issuer: 'FIT Platform'
    });

    return {
      secret: secret.base32,
      qrCode: secret.otpauth_url
    };
  }

  // Rate limiting per user
  rateLimitPerUser(options = {}) {
    const {
      windowMs = 15 * 60 * 1000, // 15 minutes
      max = 100, // limit each user to 100 requests per windowMs
      message = 'Too many requests from this user'
    } = options;

    return rateLimit({
      windowMs,
      max,
      keyGenerator: (req) => {
        return req.user ? req.user.id : req.ip;
      },
      message: {
        success: false,
        error: message
      }
    });
  }

  // Validate sensitive data
  validateSensitiveData(req, res, next) {
    const sensitiveFields = ['password', 'token', 'secret', 'key'];
    const body = req.body;
    
    for (const field of sensitiveFields) {
      if (body[field] && typeof body[field] === 'string' && body[field].length < 8) {
        return res.status(400).json({
          success: false,
          error: `Invalid ${field}: must be at least 8 characters long`
        });
      }
    }

    next();
  }

  // Log request for audit
  auditLog(req, res, next) {
    const startTime = Date.now();
    
    res.on('finish', () => {
      const duration = Date.now() - startTime;
      
      logger.info('API Request', {
        method: req.method,
        url: req.url,
        userId: req.user?.id,
        userRole: req.user?.role,
        statusCode: res.statusCode,
        duration,
        userAgent: req.get('User-Agent'),
        ip: req.ip
      });
    });

    next();
  }

  // Check if user can access specific player data
  canAccessPlayerData(playerId) {
    return (req, res, next) => {
      if (!req.user) {
        return res.status(401).json({
          success: false,
          error: 'Authentication required'
        });
      }

      // Admins and managers can access all player data
      if (req.user.role === ROLES.ADMIN || req.user.role === ROLES.MANAGER) {
        return next();
      }

      // Players can only access their own data
      if (req.user.role === ROLES.PLAYER && req.user.id === playerId) {
        return next();
      }

      // Coaches can access data of players they coach (would need additional logic)
      if (req.user.role === ROLES.COACH) {
        // In production, you'd check if the coach is assigned to this player
        return next();
      }

      return res.status(403).json({
        success: false,
        error: 'Access denied to player data'
      });
    };
  }

  // Validate OAuth2 token ownership
  validateOAuth2Ownership(req, res, next) {
    return async (req, res, next) => {
      try {
        const { playerId, service } = req.params;
        
        if (!req.user) {
          return res.status(401).json({
            success: false,
            error: 'Authentication required'
          });
        }

        // Admins can access all OAuth2 tokens
        if (req.user.role === ROLES.ADMIN) {
          return next();
        }

        // Check if user can access this player's data
        const canAccess = await this.canAccessPlayerData(playerId)(req, res, () => {});
        
        if (canAccess) {
          return next();
        }

        return res.status(403).json({
          success: false,
          error: 'Access denied to OAuth2 token'
        });
      } catch (error) {
        logger.error('OAuth2 ownership validation failed:', error);
        return res.status(500).json({
          success: false,
          error: 'Validation failed'
        });
      }
    };
  }
}

// Export middleware instance and utilities
module.exports = new AuthMiddleware();
module.exports.ROLES = ROLES;
module.exports.PERMISSIONS = PERMISSIONS;
module.exports.ROLE_PERMISSIONS = ROLE_PERMISSIONS; 