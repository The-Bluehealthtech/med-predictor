const winston = require('winston');
const path = require('path');

// Configuration des niveaux de log
const logLevels = {
    error: 0,
    warn: 1,
    info: 2,
    http: 3,
    debug: 4,
};

// Configuration des couleurs pour les logs
const logColors = {
    error: 'red',
    warn: 'yellow',
    info: 'green',
    http: 'magenta',
    debug: 'white',
};

winston.addColors(logColors);

// Format personnalisé pour les logs
const logFormat = winston.format.combine(
    winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss:ms' }),
    winston.format.colorize({ all: true }),
    winston.format.printf(
        (info) => `${info.timestamp} ${info.level}: ${info.message}`,
    ),
);

// Format pour les fichiers (sans couleurs)
const fileLogFormat = winston.format.combine(
    winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss:ms' }),
    winston.format.errors({ stack: true }),
    winston.format.json(),
);

// Configuration des transports
const transports = [
    // Console transport
    new winston.transports.Console({
        format: logFormat,
        level: process.env.LOG_LEVEL || 'info',
    }),
];

// Ajout du transport fichier si configuré
if (process.env.LOG_TO_FILE === 'true') {
    transports.push(
        new winston.transports.File({
            filename: path.join('logs', 'error.log'),
            level: 'error',
            format: fileLogFormat,
            maxsize: 5242880, // 5MB
            maxFiles: 5,
        }),
        new winston.transports.File({
            filename: path.join('logs', 'combined.log'),
            format: fileLogFormat,
            maxsize: 5242880, // 5MB
            maxFiles: 5,
        })
    );
}

// Création du logger
const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    levels: logLevels,
    format: fileLogFormat,
    transports,
    exitOnError: false,
});

// Middleware pour Express
const expressLogger = (req, res, next) => {
    const start = Date.now();
    
    res.on('finish', () => {
        const duration = Date.now() - start;
        const logMessage = `${req.method} ${req.originalUrl} ${res.statusCode} ${duration}ms`;
        
        if (res.statusCode >= 400) {
            logger.warn(logMessage);
        } else {
            logger.http(logMessage);
        }
    });
    
    next();
};

// Fonction pour logger les erreurs avec contexte
const logError = (error, context = {}) => {
    logger.error({
        message: error.message,
        stack: error.stack,
        context,
        timestamp: new Date().toISOString(),
    });
};

// Fonction pour logger les événements d'audit
const logAudit = (action, userId, details = {}) => {
    logger.info({
        type: 'AUDIT',
        action,
        userId,
        details,
        timestamp: new Date().toISOString(),
        ip: details.ip || 'unknown',
        userAgent: details.userAgent || 'unknown',
    });
};

// Fonction pour logger les performances
const logPerformance = (operation, duration, metadata = {}) => {
    logger.info({
        type: 'PERFORMANCE',
        operation,
        duration,
        metadata,
        timestamp: new Date().toISOString(),
    });
};

// Fonction pour logger les événements de sécurité
const logSecurity = (event, details = {}) => {
    logger.warn({
        type: 'SECURITY',
        event,
        details,
        timestamp: new Date().toISOString(),
        ip: details.ip || 'unknown',
        userAgent: details.userAgent || 'unknown',
    });
};

module.exports = {
    logger,
    expressLogger,
    logError,
    logAudit,
    logPerformance,
    logSecurity,
}; 