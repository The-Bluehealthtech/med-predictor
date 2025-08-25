const express = require('express');
const { body, param, query } = require('express-validator');
const DeviceController = require('../controllers/DeviceController');
const authMiddleware = require('../middleware/auth');
const rateLimit = require('express-rate-limit');

const router = express.Router();

// Rate limiting for device endpoints
const deviceRateLimit = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 200, // limit each IP to 200 requests per windowMs
  message: {
    success: false,
    error: 'Too many device requests, please try again later'
  },
  standardHeaders: true,
  legacyHeaders: false
});

// Apply rate limiting to all device routes
router.use(deviceRateLimit);

// List compatible connected devices
router.get('/:playerId', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  query('service').optional().isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), DeviceController.listDevices);

// Associate device with player
router.post('/:playerId/associate', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('deviceId').notEmpty().withMessage('Device ID is required'),
  body('deviceName').notEmpty().withMessage('Device name is required'),
  body('deviceType').isIn(['catapult_vector', 'apple_watch', 'garmin_device']).withMessage('Invalid device type'),
  body('syncFrequency').optional().isIn(['real_time', 'near_real_time', 'standard', 'background']).withMessage('Invalid sync frequency')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach']), DeviceController.associateDevice);

// Disassociate device from player
router.delete('/:playerId/:service/:deviceId', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  param('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  param('deviceId').notEmpty().withMessage('Device ID is required')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach']), DeviceController.disassociateDevice);

// Update device status
router.patch('/:playerId/status', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('deviceId').notEmpty().withMessage('Device ID is required'),
  body('isActive').optional().isBoolean().withMessage('isActive must be a boolean'),
  body('syncFrequency').optional().isIn(['real_time', 'near_real_time', 'standard', 'background']).withMessage('Invalid sync frequency')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach']), DeviceController.updateDeviceStatus);

// Trigger manual synchronization
router.post('/:playerId/sync', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  body('service').isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  body('deviceId').notEmpty().withMessage('Device ID is required'),
  body('startDate').optional().isISO8601().withMessage('Invalid start date format'),
  body('endDate').optional().isISO8601().withMessage('Invalid end date format')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), DeviceController.triggerSync);

// Get synchronization history
router.get('/:playerId/history', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  query('service').optional().isIn(['catapult', 'apple', 'garmin']).withMessage('Invalid service'),
  query('startDate').optional().isISO8601().withMessage('Invalid start date format'),
  query('endDate').optional().isISO8601().withMessage('Invalid end date format'),
  query('limit').optional().isInt({ min: 1, max: 100 }).withMessage('Limit must be between 1 and 100')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), DeviceController.getSyncHistory);

// Get player data
router.get('/:playerId/data', [
  param('playerId').isMongoId().withMessage('Invalid player ID'),
  query('startDate').optional().isISO8601().withMessage('Invalid start date format'),
  query('endDate').optional().isISO8601().withMessage('Invalid end date format'),
  query('activityType').optional().isIn(['training', 'match', 'recovery', 'other']).withMessage('Invalid activity type'),
  query('limit').optional().isInt({ min: 1, max: 100 }).withMessage('Limit must be between 1 and 100')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), DeviceController.getPlayerData);

// Get sync status for player
router.get('/:playerId/status', [
  param('playerId').isMongoId().withMessage('Invalid player ID')
], authMiddleware.authenticate, authMiddleware.requireRole(['admin', 'coach', 'analyst']), DeviceController.getSyncStatus);

// Get device statistics
router.get('/stats/overview', authMiddleware.authenticate, authMiddleware.requireRole(['admin']), DeviceController.getDeviceStats);

module.exports = router; 