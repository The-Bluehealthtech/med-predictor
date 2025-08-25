const mongoose = require('mongoose');
const { v4: uuidv4 } = require('uuid');

const playerSchema = new mongoose.Schema({
  // FIFA Connect ID (UUID)
  fifaConnectId: {
    type: String,
    required: true,
    unique: true,
    default: () => uuidv4(),
    index: true
  },

  // Personal Information
  firstName: {
    type: String,
    required: true,
    trim: true,
    maxlength: 50
  },
  lastName: {
    type: String,
    required: true,
    trim: true,
    maxlength: 50
  },
  email: {
    type: String,
    required: true,
    unique: true,
    lowercase: true,
    trim: true
  },
  dateOfBirth: {
    type: Date,
    required: true
  },
  nationality: {
    type: String,
    required: true,
    maxlength: 3 // ISO 3166-1 alpha-3
  },

  // Team Information
  teamId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Team',
    required: true
  },
  position: {
    type: String,
    enum: ['GK', 'DEF', 'MID', 'FWD'],
    required: true
  },
  jerseyNumber: {
    type: Number,
    min: 1,
    max: 99
  },

  // Connected Devices
  connectedDevices: [{
    service: {
      type: String,
      enum: ['catapult', 'apple', 'garmin'],
      required: true
    },
    deviceId: {
      type: String,
      required: true
    },
    deviceName: {
      type: String,
      required: true
    },
    deviceType: {
      type: String,
      enum: ['catapult_vector', 'apple_watch', 'garmin_device'],
      required: true
    },
    isActive: {
      type: Boolean,
      default: true
    },
    lastSync: {
      type: Date
    },
    syncFrequency: {
      type: String,
      enum: ['real_time', 'near_real_time', 'standard', 'background'],
      default: 'standard'
    },
    createdAt: {
      type: Date,
      default: Date.now
    }
  }],

  // Sync Preferences
  syncPreferences: {
    autoSync: {
      type: Boolean,
      default: true
    },
    syncInterval: {
      type: Number,
      default: 15, // minutes
      min: 1,
      max: 1440
    },
    dataRetentionDays: {
      type: Number,
      default: 365,
      min: 30,
      max: 2555
    },
    notifications: {
      syncErrors: {
        type: Boolean,
        default: true
      },
      syncSuccess: {
        type: Boolean,
        default: false
      },
      deviceDisconnection: {
        type: Boolean,
        default: true
      }
    }
  },

  // Statistics
  stats: {
    totalSessions: {
      type: Number,
      default: 0
    },
    totalDistance: {
      type: Number,
      default: 0
    },
    totalDuration: {
      type: Number,
      default: 0
    },
    averageHeartRate: {
      type: Number,
      default: 0
    },
    lastActivity: {
      type: Date
    }
  },

  // Status
  status: {
    type: String,
    enum: ['active', 'inactive', 'suspended', 'retired'],
    default: 'active'
  },

  // Metadata
  createdAt: {
    type: Date,
    default: Date.now
  },
  updatedAt: {
    type: Date,
    default: Date.now
  },
  createdBy: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  updatedBy: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User'
  }
}, {
  timestamps: true,
  toJSON: { virtuals: true },
  toObject: { virtuals: true }
});

// Indexes
playerSchema.index({ 'connectedDevices.service': 1, 'connectedDevices.deviceId': 1 });
playerSchema.index({ teamId: 1, status: 1 });
playerSchema.index({ createdAt: -1 });
playerSchema.index({ 'stats.lastActivity': -1 });

// Virtual for full name
playerSchema.virtual('fullName').get(function() {
  return `${this.firstName} ${this.lastName}`;
});

// Virtual for age
playerSchema.virtual('age').get(function() {
  if (!this.dateOfBirth) return null;
  const today = new Date();
  const birthDate = new Date(this.dateOfBirth);
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  
  return age;
});

// Pre-save middleware
playerSchema.pre('save', function(next) {
  this.updatedAt = new Date();
  next();
});

