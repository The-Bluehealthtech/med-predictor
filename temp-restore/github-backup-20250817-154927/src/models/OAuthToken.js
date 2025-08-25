const mongoose = require('mongoose');
const crypto = require('crypto');
const { DataTypes } = require('sequelize');

// Mongoose Schema for OAuth Tokens
const oauthTokenSchema = new mongoose.Schema({
  // Player reference
  playerId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Player',
    required: true,
    index: true
  },
  fifaConnectId: {
    type: String,
    required: true,
    index: true
  },

  // Service information
  service: {
    type: String,
    enum: ['catapult', 'apple', 'garmin'],
    required: true
  },
  deviceId: {
    type: String,
    required: true
  },

  // Encrypted tokens
  accessToken: {
    type: String,
    required: true
  },
  refreshToken: {
    type: String,
    required: true
  },
  tokenType: {
    type: String,
    default: 'Bearer'
  },

  // Token metadata
  expiresAt: {
    type: Date,
    required: true
  },
  issuedAt: {
    type: Date,
    default: Date.now
  },
  scope: {
    type: String,
    required: true
  },

  // Status and usage
  status: {
    type: String,
    enum: ['active', 'expired', 'revoked', 'error'],
    default: 'active'
  },
  lastUsed: {
    type: Date,
    default: Date.now
  },
  usageCount: {
    type: Number,
    default: 0
  },

  // Error tracking
  lastError: {
    message: String,
    code: String,
    timestamp: Date
  },
  errorCount: {
    type: Number,
    default: 0
  },

  // Refresh tracking
  refreshCount: {
    type: Number,
    default: 0
  },
  lastRefresh: {
    type: Date
  },

  // Security
  encryptionKey: {
    type: String,
    required: true
  },
  encryptionIv: {
    type: String,
    required: true
  }
}, {
  timestamps: true,
  toJSON: { virtuals: true },
  toObject: { virtuals: true }
});

// Indexes
oauthTokenSchema.index({ playerId: 1, service: 1, deviceId: 1 }, { unique: true });
oauthTokenSchema.index({ status: 1, expiresAt: 1 });
oauthTokenSchema.index({ 'lastError.timestamp': -1 });

// Virtual for token expiry status
oauthTokenSchema.virtual('isExpired').get(function() {
  return new Date() > this.expiresAt;
});

oauthTokenSchema.virtual('isExpiringSoon').get(function() {
  const fiveMinutesFromNow = new Date(Date.now() + 5 * 60 * 1000);
  return this.expiresAt < fiveMinutesFromNow;
});

// Encryption methods
oauthTokenSchema.methods.encryptToken = function(token) {
  const algorithm = 'aes-256-gcm';
  const key = Buffer.from(this.encryptionKey, 'hex');
  const iv = Buffer.from(this.encryptionIv, 'hex');
  
  const cipher = crypto.createCipher(algorithm, key);
  cipher.setAAD(Buffer.from(this.fifaConnectId));
  
  let encrypted = cipher.update(token, 'utf8', 'hex');
  encrypted += cipher.final('hex');
  
  return encrypted + ':' + cipher.getAuthTag().toString('hex');
};

