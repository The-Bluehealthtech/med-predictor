const mongoose = require('mongoose');
const { Sequelize } = require('sequelize');
const Redis = require('redis');
const { logger } = require('../utils/logger');

class DatabaseManager {
  constructor() {
    this.mongoConnection = null;
    this.postgresConnection = null;
    this.redisClient = null;
  }

  // MongoDB Connection
  async connectMongo() {
    try {
      const mongoUri = process.env.MONGODB_URI || 'mongodb://localhost:27017/fit_database';
      this.mongoConnection = await mongoose.connect(mongoUri, {
        useNewUrlParser: true,
        useUnifiedTopology: true,
        maxPoolSize: 10,
        serverSelectionTimeoutMS: 5000,
        socketTimeoutMS: 45000,
      });
      
      logger.info('MongoDB connected successfully');
      return this.mongoConnection;
    } catch (error) {
      logger.error('MongoDB connection error:', error);
      throw error;
    }
  }

  // PostgreSQL Connection
  async connectPostgres() {
    try {
      this.postgresConnection = new Sequelize(
        process.env.POSTGRES_DB || 'fit_database',
        process.env.POSTGRES_USER || 'postgres',
        process.env.POSTGRES_PASSWORD || 'password',
        {
          host: process.env.POSTGRES_HOST || 'localhost',
          port: process.env.POSTGRES_PORT || 5432,
          dialect: 'postgres',
          logging: process.env.NODE_ENV === 'development' ? console.log : false,
          pool: {
            max: 10,
            min: 0,
            acquire: 30000,
            idle: 10000
          },
          dialectOptions: {
            ssl: process.env.NODE_ENV === 'production' ? {
              require: true,
              rejectUnauthorized: false
            } : false
          }
        }
      );

      await this.postgresConnection.authenticate();
      logger.info('PostgreSQL connected successfully');
      return this.postgresConnection;
    } catch (error) {
      logger.error('PostgreSQL connection error:', error);
      throw error;
    }
  }

  // Redis Connection
  async connectRedis() {
    try {
      this.redisClient = Redis.createClient({
        url: process.env.REDIS_URL || 'redis://localhost:6379',
        password: process.env.REDIS_PASSWORD,
        retry_strategy: (options) => {
          if (options.error && options.error.code === 'ECONNREFUSED') {
            return new Error('The server refused the connection');
          }
          if (options.total_retry_time > 1000 * 60 * 60) {
            return new Error('Retry time exhausted');
          }
          if (options.attempt > 10) {
            return undefined;
          }
          return Math.min(options.attempt * 100, 3000);
        }
      });

      this.redisClient.on('error', (err) => {
        logger.error('Redis Client Error:', err);
      });

      this.redisClient.on('connect', () => {
        logger.info('Redis connected successfully');
      });

      await this.redisClient.connect();
      return this.redisClient;
    } catch (error) {
      logger.error('Redis connection error:', error);
      throw error;
    }
  }

  // Connect all databases
  async connectAll() {
    try {
      await Promise.all([
        this.connectMongo(),
        this.connectPostgres(),
        this.connectRedis()
      ]);
      logger.info('All database connections established');
    } catch (error) {
      logger.error('Failed to connect to databases:', error);
      throw error;
    }
  }

  // Disconnect all databases
  async disconnectAll() {
    try {
      if (this.mongoConnection) {
        await mongoose.disconnect();
        logger.info('MongoDB disconnected');
      }
      
      if (this.postgresConnection) {
        await this.postgresConnection.close();
        logger.info('PostgreSQL disconnected');
      }
      
      if (this.redisClient) {
        await this.redisClient.quit();
        logger.info('Redis disconnected');
      }
    } catch (error) {
      logger.error('Error disconnecting databases:', error);
    }
  }

  // Health check
  async healthCheck() {
    const status = {
      mongodb: false,
      postgresql: false,
      redis: false
    };

    try {
      if (mongoose.connection.readyState === 1) {
        status.mongodb = true;
      }
    } catch (error) {
      logger.error('MongoDB health check failed:', error);
    }

    try {
      if (this.postgresConnection) {
        await this.postgresConnection.authenticate();
        status.postgresql = true;
      }
    } catch (error) {
      logger.error('PostgreSQL health check failed:', error);
    }

    try {
      if (this.redisClient && this.redisClient.isReady) {
        await this.redisClient.ping();
        status.redis = true;
      }
    } catch (error) {
      logger.error('Redis health check failed:', error);
    }

    return status;
  }
}

const databaseManager = new DatabaseManager();

// Export de l'instance et des connexions pour les modèles
module.exports = {
  // Méthodes de la classe
  connectMongo: databaseManager.connectMongo.bind(databaseManager),
  connectPostgres: databaseManager.connectPostgres.bind(databaseManager),
  connectRedis: databaseManager.connectRedis.bind(databaseManager),
  connectAll: databaseManager.connectAll.bind(databaseManager),
  disconnectAll: databaseManager.disconnectAll.bind(databaseManager),
  healthCheck: databaseManager.healthCheck.bind(databaseManager),
  
  // Propriétés
  mongoConnection: databaseManager.mongoConnection,
  postgresConnection: databaseManager.postgresConnection,
  redisClient: databaseManager.redisClient,
  
  // Connexions pour les modèles
  sequelize: null, // Sera défini après connexion
  mongoose: mongoose,
  Redis: Redis
}; 