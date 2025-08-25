const mongoose = require('mongoose');
const crypto = require('crypto');
const { v4: uuidv4 } = require('uuid');

const deviceDataSchema = new mongoose.Schema({
  id: {
    type: String,
    default: () => uuidv4(),
    unique: true
  },
  playerId: {
    type: String,
    required: true,
    index: true
  },
  deviceId: {
    type: String,
    required: true,
    index: true
  },
  service: {
    type: String,
    enum: ['catapult', 'apple', 'garmin'],
    required: true
  },
  sessionId: {
    type: String,
    required: true,
    index: true
  },
  
  // Biometric data
  heartRate: {
    value: {
      type: Number,
      min: 0,
      max: 300
    },
    unit: {
      type: String,
      default: 'bpm'
    },
    timestamp: Date
  },
  hrv: {
    value: {
      type: Number,
      min: 0
    },
    unit: {
      type: String,
      default: 'ms'
    },
    timestamp: Date
  },
  
  // Activity data
  speed: {
    value: {
      type: Number,
      min: 0
    },
    unit: {
      type: String,
      default: 'm/s'
    },
    timestamp: Date
  },
  distance: {
    value: {
      type: Number,
      min: 0
    },
    unit: {
      type: String,
      default: 'm'
    },
    timestamp: Date
  },
  altitude: {
    value: {
      type: Number
    },
    unit: {
      type: String,
      default: 'm'
    },
    timestamp: Date
  },
  calories: {
    value: {
      type: Number,
      min: 0
    },
    unit: {
      type: String,
      default: 'kcal'
    },
    timestamp: Date
  },
  duration: {
    value: {
      type: Number,
      min: 0
    },
    unit: {
      type: String,
      default: 's'
    },
    timestamp: Date
  },
  
  // GPS data
  gps: {
    latitude: {
      type: Number,
      min: -90,
      max: 90
    },
    longitude: {
      type: Number,
      min: -180,
      max: 180
    },
    accuracy: {
      type: Number,
      min: 0
    },
    timestamp: Date
  },
  
  // Metadata
  dataQuality: {
    type: String,
    enum: ['high', 'medium', 'low'],
    default: 'medium'
  },
  sourceTimestamp: {
    type: Date,
    required: true
  },
  processedAt: {
    type: Date,
    default: Date.now
  },
  
  // Raw data (encrypted)
  rawData: {
    type: String,
    required: true
  },
  
  // Validation flags
  isValid: {
    type: Boolean,
    default: true
  },
  validationErrors: [{
    field: String,
    error: String,
    timestamp: {
      type: Date,
      default: Date.now
    }
  }],
  
  createdAt: {
    type: Date,
    default: Date.now
  }
}, {
  timestamps: true
});

// Indexes
deviceDataSchema.index({ playerId: 1, sessionId: 1 });
deviceDataSchema.index({ deviceId: 1, sourceTimestamp: 1 });
deviceDataSchema.index({ service: 1, sourceTimestamp: 1 });
deviceDataSchema.index({ 'gps.latitude': 1, 'gps.longitude': 1 });
deviceDataSchema.index({ isValid: 1 });

// Encryption key
const ENCRYPTION_KEY = process.env.DATA_ENCRYPTION_KEY || 'your-data-encryption-key-32-chars-long';

// Encryption methods
deviceDataSchema.methods.encryptData = function(data) {
  const iv = crypto.randomBytes(16);
  const cipher = crypto.createCipher('aes-256-gcm', ENCRYPTION_KEY);
  
  let encrypted = cipher.update(JSON.stringify(data), 'utf8', 'hex');
  encrypted += cipher.final('hex');
  
  const authTag = cipher.getAuthTag();
  
  return {
    encrypted,
    iv: iv.toString('hex'),
    authTag: authTag.toString('hex')
  };
};

deviceDataSchema.methods.decryptData = function(encryptedData) {
  const decipher = crypto.createDecipher('aes-256-gcm', ENCRYPTION_KEY);
  decipher.setAuthTag(Buffer.from(encryptedData.authTag, 'hex'));
  
  let decrypted = decipher.update(encryptedData.encrypted, 'hex', 'utf8');
  decrypted += decipher.final('utf8');
  
  return JSON.parse(decrypted);
};

// Pre-save middleware
deviceDataSchema.pre('save', function(next) {
  if (this.isModified('rawData')) {
    const encrypted = this.encryptData(this.rawData);
    this.rawData = JSON.stringify(encrypted);
  }
  
  // Validate data quality
  this.validateDataQuality();
  
  next();
});

// Instance methods
deviceDataSchema.methods.getRawData = function() {
  try {
    const encryptedData = JSON.parse(this.rawData);
    return this.decryptData(encryptedData);
  } catch (error) {
    throw new Error('Failed to decrypt raw data');
  }
};

deviceDataSchema.methods.validateDataQuality = function() {
  const errors = [];
  
  // Check required fields
  if (!this.sourceTimestamp) {
    errors.push({ field: 'sourceTimestamp', error: 'Missing source timestamp' });
  }
  
  // Validate heart rate
  if (this.heartRate && this.heartRate.value) {
    if (this.heartRate.value < 30 || this.heartRate.value > 250) {
      errors.push({ field: 'heartRate', error: 'Heart rate out of valid range' });
    }
  }
  
  // Validate GPS coordinates
  if (this.gps) {
    if (this.gps.latitude && (this.gps.latitude < -90 || this.gps.latitude > 90)) {
      errors.push({ field: 'gps.latitude', error: 'Invalid latitude' });
    }
    if (this.gps.longitude && (this.gps.longitude < -180 || this.gps.longitude > 180)) {
      errors.push({ field: 'gps.longitude', error: 'Invalid longitude' });
    }
  }
  
  // Update validation status
  this.isValid = errors.length === 0;
  this.validationErrors = errors;
  
  // Set data quality based on validation
  if (errors.length === 0) {
    this.dataQuality = 'high';
  } else if (errors.length <= 2) {
    this.dataQuality = 'medium';
  } else {
    this.dataQuality = 'low';
  }
};

