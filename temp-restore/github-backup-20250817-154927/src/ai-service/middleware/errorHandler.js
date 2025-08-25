const logger = require('../utils/logger');

const errorHandler = (err, req, res, next) => {
    logger.error('Error occurred:', {
        error: err.message,
        stack: err.stack,
        url: req.url,
        method: req.method,
        ip: req.ip
    });

    // Default error
    let error = {
        message: 'Internal Server Error',
        status: 500
    };

    // Handle specific error types
    if (err.name === 'ValidationError') {
        error.message = 'Validation Error';
        error.status = 400;
        error.details = err.details;
    } else if (err.name === 'UnauthorizedError') {
        error.message = 'Unauthorized';
        error.status = 401;
    } else if (err.name === 'ForbiddenError') {
        error.message = 'Forbidden';
        error.status = 403;
    } else if (err.name === 'NotFoundError') {
        error.message = 'Not Found';
        error.status = 404;
    } else if (err.code === 'ECONNREFUSED') {
        error.message = 'Service Unavailable';
        error.status = 503;
    }

    res.status(error.status).json({
        success: false,
        error: error.message,
        ...(process.env.NODE_ENV === 'development' && { stack: err.stack })
    });
};

module.exports = errorHandler; 