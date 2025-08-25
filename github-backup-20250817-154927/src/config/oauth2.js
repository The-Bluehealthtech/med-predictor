const crypto = require('crypto');

const oauth2Config = {
  // Catapult Connect Configuration
  catapult: {
    clientId: process.env.CATAPULT_CLIENT_ID,
    clientSecret: process.env.CATAPULT_CLIENT_SECRET,
    authorizationUrl: 'https://connect.catapultsports.com/oauth/authorize',
    tokenUrl: 'https://connect.catapultsports.com/oauth/token',
    redirectUri: `${process.env.BASE_URL}/api/oauth2/catapult/callback`,
    scope: 'read:athlete read:activity read:location read:biometrics',
    stateSecret: process.env.OAUTH2_STATE_SECRET || crypto.randomBytes(32).toString('hex'),
    tokenExpiry: 3600, // 1 hour
    refreshThreshold: 300, // 5 minutes before expiry
    dataMapping: {
      heartRate: 'heart_rate',
      hrv: 'hrv',
      speed: 'speed',
      distance: 'distance',
      altitude: 'altitude',
      calories: 'calories',
      duration: 'duration',
      gpsCoordinates: 'gps_coordinates',
      timestamp: 'timestamp'
    }
  },

  // Apple HealthKit Configuration
  apple: {
    clientId: process.env.APPLE_CLIENT_ID,
    clientSecret: process.env.APPLE_CLIENT_SECRET,
    authorizationUrl: 'https://appleid.apple.com/auth/authorize',
    tokenUrl: 'https://appleid.apple.com/auth/token',
    redirectUri: `${process.env.BASE_URL}/api/oauth2/apple/callback`,
    scope: 'health.read',
    stateSecret: process.env.OAUTH2_STATE_SECRET || crypto.randomBytes(32).toString('hex'),
    tokenExpiry: 3600,
    refreshThreshold: 300,
    dataMapping: {
      heartRate: 'heartRate',
      hrv: 'heartRateVariability',
      speed: 'speed',
      distance: 'distance',
      altitude: 'altitude',
      calories: 'activeEnergyBurned',
      duration: 'duration',
      gpsCoordinates: 'location',
      timestamp: 'startDate'
    }
  },

  // Garmin Connect Configuration
  garmin: {
    clientId: process.env.GARMIN_CLIENT_ID,
    clientSecret: process.env.GARMIN_CLIENT_SECRET,
    authorizationUrl: 'https://connect.garmin.com/oauthConfirm',
    tokenUrl: 'https://connect.garmin.com/oauth/token',
    redirectUri: `${process.env.BASE_URL}/api/oauth2/garmin/callback`,
    scope: 'read_activity read_heart_rate read_location',
    stateSecret: process.env.OAUTH2_STATE_SECRET || crypto.randomBytes(32).toString('hex'),
    tokenExpiry: 3600,
    refreshThreshold: 300,
    dataMapping: {
      heartRate: 'heartRate',
      hrv: 'hrv',
      speed: 'speed',
      distance: 'distance',
      altitude: 'altitude',
      calories: 'calories',
      duration: 'duration',
      gpsCoordinates: 'gpsCoordinates',
      timestamp: 'timestamp'
    }
  },

  // Common OAuth2 settings
  common: {
    stateExpiry: 600, // 10 minutes
    maxRetries: 3,
    retryDelay: 1000, // 1 second
    timeout: 30000, // 30 seconds
    userAgent: 'FIT-Microservice/1.0.0'
  },

  // Rate limiting per service
  rateLimits: {
    catapult: {
      requestsPerMinute: 60,
      requestsPerHour: 1000
    },
    apple: {
      requestsPerMinute: 30,
      requestsPerHour: 500
    },
    garmin: {
      requestsPerMinute: 45,
      requestsPerHour: 800
    }
  },

  // Data sync intervals (in minutes)
  syncIntervals: {
    realTime: 1, // Real-time data (if available)
    nearRealTime: 5, // Near real-time data
    standard: 15, // Standard sync interval
    background: 60 // Background sync
  },

  // Error handling
  errorHandling: {
    maxConsecutiveFailures: 5,
    backoffMultiplier: 2,
    maxBackoffDelay: 3600000, // 1 hour
    retryableErrors: [
      'ECONNRESET',
      'ETIMEDOUT',
      'ENOTFOUND',
      'ECONNREFUSED',
      'rate_limit_exceeded',
      'temporary_unavailable'
    ]
  }
};

// Validation function for OAuth2 configuration
function validateOAuth2Config() {
  const requiredServices = ['catapult', 'apple', 'garmin'];
  const missingConfigs = [];

  requiredServices.forEach(service => {
    const config = oauth2Config[service];
    if (!config.clientId || !config.clientSecret) {
      missingConfigs.push(`${service} OAuth2 credentials`);
    }
  });

  if (missingConfigs.length > 0) {
    throw new Error(`Missing OAuth2 configuration: ${missingConfigs.join(', ')}`);
  }

  return true;
}

// Generate state parameter for OAuth2 flow
function generateState(service) {
  const state = crypto.randomBytes(32).toString('hex');
  const timestamp = Date.now();
  const signature = crypto
    .createHmac('sha256', oauth2Config[service].stateSecret)
    .update(`${state}${timestamp}`)
    .digest('hex');

  return {
    state,
    timestamp,
    signature,
    fullState: `${state}.${timestamp}.${signature}`
  };
}

// Validate state parameter
function validateState(fullState, service) {
  try {
    const [state, timestamp, signature] = fullState.split('.');
    const expectedSignature = crypto
      .createHmac('sha256', oauth2Config[service].stateSecret)
      .update(`${state}${timestamp}`)
      .digest('hex');

    if (signature !== expectedSignature) {
      return false;
    }

    const stateAge = Date.now() - parseInt(timestamp);
    if (stateAge > oauth2Config.common.stateExpiry * 1000) {
      return false;
    }

    return true;
  } catch (error) {
    return false;
  }
}

module.exports = {
  oauth2Config,
  validateOAuth2Config,
  generateState,
  validateState
}; 