// Instance methods
playerSchema.methods.addDevice = function(deviceData) {
  const existingDeviceIndex = this.connectedDevices.findIndex(
    device => device.service === deviceData.service && device.deviceId === deviceData.deviceId
  );

  if (existingDeviceIndex >= 0) {
    // Update existing device
    this.connectedDevices[existingDeviceIndex] = {
      ...this.connectedDevices[existingDeviceIndex],
      ...deviceData,
      updatedAt: new Date()
    };
  } else {
    // Add new device
    this.connectedDevices.push({
      ...deviceData,
      createdAt: new Date()
    });
  }
};

playerSchema.methods.removeDevice = function(service, deviceId) {
  this.connectedDevices = this.connectedDevices.filter(
    device => !(device.service === service && device.deviceId === deviceId)
  );
};

playerSchema.methods.getActiveDevices = function() {
  return this.connectedDevices.filter(device => device.isActive);
};

playerSchema.methods.updateLastSync = function(service, deviceId) {
  const device = this.connectedDevices.find(
    d => d.service === service && d.deviceId === deviceId
  );
  
  if (device) {
    device.lastSync = new Date();
  }
};

// Static methods
playerSchema.statics.findByFifaConnectId = function(fifaConnectId) {
  return this.findOne({ fifaConnectId });
};

playerSchema.statics.findByDevice = function(service, deviceId) {
  return this.findOne({
    'connectedDevices.service': service,
    'connectedDevices.deviceId': deviceId,
    'connectedDevices.isActive': true
  });
};

playerSchema.statics.findActivePlayers = function() {
  return this.find({ status: 'active' });
};

playerSchema.statics.findByTeam = function(teamId) {
  return this.find({ teamId, status: 'active' });
};

// Export both Mongoose model and Sequelize model for dual database support
const Player = mongoose.model('Player', playerSchema);

// Sequelize model for PostgreSQL - chargement différé
let PlayerSequelize = null;

const getPlayerSequelize = () => {
  if (!PlayerSequelize) {
    const sequelize = require('../config/database').sequelize;
    if (!sequelize) {
      throw new Error('Sequelize connection not available');
    }
    
    const { DataTypes } = require('sequelize');
    
    PlayerSequelize = sequelize.define('Player', {
      id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true
      },
      fifaConnectId: {
        type: DataTypes.UUID,
        allowNull: false,
        unique: true
      },
      firstName: {
        type: DataTypes.STRING(50),
        allowNull: false
      },
      lastName: {
        type: DataTypes.STRING(50),
        allowNull: false
      },
      email: {
        type: DataTypes.STRING,
        allowNull: false,
        unique: true,
        validate: {
          isEmail: true
        }
      },
      dateOfBirth: {
        type: DataTypes.DATEONLY,
        allowNull: false
      },
      nationality: {
        type: DataTypes.STRING(3),
        allowNull: false
      },
      teamId: {
        type: DataTypes.UUID,
        allowNull: false
      },
      position: {
        type: DataTypes.ENUM('GK', 'DEF', 'MID', 'FWD'),
        allowNull: false
      },
      jerseyNumber: {
        type: DataTypes.INTEGER,
        validate: {
          min: 1,
          max: 99
        }
      },
      status: {
        type: DataTypes.ENUM('active', 'inactive', 'suspended', 'retired'),
        defaultValue: 'active'
      },
      syncPreferences: {
        type: DataTypes.JSONB,
        defaultValue: {
          autoSync: true,
          syncInterval: 15,
          dataRetentionDays: 365,
          notifications: {
            syncErrors: true,
            syncSuccess: false,
            deviceDisconnection: true
          }
        }
      },
      stats: {
        type: DataTypes.JSONB,
        defaultValue: {
          totalSessions: 0,
          totalDistance: 0,
          totalDuration: 0,
          averageHeartRate: 0
        }
      }
    }, {
      tableName: 'players',
      timestamps: true
    });
  }
  
  return PlayerSequelize;
};

module.exports = {
  Player, // Mongoose model
  getPlayerSequelize // Function to get Sequelize model
}; 