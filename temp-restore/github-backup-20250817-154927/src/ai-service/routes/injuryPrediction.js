const express = require('express');
const router = express.Router();

router.post('/predict', (req, res) => {
    res.json({
        success: true,
        message: 'Injury prediction endpoint - coming soon',
        data: {
            riskScore: 0.3,
            confidence: 0.8,
            recommendations: ['Continue monitoring', 'Maintain current training load']
        }
    });
});

module.exports = router; 