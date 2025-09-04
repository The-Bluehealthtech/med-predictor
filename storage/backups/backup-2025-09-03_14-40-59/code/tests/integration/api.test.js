const { expect } = require('chai');
const request = require('supertest');
const app = require('../../src/app');
const mongoose = require('mongoose');
const { Sequelize } = require('sequelize');

describe('FIT API Integration Tests', () => {
  let server;
  let testPlayerId;
  let authToken;

  before(async () => {
    // Connecter aux bases de données de test
    await mongoose.connect(process.env.TEST_DATABASE_URL || 'mongodb://localhost:27017/fit_test_database');
    
    // Créer un utilisateur de test
    const testPlayer = await mongoose.connection.db.collection('players').insertOne({
      fifaConnectId: '550e8400-e29b-41d4-a716-446655440000',
      firstName: 'Test',
      lastName: 'Player',
      email: 'test@example.com',
      createdAt: new Date(),
      updatedAt: new Date()
    });
    
    testPlayerId = testPlayer.insertedId;
    
    // Générer un token JWT de test
    const jwt = require('jsonwebtoken');
    authToken = jwt.sign(
      { 
        userId: testPlayerId.toString(),
        role: 'player',
        permissions: ['read:own_data', 'write:own_data']
      },
      process.env.JWT_SECRET || 'test-secret',
      { expiresIn: '1h' }
    );
  });

  after(async () => {
    // Nettoyer les bases de données de test
    await mongoose.connection.db.dropDatabase();
    await mongoose.disconnect();
    
    if (server) {
      server.close();
    }
  });

  describe('Health Check', () => {
    it('should return health status', async () => {
      const response = await request(app)
        .get('/health')
        .expect(200);

      expect(response.body).to.have.property('status', 'ok');
      expect(response.body).to.have.property('timestamp');
      expect(response.body).to.have.property('version');
    });
  });

  describe('OAuth2 Endpoints', () => {
    describe('GET /api/v1/oauth2/services', () => {
      it('should return configured OAuth2 services', async () => {
        const response = await request(app)
          .get('/api/v1/oauth2/services')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('services');
        expect(response.body.services).to.be.an('array');
        expect(response.body.services).to.include('catapult');
        expect(response.body.services).to.include('apple');
        expect(response.body.services).to.include('garmin');
      });

      it('should require authentication', async () => {
        await request(app)
          .get('/api/v1/oauth2/services')
          .expect(401);
      });
    });

    describe('GET /api/v1/oauth2/:service/auth-url', () => {
      it('should generate auth URL for Catapult', async () => {
        const response = await request(app)
          .get('/api/v1/oauth2/catapult/auth-url')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('authUrl');
        expect(response.body).to.have.property('state');
        expect(response.body.authUrl).to.include('connect.catapultsports.com');
        expect(response.body.authUrl).to.include('client_id=');
      });

      it('should generate auth URL for Apple', async () => {
        const response = await request(app)
          .get('/api/v1/oauth2/apple/auth-url')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('authUrl');
        expect(response.body).to.have.property('state');
        expect(response.body.authUrl).to.include('appleid.apple.com');
      });

      it('should generate auth URL for Garmin', async () => {
        const response = await request(app)
          .get('/api/v1/oauth2/garmin/auth-url')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('authUrl');
        expect(response.body).to.have.property('state');
        expect(response.body.authUrl).to.include('connect.garmin.com');
      });

      it('should reject invalid service', async () => {
        await request(app)
          .get('/api/v1/oauth2/invalid/auth-url')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(400);
      });
    });

    describe('POST /api/v1/oauth2/:service/callback', () => {
      it('should handle OAuth2 callback successfully', async () => {
        // Mock du service OAuth2
        const OAuth2Service = require('../../src/services/OAuth2Service');
        const mockTokenResponse = {
          access_token: 'test-access-token',
          refresh_token: 'test-refresh-token',
          expires_in: 3600
        };

        const sinon = require('sinon');
        const stub = sinon.stub(OAuth2Service.prototype, 'exchangeCodeForToken').resolves(mockTokenResponse);

        const response = await request(app)
          .post('/api/v1/oauth2/catapult/callback')
          .set('Authorization', `Bearer ${authToken}`)
          .send({
            code: 'test-auth-code',
            state: 'test-state'
          })
          .expect(200);

        expect(response.body).to.have.property('message', 'Authentification OAuth2 réussie');
        expect(response.body).to.have.property('service', 'catapult');

        stub.restore();
      });

      it('should handle callback error', async () => {
        const OAuth2Service = require('../../src/services/OAuth2Service');
        const sinon = require('sinon');
        const stub = sinon.stub(OAuth2Service.prototype, 'exchangeCodeForToken').rejects(new Error('Auth failed'));

        await request(app)
          .post('/api/v1/oauth2/catapult/callback')
          .set('Authorization', `Bearer ${authToken}`)
          .send({
            code: 'invalid-code',
            state: 'test-state'
          })
          .expect(400);

        stub.restore();
      });
    });

    describe('GET /api/v1/oauth2/tokens', () => {
      it('should return player OAuth tokens', async () => {
        // Insérer un token de test
        await mongoose.connection.db.collection('oauth_tokens').insertOne({
          playerId: testPlayerId,
          service: 'catapult',
          encryptedAccessToken: 'encrypted-access-token',
          encryptedRefreshToken: 'encrypted-refresh-token',
          expiresAt: new Date(Date.now() + 3600000),
          status: 'active',
          createdAt: new Date(),
          updatedAt: new Date()
        });

        const response = await request(app)
          .get('/api/v1/oauth2/tokens')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('tokens');
        expect(response.body.tokens).to.be.an('array');
        expect(response.body.tokens[0]).to.have.property('service', 'catapult');
        expect(response.body.tokens[0]).to.have.property('status', 'active');
      });
    });

    describe('POST /api/v1/oauth2/tokens/:service/refresh', () => {
      it('should refresh OAuth token', async () => {
        const OAuth2Service = require('../../src/services/OAuth2Service');
        const mockRefreshResponse = {
          access_token: 'new-access-token',
          refresh_token: 'new-refresh-token',
          expires_in: 3600
        };

        const sinon = require('sinon');
        const stub = sinon.stub(OAuth2Service.prototype, 'refreshToken').resolves(mockRefreshResponse);

        const response = await request(app)
          .post('/api/v1/oauth2/tokens/catapult/refresh')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('message', 'Token rafraîchi avec succès');

        stub.restore();
      });
    });

    describe('DELETE /api/v1/oauth2/tokens/:service', () => {
      it('should revoke OAuth token', async () => {
        const OAuth2Service = require('../../src/services/OAuth2Service');
        const sinon = require('sinon');
        const stub = sinon.stub(OAuth2Service.prototype, 'revokeToken').resolves();

        const response = await request(app)
          .delete('/api/v1/oauth2/tokens/catapult')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('message', 'Token révoqué avec succès');

        stub.restore();
      });
    });
  });

  describe('Device Management Endpoints', () => {
    describe('GET /api/v1/devices', () => {
      it('should return player devices', async () => {
        // Insérer des appareils de test
        await mongoose.connection.db.collection('players').updateOne(
          { _id: testPlayerId },
          {
            $set: {
              connectedDevices: [
                {
                  id: 'device-1',
                  name: 'Catapult Vector',
                  type: 'gps',
                  service: 'catapult',
                  status: 'connected',
                  lastSync: new Date()
                }
              ]
            }
          }
        );

        const response = await request(app)
          .get('/api/v1/devices')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('devices');
        expect(response.body.devices).to.be.an('array');
        expect(response.body.devices[0]).to.have.property('name', 'Catapult Vector');
      });
    });

    describe('POST /api/v1/devices/:deviceId/associate', () => {
      it('should associate device with player', async () => {
        const response = await request(app)
          .post('/api/v1/devices/device-2/associate')
          .set('Authorization', `Bearer ${authToken}`)
          .send({
            name: 'Apple Watch',
            type: 'smartwatch',
            service: 'apple'
          })
          .expect(200);

        expect(response.body).to.have.property('message', 'Appareil associé avec succès');
      });
    });

    describe('DELETE /api/v1/devices/:deviceId', () => {
      it('should disassociate device from player', async () => {
        const response = await request(app)
          .delete('/api/v1/devices/device-1')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('message', 'Appareil dissocié avec succès');
      });
    });

    describe('POST /api/v1/devices/sync', () => {
      it('should trigger manual sync', async () => {
        const DataSyncService = require('../../src/services/DataSyncService');
        const sinon = require('sinon');
        const stub = sinon.stub(DataSyncService.prototype, 'syncPlayerData').resolves({
          success: true,
          syncedRecords: 10
        });

        const response = await request(app)
          .post('/api/v1/devices/sync')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('message', 'Synchronisation déclenchée');
        expect(response.body).to.have.property('syncedRecords', 10);

        stub.restore();
      });
    });

    describe('GET /api/v1/devices/sync-history', () => {
      it('should return sync history', async () => {
        const response = await request(app)
          .get('/api/v1/devices/sync-history')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('history');
        expect(response.body.history).to.be.an('array');
      });
    });
  });

  describe('Data Endpoints', () => {
    describe('GET /api/v1/data', () => {
      it('should return player data', async () => {
        // Insérer des données de test
        await mongoose.connection.db.collection('device_data').insertOne({
          playerId: testPlayerId,
          deviceId: 'device-1',
          dataType: 'biometric',
          timestamp: new Date(),
          data: {
            heartRate: 75,
            hrv: 45,
            calories: 120
          },
          encrypted: false,
          createdAt: new Date()
        });

        const response = await request(app)
          .get('/api/v1/data')
          .set('Authorization', `Bearer ${authToken}`)
          .query({
            type: 'biometric',
            startDate: new Date(Date.now() - 86400000).toISOString(),
            endDate: new Date().toISOString()
          })
          .expect(200);

        expect(response.body).to.have.property('data');
        expect(response.body.data).to.be.an('array');
        expect(response.body.data[0]).to.have.property('dataType', 'biometric');
      });
    });

    describe('GET /api/v1/data/stats', () => {
      it('should return data statistics', async () => {
        const response = await request(app)
          .get('/api/v1/data/stats')
          .set('Authorization', `Bearer ${authToken}`)
          .expect(200);

        expect(response.body).to.have.property('stats');
        expect(response.body.stats).to.have.property('totalRecords');
        expect(response.body.stats).to.have.property('lastSync');
      });
    });
  });

  describe('Error Handling', () => {
    it('should handle 404 errors', async () => {
      await request(app)
        .get('/api/v1/nonexistent')
        .set('Authorization', `Bearer ${authToken}`)
        .expect(404);
    });

    it('should handle invalid JWT token', async () => {
      await request(app)
        .get('/api/v1/oauth2/services')
        .set('Authorization', 'Bearer invalid-token')
        .expect(401);
    });

    it('should handle rate limiting', async () => {
      // Faire plusieurs requêtes rapides pour déclencher le rate limiting
      const promises = [];
      for (let i = 0; i < 110; i++) {
        promises.push(
          request(app)
            .get('/api/v1/oauth2/services')
            .set('Authorization', `Bearer ${authToken}`)
        );
      }

      const responses = await Promise.all(promises);
      const rateLimited = responses.some(response => response.status === 429);
      expect(rateLimited).to.be.true;
    });
  });

  describe('Validation', () => {
    it('should validate required fields', async () => {
      await request(app)
        .post('/api/v1/devices/device-1/associate')
        .set('Authorization', `Bearer ${authToken}`)
        .send({})
        .expect(400);
    });

    it('should validate date formats', async () => {
      await request(app)
        .get('/api/v1/data')
        .set('Authorization', `Bearer ${authToken}`)
        .query({
          startDate: 'invalid-date',
          endDate: 'invalid-date'
        })
        .expect(400);
    });
  });
}); 