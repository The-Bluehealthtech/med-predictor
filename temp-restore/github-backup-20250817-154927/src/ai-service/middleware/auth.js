const logger = require('../utils/logger');

const authMiddleware = (req, res, next) => {
    // For testing purposes, allow all requests
    // In production, implement proper authentication
    logger.info('Authentication middleware - allowing request', {
        url: req.url,
        method: req.method,
        ip: req.ip
    });
    
    next();
};

module.exports = authMiddleware; 