const express = require('express');
const { body, param, query } = require('express-validator');
const OAuth2Controller = require('../controllers/OAuth2Controller');
const authMiddleware = require('../middleware/auth');
const rateLimit = require('express-rate-limit');

const router = express.Router();

// Rate limiting for OAuth2 endpoints
const oauth2RateLimit = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // limit each IP to 100 requests per windowMs
  message: {
    success: false,
    error: 'Too many OAuth2 requests, please try again later'
  },
  standardHeaders: true,
  legacyHeaders: false
});

// Apply rate limiting to all OAuth2 routes
router.use(oauth2RateLimit);

// Generate OAuth2 authorization URL
router.post('/auth-url', [
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('playerId').isMongoId().withMessage('Invalid player ID'),
  body('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.generateAuthUrl);

// OAuth2 callback endpoints
router.get('/catapult/callback', OAuth2Controller.handleCallback);
router.get('/apple/callback', OAuth2Controller.handleCallback);
router.get('/garmin/callback', OAuth2Controller.handleCallback);

// Get OAuth2 tokens
router.get('/tokens/:playerId', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  query('service').optional().isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  query('deviceId').optional().notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.getTokens);

// Refresh OAuth2 tokens
router.post('/refresh', [
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('playerId').isMongoId().withMessage('Invalid player ID'),
  body('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.refreshTokens);

// Revoke OAuth2 tokens
router.post('/revoke', [
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('playerId').isMongoId().withMessage('Invalid player ID'),
  body('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin']), OAuth2Controller.revokeTokens);

// Validate OAuth2 tokens
router.get('/validate/:service/:playerId/:deviceId', [
  param('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  param('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.validateTokens);

// Get user info from OAuth2 service
router.get('/user-info/:service/:playerId/:deviceId', [
  param('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  param('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.getUserInfo);

// Get connected devices from OAuth2 service
router.get('/devices/:service/:playerId/:deviceId', [
  param('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  param('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), OAuth2Controller.getConnectedDevices);

// List configured OAuth2 services
router.get('/services', authMiddleware.authenticate, OAuth2Controller.listServices);

// Get OAuth2 connection statistics
router.get('/stats', authMiddleware.authenticate, authMiddleware.requireRole(['admin']), OAuth2Controller.getConnectionStats);

module.exports = router; 