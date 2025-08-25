const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const morgan = require('morgan');
const path = require('path');
const mongoose = require('mongoose');
require('dotenv').config();

// Import des configurations
const database = require('./config/database');
const oauth2Config = require('./config/oauth2');

// Import des middlewares
const errorHandler = require('./middleware/errorHandler');
const authMiddleware = require('./middleware/auth');

// Import des services
const DataSyncService = require('./services/DataSyncService');

// Configuration de l'application
const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || '0.0.0.0';

// Middleware de sécurité
if (process.env.HELMET_ENABLED !== 'false') {
    app.use(helmet({
        contentSecurityPolicy: process.env.HELMET_CONTENT_SECURITY_POLICY !== 'false' ? {
            directives: {
                defaultSrc: ["'self'"],
                styleSrc: ["'self'", "'unsafe-inline'"],
                scriptSrc: ["'self'"],
                imgSrc: ["'self'", "data:", "https:"],
                connectSrc: ["'self'"],
                fontSrc: ["'self'"],
                objectSrc: ["'none'"],
                mediaSrc: ["'self'"],
                frameSrc: ["'none'"],
            },
        } : false,
    }));
}

// Configuration CORS
const corsOptions = {
    origin: process.env.CORS_ORIGIN || 'http://localhost:8000',
    credentials: process.env.CORS_CREDENTIALS === 'true',
    maxAge: parseInt(process.env.CORS_MAX_AGE) || 86400,
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
};
app.use(cors(corsOptions));

// Rate limiting
const limiter = rateLimit({
    windowMs: parseInt(process.env.RATE_LIMIT_WINDOW_MS) || 15 * 60 * 1000, // 15 minutes
    max: parseInt(process.env.RATE_LIMIT_MAX_REQUESTS) || 100, // limite par IP
    skipSuccessfulRequests: process.env.RATE_LIMIT_SKIP_SUCCESSFUL_REQUESTS === 'true',
    message: {
        error: 'Trop de requêtes, veuillez réessayer plus tard.',
        retryAfter: Math.ceil(parseInt(process.env.RATE_LIMIT_WINDOW_MS) / 1000 / 60)
    }
});
app.use(limiter);

// Logging
if (process.env.NODE_ENV !== 'test') {
    app.use(morgan('combined', {
        stream: {
            write: (message) => {
                console.log(message.trim());
            }
        }
    }));
}

// Parsing des requêtes
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Trust proxy (pour les headers X-Forwarded-*)
if (process.env.TRUST_PROXY === 'true') {
    app.set('trust proxy', 1);
}

// Routes publiques
app.get('/health', (req, res) => {
    res.status(200).json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        version: process.env.npm_package_version || '1.0.0',
        environment: process.env.NODE_ENV || 'development',
        uptime: process.uptime(),
        memory: process.memoryUsage(),
        databases: {
            mongodb: database.mongoStatus,
            postgresql: database.postgresStatus,
            redis: database.redisStatus
        }
    });
});

app.get('/metrics', (req, res) => {
    res.status(200).json({
        timestamp: new Date().toISOString(),
        uptime: process.uptime(),
        memory: process.memoryUsage(),
        cpu: process.cpuUsage(),
        databases: {
            mongodb: database.mongoStatus,
            postgresql: database.postgresStatus,
            redis: database.redisStatus
        }
    });
});

// Route 404 pour les endpoints non trouvés
app.use('*', (req, res) => {
    res.status(404).json({
        error: 'Endpoint non trouvé',
        path: req.originalUrl,
        method: req.method,
        timestamp: new Date().toISOString()
    });
});

// Middleware de gestion d'erreurs (doit être en dernier)
app.use(errorHandler);

// Fonction de démarrage du serveur
async function startServer() {
    try {
        // Connexion aux bases de données
        await database.connectAll();
        
        // Définir sequelize pour les modèles
        database.sequelize = database.postgresConnection;
        
        // Définir les statuts pour les endpoints de santé
        database.mongoStatus = mongoose.connection.readyState === 1;
        database.postgresStatus = database.postgresConnection ? true : false;
        database.redisStatus = database.redisClient ? true : false;
        
        console.log('✅ Connexion aux bases de données établie');

        // Chargement des routes après connexion à la base de données
        const oauth2Routes = require('./routes/oauth2');
        const deviceRoutes = require('./routes/devices');
        
        // API Routes avec préfixe
        const apiPrefix = process.env.API_PREFIX || '/api';
        const apiVersion = process.env.API_VERSION || 'v1';
        const basePath = `${apiPrefix}/${apiVersion}`;

        // Routes OAuth2
        app.use(`${basePath}/oauth2`, oauth2Routes);

        // Routes des appareils (protégées par authentification)
        app.use(`${basePath}/devices`, authMiddleware.authenticate, deviceRoutes);

        // Initialisation des services
        const dataSyncService = new DataSyncService();
        console.log('✅ Services initialisés');

        // Démarrage du serveur
        const server = app.listen(PORT, HOST, () => {
            console.log(`🚀 Serveur FIT démarré sur http://${HOST}:${PORT}`);
            console.log(`📊 Health check: http://${HOST}:${PORT}/health`);
            console.log(`📈 Métriques: http://${HOST}:${PORT}/metrics`);
            console.log(`🔗 API: http://${HOST}:${PORT}${basePath}`);
            console.log(`🌍 Environnement: ${process.env.NODE_ENV || 'development'}`);
        });

        // Gestion gracieuse de l'arrêt
        process.on('SIGTERM', () => {
            console.log('🛑 Signal SIGTERM reçu, arrêt gracieux...');
            server.close(async () => {
                await database.disconnectAll();
                console.log('✅ Serveur arrêté proprement');
                process.exit(0);
            });
        });

        process.on('SIGINT', () => {
            console.log('🛑 Signal SIGINT reçu, arrêt gracieux...');
            server.close(async () => {
                await database.disconnectAll();
                console.log('✅ Serveur arrêté proprement');
                process.exit(0);
            });
        });

    } catch (error) {
        console.error('❌ Erreur lors du démarrage du serveur:', error);
        process.exit(1);
    }
}

// Démarrage du serveur si le fichier est exécuté directement
if (require.main === module) {
    startServer();
}

module.exports = app; 