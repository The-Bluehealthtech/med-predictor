const express = require('express');
const router = express.Router();

router.get('/', (req, res) => {
    res.json({
        service: 'FIT Medical AI Service',
        status: 'healthy',
        timestamp: new Date().toISOString(),
        version: '1.0.0'
    });
});

module.exports = router; 