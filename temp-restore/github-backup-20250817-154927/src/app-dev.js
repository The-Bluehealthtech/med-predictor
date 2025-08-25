const express = require('express');
const cors = require('cors');
const path = require('path');
require('dotenv').config();

// Configuration de l'application
const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || 'localhost';

// Middleware CORS simplifié
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

// Route de santé simplifiée
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

// Route API simplifiée
app.get('/api/health', (req, res) => {
    res.status(200).json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        service: 'FIT Service',
        version: '1.0.0',
        environment: 'development'
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
    console.log(`🚀 Serveur FIT démarré en mode développement sur http://${HOST}:${PORT}`);
    console.log(`📊 Health check: http://${HOST}:${PORT}/health`);
    console.log(`🔗 API: http://${HOST}:${PORT}/api/health`);
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
