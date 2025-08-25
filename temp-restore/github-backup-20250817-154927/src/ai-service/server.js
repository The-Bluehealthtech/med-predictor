const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const compression = require('compression');
const rateLimit = require('express-rate-limit');
require('dotenv').config();

const logger = require('./utils/logger');
const errorHandler = require('./middleware/errorHandler');
const authMiddleware = require('./middleware/auth');
const validationMiddleware = require('./middleware/validation');

// Import routes
const scaRiskRoutes = require('./routes/scaRisk');
const rtpValidatorRoutes = require('./routes/rtpValidator');
const pcmaExtractorRoutes = require('./routes/pcmaExtractor');
const injuryPredictionRoutes = require('./routes/injuryPrediction');
const medicalNoteRoutes = require('./routes/medicalNote');
const complianceRoutes = require('./routes/compliance');
const healthRoutes = require('./routes/health');
const whisperRoutes = require('./routes/whisper');
const ocrRoutes = require('./routes/ocr');
const medGeminiRoutes = require('./routes/medGemini');

const app = express();
const PORT = process.env.PORT || 3001;

// Security middleware
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      scriptSrc: ["'self'"],
      imgSrc: ["'self'", "data:", "https:"],
    },
  },
}));

// CORS configuration
app.use(cors({
  origin: process.env.ALLOWED_ORIGINS?.split(',') || ['http://localhost:3000'],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));

// Rate limiting
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // limit each IP to 100 requests per windowMs
  message: {
    error: 'Too many requests from this IP, please try again later.'
  },
  standardHeaders: true,
  legacyHeaders: false,
});

app.use('/api/', limiter);

// Compression
app.use(compression());

// Logging
app.use(morgan('combined', { stream: { write: message => logger.info(message.trim()) } }));

// Body parsing
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Authentication middleware for protected routes
app.use('/api/ai', authMiddleware);

// API Routes
app.use('/api/ai/sca-risk', scaRiskRoutes);
app.use('/api/ai/rtp-validator', rtpValidatorRoutes);
app.use('/api/ai/pcma-extractor', pcmaExtractorRoutes);
app.use('/api/ai/injury-prediction', injuryPredictionRoutes);
app.use('/api/ai/medical-notes', medicalNoteRoutes);
app.use('/api/ai/compliance', complianceRoutes);
app.use('/api/ai/whisper', whisperRoutes);
app.use('/api/ai/ocr', ocrRoutes);
app.use('/api/v1/med-gemini', medGeminiRoutes);
app.use('/health', healthRoutes);

// Root endpoint
app.get('/', (req, res) => {
  res.json({
    service: 'FIT Medical AI Service',
    version: '1.0.0',
    status: 'operational',
    description: 'FIFA-aligned clinical intelligence platform',
    endpoints: {
      'sca-risk': '/api/ai/sca-risk',
      'injury-prediction': '/api/ai/injury-prediction',
      'medical-notes': '/api/ai/medical-notes',
      'compliance': '/api/ai/compliance',
      'health': '/health'
    }
  });
});

// 404 handler
app.use('*', (req, res) => {
  res.status(404).json({
    error: 'Endpoint not found',
    message: `The requested endpoint ${req.originalUrl} does not exist.`,
    availableEndpoints: [
      '/api/ai/sca-risk',
      '/api/ai/injury-prediction',
      '/api/ai/medical-notes',
      '/api/ai/compliance',
      '/health'
    ]
  });
});

// Error handling middleware
app.use(errorHandler);

// Graceful shutdown
process.on('SIGTERM', () => {
  logger.info('SIGTERM received, shutting down gracefully');
  process.exit(0);
});

process.on('SIGINT', () => {
  logger.info('SIGINT received, shutting down gracefully');
  process.exit(0);
});

// Unhandled promise rejections
process.on('unhandledRejection', (reason, promise) => {
  logger.error('Unhandled Rejection at:', promise, 'reason:', reason);
  process.exit(1);
});

// Uncaught exceptions
process.on('uncaughtException', (error) => {
  logger.error('Uncaught Exception:', error);
  process.exit(1);
});

// Start server
app.listen(PORT, () => {
  logger.info(`FIT Medical AI Service running on port ${PORT}`);
  logger.info(`Environment: ${process.env.NODE_ENV || 'development'}`);
  logger.info(`Health check available at: http://localhost:${PORT}/health`);
});

module.exports = app; 