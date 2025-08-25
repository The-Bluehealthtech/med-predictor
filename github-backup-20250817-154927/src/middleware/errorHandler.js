const logger = require('../utils/logger');

class ErrorHandler {
  // Global error handling middleware
  handleError(error, req, res, next) {
    // Log the error
    logger.error('Unhandled error:', {
      error: error.message,
      stack: error.stack,
      url: req.url,
      method: req.method,
      userId: req.user?.id,
      userAgent: req.get('User-Agent'),
      ip: req.ip
    });

    // Determine error type and status code
    const { statusCode, message, details } = this.classifyError(error);

    // Send error response
    res.status(statusCode).json({
      success: false,
      error: message,
      details: process.env.NODE_ENV === 'development' ? details : undefined,
      timestamp: new Date().toISOString(),
      path: req.url
    });
  }

  // Classify errors and determine appropriate status codes
  classifyError(error) {
    // Validation errors
    if (error.name === 'ValidationError') {
      return {
        statusCode: 400,
        message: 'Validation failed',
        details: error.message
      };
    }

    // JWT errors
    if (error.name === 'JsonWebTokenError') {
      return {
        statusCode: 401,
        message: 'Invalid token',
        details: error.message
      };
    }

    if (error.name === 'TokenExpiredError') {
      return {
        statusCode: 401,
        message: 'Token expired',
        details: error.message
      };
    }

    // Database errors
    if (error.name === 'MongoError' || error.name === 'MongooseError') {
      if (error.code === 11000) {
        return {
          statusCode: 409,
          message: 'Duplicate entry',
          details: 'A record with this information already exists'
        };
      }
      return {
        statusCode: 500,
        message: 'Database error',
        details: error.message
      };
    }

    if (error.name === 'SequelizeValidationError') {
      return {
        statusCode: 400,
        message: 'Database validation failed',
        details: error.errors.map(e => e.message)
      };
    }

    if (error.name === 'SequelizeUniqueConstraintError') {
      return {
        statusCode: 409,
        message: 'Duplicate entry',
        details: 'A record with this information already exists'
      };
    }

    // Network/HTTP errors
    if (error.code === 'ECONNREFUSED') {
      return {
        statusCode: 503,
        message: 'Service unavailable',
        details: 'Unable to connect to external service'
      };
    }

    if (error.code === 'ETIMEDOUT') {
      return {
        statusCode: 504,
        message: 'Gateway timeout',
        details: 'Request to external service timed out'
      };
    }

    // OAuth2 specific errors
    if (error.message && error.message.includes('OAuth2')) {
      return {
        statusCode: 400,
        message: 'OAuth2 error',
        details: error.message
      };
    }

    // Rate limiting errors
    if (error.message && error.message.includes('rate limit')) {
      return {
        statusCode: 429,
        message: 'Too many requests',
        details: 'Rate limit exceeded, please try again later'
      };
    }

    // File upload errors
    if (error.code === 'LIMIT_FILE_SIZE') {
      return {
        statusCode: 413,
        message: 'File too large',
        details: 'Uploaded file exceeds maximum allowed size'
      };
    }

    if (error.code === 'LIMIT_UNEXPECTED_FILE') {
      return {
        statusCode: 400,
        message: 'Unexpected file field',
        details: 'File upload field name not expected'
      };
    }

    // Default error
    return {
      statusCode: 500,
      message: 'Internal server error',
      details: error.message
    };
  }

  // Handle 404 errors
  handleNotFound(req, res, next) {
    const error = new Error(`Route not found: ${req.method} ${req.url}`);
    error.statusCode = 404;
    next(error);
  }

  // Handle async errors
  asyncHandler(fn) {
    return (req, res, next) => {
      Promise.resolve(fn(req, res, next)).catch(next);
    };
  }

  // Handle specific error types
  handleValidationError(error) {
    return {
      statusCode: 400,
      message: 'Validation failed',
      details: error.details || error.message
    };
  }

  handleAuthenticationError(error) {
    return {
      statusCode: 401,
      message: 'Authentication failed',
      details: error.message
    };
  }

  handleAuthorizationError(error) {
    return {
      statusCode: 403,
      message: 'Access denied',
      details: error.message
    };
  }

  handleDatabaseError(error) {
    logger.error('Database error:', error);
    
    return {
      statusCode: 500,
      message: 'Database operation failed',
      details: process.env.NODE_ENV === 'development' ? error.message : 'Internal database error'
    };
  }

  handleExternalServiceError(error) {
    logger.error('External service error:', error);
    
    return {
      statusCode: 502,
      message: 'External service error',
      details: process.env.NODE_ENV === 'development' ? error.message : 'Service temporarily unavailable'
    };
  }

  // Custom error classes
  static ValidationError(message, details = null) {
    const error = new Error(message);
    error.name = 'ValidationError';
    error.statusCode = 400;
    error.details = details;
    return error;
  }

  static AuthenticationError(message) {
    const error = new Error(message);
    error.name = 'AuthenticationError';
    error.statusCode = 401;
    return error;
  }

  static AuthorizationError(message) {
    const error = new Error(message);
    error.name = 'AuthorizationError';
    error.statusCode = 403;
    return error;
  }

  static NotFoundError(message) {
    const error = new Error(message);
    error.name = 'NotFoundError';
    error.statusCode = 404;
    return error;
  }

  static ConflictError(message) {
    const error = new Error(message);
    error.name = 'ConflictError';
    error.statusCode = 409;
    return error;
  }

  static RateLimitError(message) {
    const error = new Error(message);
    error.name = 'RateLimitError';
    error.statusCode = 429;
    return error;
  }

  static DatabaseError(message) {
    const error = new Error(message);
    error.name = 'DatabaseError';
    error.statusCode = 500;
    return error;
  }

  static ExternalServiceError(message) {
    const error = new Error(message);
    error.name = 'ExternalServiceError';
    error.statusCode = 502;
    return error;
  }
}

// Export error handler instance and custom error classes
const errorHandler = new ErrorHandler();
module.exports = errorHandler.handleError.bind(errorHandler);
module.exports.ErrorHandler = ErrorHandler;
module.exports.errorHandler = errorHandler; 