deviceDataSchema.methods.getSummary = function() {
  return {
    id: this.id,
    playerId: this.playerId,
    deviceId: this.deviceId,
    service: this.service,
    sessionId: this.sessionId,
    sourceTimestamp: this.sourceTimestamp,
    dataQuality: this.dataQuality,
    isValid: this.isValid,
    hasHeartRate: !!this.heartRate?.value,
    hasGPS: !!(this.gps?.latitude && this.gps?.longitude),
    hasSpeed: !!this.speed?.value,
    hasDistance: !!this.distance?.value
  };
};

// Static methods
deviceDataSchema.statics.findByPlayer = function(playerId, options = {}) {
  const query = { playerId };
  
  if (options.sessionId) {
    query.sessionId = options.sessionId;
  }
  
  if (options.service) {
    query.service = options.service;
  }
  
  if (options.startDate) {
    query.sourceTimestamp = { $gte: options.startDate };
  }
  
  if (options.endDate) {
    if (query.sourceTimestamp) {
      query.sourceTimestamp.$lte = options.endDate;
    } else {
      query.sourceTimestamp = { $lte: options.endDate };
    }
  }
  
  return this.find(query)
    .sort({ sourceTimestamp: -1 })
    .limit(options.limit || 100);
};

deviceDataSchema.statics.getPlayerStats = function(playerId, startDate, endDate) {
  const matchStage = {
    playerId,
    sourceTimestamp: {
      $gte: startDate,
      $lte: endDate
    }
  };
  
  return this.aggregate([
    { $match: matchStage },
    {
      $group: {
        _id: null,
        totalRecords: { $sum: 1 },
        totalDistance: { $sum: '$distance.value' },
        totalDuration: { $sum: '$duration.value' },
        totalCalories: { $sum: '$calories.value' },
        avgHeartRate: { $avg: '$heartRate.value' },
        maxHeartRate: { $max: '$heartRate.value' },
        minHeartRate: { $min: '$heartRate.value' },
        sessions: { $addToSet: '$sessionId' }
      }
    },
    {
      $project: {
        _id: 0,
        totalRecords: 1,
        totalDistance: 1,
        totalDuration: 1,
        totalCalories: 1,
        avgHeartRate: { $round: ['$avgHeartRate', 2] },
        maxHeartRate: 1,
        minHeartRate: 1,
        uniqueSessions: { $size: '$sessions' }
      }
    }
  ]);
};

deviceDataSchema.statics.cleanupOldData = function(retentionDays) {
  const cutoffDate = new Date();
  cutoffDate.setDate(cutoffDate.getDate() - retentionDays);
  
  return this.deleteMany({
    sourceTimestamp: { $lt: cutoffDate }
  });
};

// Create Sequelize model for PostgreSQL
const createSequelizeModel = (sequelize) => {
  const DeviceData = sequelize.define('DeviceData', {
    id: {
      type: sequelize.DataTypes.UUID,
      defaultValue: sequelize.DataTypes.UUIDV4,
      primaryKey: true
    },
    playerId: {
      type: sequelize.DataTypes.UUID,
      allowNull: false
    },
    deviceId: {
      type: sequelize.DataTypes.STRING,
      allowNull: false
    },
    service: {
      type: sequelize.DataTypes.ENUM('catapult', 'apple', 'garmin'),
      allowNull: false
    },
    sessionId: {
      type: sequelize.DataTypes.STRING,
      allowNull: false
    },
    heartRate: {
      type: sequelize.DataTypes.JSONB
    },
    hrv: {
      type: sequelize.DataTypes.JSONB
    },
    speed: {
      type: sequelize.DataTypes.JSONB
    },
    distance: {
      type: sequelize.DataTypes.JSONB
    },
    altitude: {
      type: sequelize.DataTypes.JSONB
    },
    calories: {
      type: sequelize.DataTypes.JSONB
    },
    duration: {
      type: sequelize.DataTypes.JSONB
    },
    gps: {
      type: sequelize.DataTypes.JSONB
    },
    dataQuality: {
      type: sequelize.DataTypes.ENUM('high', 'medium', 'low'),
      defaultValue: 'medium'
    },
    sourceTimestamp: {
      type: sequelize.DataTypes.DATE,
      allowNull: false
    },
    processedAt: {
      type: sequelize.DataTypes.DATE,
      defaultValue: sequelize.DataTypes.NOW
    },
    rawData: {
      type: sequelize.DataTypes.TEXT,
      allowNull: false
    },
    isValid: {
      type: sequelize.DataTypes.BOOLEAN,
      defaultValue: true
    },
    validationErrors: {
      type: sequelize.DataTypes.JSONB,
      defaultValue: []
    }
  }, {
    tableName: 'device_data',
    timestamps: true
  });

  return DeviceData;
};

module.exports = {
  DeviceData: mongoose.model('DeviceData', deviceDataSchema),
  createSequelizeModel
}; 