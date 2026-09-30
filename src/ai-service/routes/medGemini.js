const express = require('express');
const router = express.Router();
const MedGeminiService = require('../services/medGeminiService');
const logger = require('../utils/logger');

// Initialize Med-Gemini service
const medGeminiService = new MedGeminiService();

/**
 * @route POST /api/v1/med-gemini/analyze
 * @desc Analyze medical images (ECG, MRI) using Med-Gemini AI
 * @access Public
 */
router.post('/analyze', async (req, res) => {
    const { analysis_type, file_content, file_type, prompt } = req.body;
    const textOnly = ['fitness_assessment', 'scat_analysis'].includes(analysis_type);
    if (!analysis_type || typeof prompt !== 'string' || !prompt.trim() || (!textOnly && !file_content)) {
        return res.status(422).json({ success: false, message: 'Missing analysis data' });
    }
    const mime = {jpg:'image/jpeg',jpeg:'image/jpeg',png:'image/png',pdf:'application/pdf'};
    if (!textOnly && !mime[file_type]) {
        return res.status(422).json({ success: false, message: 'Unsupported analysis file format' });
    }
    try {
        const result = textOnly ? await medGeminiService.analyze(prompt)
            : await medGeminiService.analyzeMedicalImage(prompt, Buffer.from(file_content, 'base64'),
                mime[file_type], analysis_type);
        const analysis = require('../utils/medicalResult').normalizeMedicalResult(result);
        return res.json({ success: true, analysis, model: result.model,
            requires_medical_review: true });
    } catch (error) {
        logger.warn('Structured medical analysis unavailable');
        return res.status(503).json({ success: false, message: 'AI analysis unavailable' });
    }
});

/**
 * @route POST /api/v1/med-gemini/analyze-ecg
 * @desc Analyze ECG specifically
 * @access Public
 */
router.post('/analyze-ecg', async (req, res) => {
    try {
        const { file_content, file_type } = req.body;

        if (!file_content) {
            return res.status(400).json({
                success: false,
                message: 'Missing file_content'
            });
        }

        const fileBuffer = Buffer.from(file_content, 'base64');
        
        const ecgPrompt = `Analyze this ECG image and provide detailed medical interpretation including: 
        - Rhythm (sinus, atrial fibrillation, etc.)
        - Heart rate in bpm
        - Any abnormalities (ST changes, T wave abnormalities, QRS complex analysis)
        - Clinical recommendations
        
        Format the response as JSON with fields: rhythm, heart_rate, abnormalities, recommendations.`;

        const analysisResult = await medGeminiService.analyzeMedicalImage(
            ecgPrompt,
            fileBuffer,
            `image/${file_type}`,
            'ecg'
        );

        return res.json({
            success: true,
            analysis: require('../utils/medicalResult').normalizeMedicalResult(analysisResult)
        });

    } catch (error) {
        logger.error(`ECG analysis error: ${error.message}`);
        
        return res.status(503).json({
            success: false,
            message: 'Error during ECG analysis',
            error: error.message
        });
    }
});

/**
 * @route POST /api/v1/med-gemini/analyze-mri-bone-age
 * @desc Analyze MRI for bone age assessment
 * @access Public
 */
router.post('/analyze-mri-bone-age', async (req, res) => {
    try {
        const { file_content, file_type } = req.body;

        if (!file_content) {
            return res.status(400).json({
                success: false,
                message: 'Missing file_content'
            });
        }

        const fileBuffer = Buffer.from(file_content, 'base64');
        
        const mriPrompt = `Analyze this MRI image for bone age assessment. Evaluate:
        - Skeletal maturity
        - Estimated bone age
        - Comparison with chronological age
        - Any growth abnormalities
        - Clinical recommendations
        
        Format the response as JSON with fields: bone_age, chronological_age, age_difference, skeletal_maturity, abnormalities, recommendations.`;

        const analysisResult = await medGeminiService.analyzeMedicalImage(
            mriPrompt,
            fileBuffer,
            `image/${file_type}`,
            'mri_bone_age'
        );

        return res.json({
            success: true,
            analysis: require('../utils/medicalResult').normalizeMedicalResult(analysisResult)
        });

    } catch (error) {
        logger.error(`MRI bone age analysis error: ${error.message}`);
        
        return res.status(503).json({
            success: false,
            message: 'Error during MRI bone age analysis',
            error: error.message
        });
    }
});

/**
 * @route GET /api/v1/med-gemini/health
 * @desc Health check for Med-Gemini service
 * @access Public
 */
router.get('/health', (req, res) => {
    return res.json({
        success: true,
        service: 'Med-Gemini AI Service',
        status: 'healthy',
        timestamp: new Date().toISOString()
    });
});

module.exports = router;