oauthTokenSchema.methods.decryptToken = function(encryptedToken) {
  try {
    const algorithm = 'aes-256-gcm';
    const key = Buffer.from(this.encryptionKey, 'hex');
    const iv = Buffer.from(this.encryptionIv, 'hex');
    
    const [encrypted, authTag] = encryptedToken.split(':');
    const decipher = crypto.createDecipher(algorithm, key);
    decipher.setAAD(Buffer.from(this.fifaConnectId));
    decipher.setAuthTag(Buffer.from(authTag, 'hex'));
    
    let decrypted = decipher.update(encrypted, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    
    return decrypted;
  } catch (error) {
    throw new Error('Failed to decrypt token');
  }
};

// Instance methods
oauthTokenSchema.methods.getAccessToken = function() {
  return this.decryptToken(this.accessToken);
};

oauthTokenSchema.methods.getRefreshToken = function() {
  return this.decryptToken(this.refreshToken);
};

oauthTokenSchema.methods.updateTokens = function(accessToken, refreshToken, expiresIn) {
  this.accessToken = this.encryptToken(accessToken);
  this.refreshToken = this.encryptToken(refreshToken);
  this.expiresAt = new Date(Date.now() + expiresIn * 1000);
  this.lastRefresh = new Date();
  this.refreshCount += 1;
  this.status = 'active';
  this.lastError = null;
  this.errorCount = 0;
};

oauthTokenSchema.methods.markAsUsed = function() {
  this.lastUsed = new Date();
  this.usageCount += 1;
};

oauthTokenSchema.methods.recordError = function(error) {
  this.lastError = {
    message: error.message,
    code: error.code || 'UNKNOWN',
    timestamp: new Date()
  };
  this.errorCount += 1;
  
  if (this.errorCount >= 5) {
    this.status = 'error';
  }
};

oauthTokenSchema.methods.revoke = function() {
  this.status = 'revoked';
  this.accessToken = null;
  this.refreshToken = null;
};

// Static methods
oauthTokenSchema.statics.findByPlayerAndService = function(playerId, service, deviceId) {
  return this.findOne({ playerId, service, deviceId });
};

oauthTokenSchema.statics.findActiveTokens = function() {
  return this.find({ 
    status: 'active', 
    expiresAt: { $gt: new Date() } 
  });
};

oauthTokenSchema.statics.findExpiringTokens = function() {
  const fiveMinutesFromNow = new Date(Date.now() + 5 * 60 * 1000);
  return this.find({
    status: 'active',
    expiresAt: { $lt: fiveMinutesFromNow }
  });
};

oauthTokenSchema.statics.findErrorTokens = function() {
  return this.find({ status: 'error' });
};

// Pre-save middleware to generate encryption keys if not present
oauthTokenSchema.pre('save', function(next) {
  if (!this.encryptionKey || !this.encryptionIv) {
    this.encryptionKey = crypto.randomBytes(32).toString('hex');
    this.encryptionIv = crypto.randomBytes(16).toString('hex');
  }
  next();
});

// Create Mongoose model
const OAuthToken = mongoose.model('OAuthToken', oauthTokenSchema);

// Sequelize model for PostgreSQL - chargement différé
let OAuthTokenSequelize = null;

const getOAuthTokenSequelize = () => {
  if (!OAuthTokenSequelize) {
    const sequelize = require('../config/database').sequelize;
    if (!sequelize) {
      throw new Error('Sequelize connection not available');
    }
    
    const { DataTypes } = require('sequelize');
    
    OAuthTokenSequelize = sequelize.define('OAuthToken', {
      id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true
      },
      playerId: {
        type: DataTypes.UUID,
        allowNull: false,
        references: {
          model: 'players',
          key: 'id'
        }
      },
      fifaConnectId: {
        type: DataTypes.UUID,
        allowNull: false
      },
      service: {
        type: DataTypes.ENUM('catapult', 'apple', 'garmin'),
        allowNull: false
      },
      deviceId: {
        type: DataTypes.STRING,
        allowNull: false
      },
      accessToken: {
        type: DataTypes.TEXT,
        allowNull: false
      },
      refreshToken: {
        type: DataTypes.TEXT,
        allowNull: false
      },
      tokenType: {
        type: DataTypes.STRING,
        defaultValue: 'Bearer'
      },
      expiresAt: {
        type: DataTypes.DATE,
        allowNull: false
      },
      issuedAt: {
        type: DataTypes.DATE,
        defaultValue: DataTypes.NOW
      },
      scope: {
        type: DataTypes.STRING,
        allowNull: false
      },
      status: {
        type: DataTypes.ENUM('active', 'expired', 'revoked', 'error'),
        defaultValue: 'active'
      },
      lastUsed: {
        type: DataTypes.DATE,
        defaultValue: DataTypes.NOW
      },
      usageCount: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      lastError: {
        type: DataTypes.JSONB
      },
      errorCount: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      refreshCount: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      lastRefresh: {
        type: DataTypes.DATE
      },
      encryptionKey: {
        type: DataTypes.STRING,
        allowNull: false
      },
      encryptionIv: {
        type: DataTypes.STRING,
        allowNull: false
      }
    }, {
      tableName: 'oauth_tokens',
      timestamps: true,
      indexes: [
        {
          unique: true,
          fields: ['playerId', 'service', 'deviceId']
        },
        {
          fields: ['status', 'expiresAt']
        },
        {
          fields: ['fifaConnectId']
        }
      ]
    });

    // Add hooks for Sequelize model
    OAuthTokenSequelize.beforeCreate((token, options) => {
      if (!token.encryptionKey || !token.encryptionIv) {
        token.encryptionKey = crypto.randomBytes(32).toString('hex');
        token.encryptionIv = crypto.randomBytes(16).toString('hex');
      }
    });
  }
  
  return OAuthTokenSequelize;
};

module.exports = {
  OAuthToken, // Mongoose model
  getOAuthTokenSequelize // Function to get Sequelize model
}; 