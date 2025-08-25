const express = require('express');
const Joi = require('joi');
const router = express.Router();
const MedGeminiService = require('../services/medGeminiService');

// Initialize Med-Gemini service
const medGeminiService = new MedGeminiService();

// Validation schema for medical note generation
const medicalNoteSchema = Joi.object({
    transcript: Joi.string().required().max(10000),
    noteType: Joi.string().valid('SOAP', 'progress', 'consultation', 'discharge').default('SOAP'),
    athleteId: Joi.number().integer().required(),
    context: Joi.string().optional().max(2000)
});

/**
 * Generate medical note from transcript
 * POST /generate-note
 */
router.post('/generate-note', async (req, res) => {
    try {
        // Validate request
        const { error, value } = medicalNoteSchema.validate(req.body);
        if (error) {
            return res.status(400).json({
                success: false,
                message: 'Invalid request data',
                error: error.details[0].message
            });
        }

        const { transcript, noteType, athleteId, context } = value;
        
        console.log(`Generating ${noteType} note for athlete ${athleteId}`);

        // Construct enhanced prompt with context
        let enhancedTranscript = transcript;
        if (context) {
            enhancedTranscript = `Context: ${context}\n\nClinical Transcript: ${transcript}`;
        }

        // Generate medical note using Med-Gemini
        const result = await medGeminiService.generateMedicalNote(enhancedTranscript, noteType);

        if (result.success) {
            console.log(`Successfully generated ${noteType} note for athlete ${athleteId}`);
            
            res.json({
                success: true,
                message: `${noteType} note generated successfully`,
                data: {
                    note: result.text,
                    noteType: noteType,
                    athleteId: athleteId,
                    confidence: result.confidence || 0.8,
                    processingTime: result.processingTime,
                    model: result.model
                },
                metadata: {
                    model: result.model,
                    processingTime: result.processingTime,
                    timestamp: result.timestamp
                }
            });
        } else {
            console.error(`Failed to generate ${noteType} note for athlete ${athleteId}:`, result.error);
            
            res.status(500).json({
                success: false,
                message: 'Failed to generate medical note',
                error: result.error,
                metadata: {
                    model: result.model,
                    timestamp: result.timestamp
                }
            });
        }

    } catch (error) {
        console.error('Error generating medical note:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error during note generation',
            error: error.message
        });
    }
});

/**
 * Generate structured medical note with specific sections
 * POST /generate-structured-note
 */
router.post('/generate-structured-note', async (req, res) => {
    try {
        const { transcript, sections, athleteId } = req.body;
        
        if (!transcript || !sections || !athleteId) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: transcript, sections, athleteId'
            });
        }

        const prompt = `You are a sports medicine specialist. Based on the following clinical encounter transcript, generate a structured medical note with the specified sections.

Clinical Transcript: "${transcript}"

Required Sections: ${sections.join(', ')}

Please provide a well-structured medical note with each section clearly labeled. Format the response as a professional medical note.`;

        const result = await medGeminiService.analyze(prompt, { text: transcript });

        if (result.success) {
            res.json({
                success: true,
                message: 'Structured medical note generated successfully',
                data: {
                    note: result.text,
                    sections: sections,
                    athleteId: athleteId,
                    confidence: result.confidence || 0.8,
                    processingTime: result.processingTime,
                    model: result.model
                },
                metadata: {
                    model: result.model,
                    processingTime: result.processingTime,
                    timestamp: result.timestamp
                }
            });
        } else {
            res.status(500).json({
                success: false,
                message: 'Failed to generate structured medical note',
                error: result.error,
                metadata: {
                    model: result.model,
                    timestamp: result.timestamp
                }
            });
        }

    } catch (error) {
        console.error('Error generating structured medical note:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error during structured note generation',
            error: error.message
        });
    }
});

/**
 * Health check for medical note service
 * GET /health
 */
router.get('/health', async (req, res) => {
    try {
        const healthStatus = await medGeminiService.healthCheck();
        res.json({
            service: 'Medical Note Generation',
            status: healthStatus.status,
            model: healthStatus.model,
            lastCheck: healthStatus.lastCheck,
            responseTime: healthStatus.responseTime
        });
    } catch (error) {
        res.status(500).json({
            service: 'Medical Note Generation',
            status: 'unhealthy',
            error: error.message
        });
    }
});

module.exports = router; 