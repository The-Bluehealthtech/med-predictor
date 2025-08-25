const express = require('express');
const cors = require('cors');
const path = require('path');
require('dotenv').config();

// Configuration de l'application
const app = express();
const PORT = process.env.PORT || 8000;
const HOST = process.env.HOST || 'localhost';

// Middleware CORS
app.use(cors({
    origin: 'http://localhost:8000',
    credentials: true
}));

// Parsing des requêtes
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Servir les fichiers statiques
app.use(express.static(path.join(__dirname, 'public')));

// Route principale - servir l'interface HTML
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Route de santé
app.get('/health', (req, res) => {
    res.status(200).json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        version: '1.0.0',
        environment: 'development',
        uptime: process.uptime(),
        memory: process.memoryUsage(),
        message: 'FIT Service running in development mode'
    });
});

// API Routes avec préfixe
const apiPrefix = '/api';
const apiVersion = 'v1';
const basePath = `${apiPrefix}/${apiVersion}`;

// Route API de santé
app.get(`${basePath}/health`, (req, res) => {
    res.status(200).json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        service: 'FIT Service',
        version: '1.0.0',
        environment: 'development'
    });
});

// Routes OAuth2 simulées
app.get(`${basePath}/oauth2/providers`, (req, res) => {
    res.json({
        providers: [
            {
                id: 'catapult',
                name: 'Catapult Sports',
                description: 'Intégration avec les données Catapult',
                status: 'available',
                icon: '🏃‍♂️'
            },
            {
                id: 'apple',
                name: 'Apple HealthKit',
                description: 'Intégration avec Apple Health',
                status: 'available',
                icon: '🍎'
            },
            {
                id: 'garmin',
                name: 'Garmin Connect',
                description: 'Intégration avec Garmin',
                status: 'available',
                icon: '⌚'
            }
        ]
    });
});

// Routes des appareils simulées
app.get(`${basePath}/devices`, (req, res) => {
    res.json({
        devices: [
            {
                id: 'device-001',
                name: 'GPS Tracker Pro',
                type: 'gps',
                status: 'active',
                lastSync: new Date().toISOString(),
                data: {
                    location: { lat: 48.8566, lng: 2.3522 },
                    speed: 12.5,
                    distance: 1500
                }
            },
            {
                id: 'device-002',
                name: 'Heart Rate Monitor',
                type: 'heart_rate',
                status: 'active',
                lastSync: new Date().toISOString(),
                data: {
                    heartRate: 145,
                    maxHeartRate: 180,
                    avgHeartRate: 130
                }
            },
            {
                id: 'device-003',
                name: 'Accelerometer',
                type: 'accelerometer',
                status: 'active',
                lastSync: new Date().toISOString(),
                data: {
                    steps: 8500,
                    calories: 450,
                    activity: 'running'
                }
            }
        ]
    });
});

// Routes des données FIT simulées
app.get(`${basePath}/fit/data`, (req, res) => {
    res.json({
        playerId: 'player-001',
        sessionId: 'session-2025-07-24',
        timestamp: new Date().toISOString(),
        metrics: {
            distance: 8500,
            speed: 12.5,
            heartRate: {
                current: 145,
                average: 130,
                max: 180
            },
            acceleration: {
                current: 2.5,
                max: 4.2,
                average: 1.8
            },
            load: {
                total: 450,
                metabolic: 320,
                mechanical: 130
            }
        },
        zones: {
            heartRate: {
                zone1: 15,
                zone2: 25,
                zone3: 30,
                zone4: 20,
                zone5: 10
            },
            speed: {
                zone1: 20,
                zone2: 30,
                zone3: 25,
                zone4: 15,
                zone5: 10
            }
        }
    });
});

// Routes des prédictions médicales simulées
app.get(`${basePath}/predictions/health`, (req, res) => {
    res.json({
        playerId: 'player-001',
        timestamp: new Date().toISOString(),
        predictions: {
            injuryRisk: {
                overall: 0.15,
                category: 'low',
                factors: [
                    { factor: 'high_load_week', risk: 0.25 },
                    { factor: 'sleep_quality', risk: 0.10 },
                    { factor: 'recovery_time', risk: 0.20 }
                ]
            },
            performance: {
                predicted: 85,
                confidence: 0.92,
                trend: 'improving'
            },
            recommendations: [
                'Augmenter le temps de récupération',
                'Surveiller la charge d\'entraînement',
                'Améliorer la qualité du sommeil'
            ]
        }
    });
});

// Routes des analytics simulées
app.get(`${basePath}/analytics/performance`, (req, res) => {
    res.json({
        playerId: 'player-001',
        period: 'last_30_days',
        analytics: {
            totalDistance: 125000,
            totalTime: 7200,
            averageSpeed: 10.2,
            maxSpeed: 18.5,
            heartRateZones: {
                zone1: 25,
                zone2: 35,
                zone3: 25,
                zone4: 10,
                zone5: 5
            },
            loadDistribution: {
                low: 30,
                moderate: 45,
                high: 20,
                veryHigh: 5
            },
            trends: {
                distance: 'increasing',
                speed: 'stable',
                heartRate: 'decreasing'
            }
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

// Démarrage du serveur
const server = app.listen(PORT, HOST, () => {
    console.log(`🚀 Serveur FIT complet démarré sur http://${HOST}:${PORT}`);
    console.log(`📊 Health check: http://${HOST}:${PORT}/health`);
    console.log(`🔗 API: http://${HOST}:${PORT}${basePath}`);
    console.log(`🏥 Prédictions: http://${HOST}:${PORT}${basePath}/predictions/health`);
    console.log(`📈 Analytics: http://${HOST}:${PORT}${basePath}/analytics/performance`);
    console.log(`📱 Devices: http://${HOST}:${PORT}${basePath}/devices`);
    console.log(`🔐 OAuth2: http://${HOST}:${PORT}${basePath}/oauth2/providers`);
    console.log(`🌍 Environnement: development`);
});

// Gestion gracieuse de l'arrêt
process.on('SIGTERM', () => {
    console.log('🛑 Signal SIGTERM reçu, arrêt gracieux...');
    server.close(() => {
        console.log('✅ Serveur arrêté proprement');
        process.exit(0);
    });
});

process.on('SIGINT', () => {
    console.log('🛑 Signal SIGINT reçu, arrêt gracieux...');
    server.close(() => {
        console.log('✅ Serveur arrêté proprement');
        process.exit(0);
    });
});

module.exports = app;
