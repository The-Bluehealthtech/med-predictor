const { expect } = require('chai');
const sinon = require('sinon');
const OAuth2Service = require('../../src/services/OAuth2Service');
const crypto = require('crypto');

describe('OAuth2Service', () => {
  let oauth2Service;
  let sandbox;

  beforeEach(() => {
    sandbox = sinon.createSandbox();
    oauth2Service = new OAuth2Service();
  });

  afterEach(() => {
    sandbox.restore();
  });

  describe('generateAuthUrl', () => {
    it('should generate correct Catapult auth URL', () => {
      const state = 'test-state';
      const url = oauth2Service.generateAuthUrl('catapult', state);
      
      expect(url).to.include('https://connect.catapultsports.com/oauth/authorize');
      expect(url).to.include('client_id=');
      expect(url).to.include('redirect_uri=');
      expect(url).to.include('state=' + state);
      expect(url).to.include('scope=');
    });

    it('should generate correct Apple auth URL', () => {
      const state = 'test-state';
      const url = oauth2Service.generateAuthUrl('apple', state);
      
      expect(url).to.include('https://appleid.apple.com/auth/authorize');
      expect(url).to.include('client_id=');
      expect(url).to.include('redirect_uri=');
      expect(url).to.include('state=' + state);
    });

    it('should generate correct Garmin auth URL', () => {
      const state = 'test-state';
      const url = oauth2Service.generateAuthUrl('garmin', state);
      
      expect(url).to.include('https://connect.garmin.com/oauthConfirm');
      expect(url).to.include('client_id=');
      expect(url).to.include('redirect_uri=');
      expect(url).to.include('state=' + state);
    });

    it('should throw error for invalid service', () => {
      expect(() => {
        oauth2Service.generateAuthUrl('invalid', 'state');
      }).to.throw('Service non supporté');
    });
  });

  describe('exchangeCodeForToken', () => {
    it('should exchange code for token successfully', async () => {
      const mockResponse = {
        access_token: 'test-access-token',
        refresh_token: 'test-refresh-token',
        expires_in: 3600,
        token_type: 'Bearer'
      };

      const axiosStub = sandbox.stub(require('axios'), 'post').resolves({
        data: mockResponse
      });

      const result = await oauth2Service.exchangeCodeForToken('catapult', 'test-code');

      expect(result).to.deep.equal(mockResponse);
      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should handle token exchange error', async () => {
      const error = new Error('Token exchange failed');
      sandbox.stub(require('axios'), 'post').rejects(error);

      try {
        await oauth2Service.exchangeCodeForToken('catapult', 'test-code');
        expect.fail('Should have thrown an error');
      } catch (err) {
        expect(err.message).to.include('Échec de l\'échange de code');
      }
    });
  });

  describe('refreshToken', () => {
    it('should refresh token successfully', async () => {
      const mockResponse = {
        access_token: 'new-access-token',
        refresh_token: 'new-refresh-token',
        expires_in: 3600
      };

      const axiosStub = sandbox.stub(require('axios'), 'post').resolves({
        data: mockResponse
      });

      const result = await oauth2Service.refreshToken('catapult', 'old-refresh-token');

      expect(result).to.deep.equal(mockResponse);
      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should handle refresh token error', async () => {
      const error = new Error('Refresh failed');
      sandbox.stub(require('axios'), 'post').rejects(error);

      try {
        await oauth2Service.refreshToken('catapult', 'invalid-refresh-token');
        expect.fail('Should have thrown an error');
      } catch (err) {
        expect(err.message).to.include('Échec du refresh du token');
      }
    });
  });

  describe('validateToken', () => {
    it('should validate valid token', async () => {
      const mockResponse = { valid: true, user_id: '123' };
      const axiosStub = sandbox.stub(require('axios'), 'get').resolves({
        data: mockResponse
      });

      const result = await oauth2Service.validateToken('catapult', 'valid-token');

      expect(result).to.be.true;
      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should return false for invalid token', async () => {
      const error = { response: { status: 401 } };
      sandbox.stub(require('axios'), 'get').rejects(error);

      const result = await oauth2Service.validateToken('catapult', 'invalid-token');

      expect(result).to.be.false;
    });
  });

  describe('revokeToken', () => {
    it('should revoke token successfully', async () => {
      const axiosStub = sandbox.stub(require('axios'), 'post').resolves({
        status: 200
      });

      await oauth2Service.revokeToken('catapult', 'token-to-revoke');

      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should handle revoke error gracefully', async () => {
      const error = new Error('Revoke failed');
      sandbox.stub(require('axios'), 'post').rejects(error);

      // Should not throw error
      await oauth2Service.revokeToken('catapult', 'token-to-revoke');
    });
  });

  describe('getUserInfo', () => {
    it('should get user info successfully', async () => {
      const mockUserInfo = {
        id: '123',
        name: 'John Doe',
        email: 'john@example.com'
      };

      const axiosStub = sandbox.stub(require('axios'), 'get').resolves({
        data: mockUserInfo
      });

      const result = await oauth2Service.getUserInfo('catapult', 'valid-token');

      expect(result).to.deep.equal(mockUserInfo);
      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should handle user info error', async () => {
      const error = new Error('Failed to get user info');
      sandbox.stub(require('axios'), 'get').rejects(error);

      try {
        await oauth2Service.getUserInfo('catapult', 'valid-token');
        expect.fail('Should have thrown an error');
      } catch (err) {
        expect(err.message).to.include('Échec de récupération des informations utilisateur');
      }
    });
  });

  describe('getDevices', () => {
    it('should get devices successfully', async () => {
      const mockDevices = [
        { id: '1', name: 'Catapult Vector', type: 'gps' },
        { id: '2', name: 'Apple Watch', type: 'smartwatch' }
      ];

      const axiosStub = sandbox.stub(require('axios'), 'get').resolves({
        data: mockDevices
      });

      const result = await oauth2Service.getDevices('catapult', 'valid-token');

      expect(result).to.deep.equal(mockDevices);
      expect(axiosStub.calledOnce).to.be.true;
    });

    it('should handle devices error', async () => {
      const error = new Error('Failed to get devices');
      sandbox.stub(require('axios'), 'get').rejects(error);

      try {
        await oauth2Service.getDevices('catapult', 'valid-token');
        expect.fail('Should have thrown an error');
      } catch (err) {
        expect(err.message).to.include('Échec de récupération des appareils');
      }
    });
  });

  describe('validateState', () => {
    it('should validate correct state', () => {
      const state = 'test-state';
      const storedState = 'test-state';
      
      const result = oauth2Service.validateState(state, storedState);
      expect(result).to.be.true;
    });

    it('should reject incorrect state', () => {
      const state = 'test-state';
      const storedState = 'different-state';
      
      const result = oauth2Service.validateState(state, storedState);
      expect(result).to.be.false;
    });

    it('should reject empty state', () => {
      const state = '';
      const storedState = 'test-state';
      
      const result = oauth2Service.validateState(state, storedState);
      expect(result).to.be.false;
    });
  });

  describe('generateState', () => {
    it('should generate random state', () => {
      const state1 = oauth2Service.generateState();
      const state2 = oauth2Service.generateState();
      
      expect(state1).to.be.a('string');
      expect(state1.length).to.be.greaterThan(10);
      expect(state1).to.not.equal(state2);
    });
  });
}); 