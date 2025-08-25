const express = require('express');
const Joi = require('joi');
const router = express.Router();
const MedGeminiService = require('../services/medGeminiService');

// Initialize Med-Gemini service
const medGeminiService = new MedGeminiService();

// Validation schema for PCMA data extraction
const pcmaExtractionSchema = Joi.object({
    transcript: Joi.string().required().max(10000),
    athlete_id: Joi.number().integer().required(),
    pcma_type: Joi.string().valid('cardio', 'neurological', 'musculoskeletal', 'general').required()
});

/**
 * Extract structured PCMA data from transcript using Med-Gemini
 * POST /extract-pcma-data
 */
router.post('/extract-pcma-data', async (req, res) => {
    try {
        // Validate request
        const { error, value } = pcmaExtractionSchema.validate(req.body);
        if (error) {
            return res.status(400).json({
                success: false,
                message: 'Invalid request data',
                error: error.details[0].message
            });
        }

        const { transcript, athlete_id, pcma_type } = value;
        
        console.log(`Extracting PCMA data for athlete ${athlete_id}, type: ${pcma_type}`);

        // Construct Med-Gemini prompt based on PCMA type
        const prompt = constructPCMAPrompt(transcript, pcma_type);

        // Extract structured data using Med-Gemini
        const result = await medGeminiService.extractStructuredData(prompt, transcript);

        if (result.success) {
            const extractedData = result.structuredData || {};
            const confidenceScore = result.confidence || 0.7;
            const extractedFields = Object.keys(extractedData);

            console.log(`Successfully extracted ${extractedFields.length} fields with confidence: ${confidenceScore}`);

            res.json({
                ...extractedData,
                confidence_score: confidenceScore,
                extracted_fields: extractedFields,
                processing_time: result.processingTime,
                model: result.model,
                timestamp: result.timestamp
            });
        } else {
            console.error(`Failed to extract PCMA data for athlete ${athlete_id}:`, result.error);
            
            res.status(500).json({
                success: false,
                message: 'Failed to extract PCMA data from transcript',
                error: result.error,
                model: result.model,
                timestamp: result.timestamp
            });
        }

    } catch (error) {
        console.error('Error extracting PCMA data:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error during data extraction',
            error: error.message
        });
    }
});

/**
 * Construct Med-Gemini prompt based on PCMA type
 * @param {string} transcript - The clinical transcript
 * @param {string} pcmaType - The type of PCMA assessment
 * @returns {string} - The constructed prompt
 */
function constructPCMAPrompt(transcript, pcmaType) {
    const basePrompt = `You are a sports medicine specialist performing a Pre-Competition Medical Assessment (PCMA). 
    
Based on the following clinical transcript, extract structured clinical data and return it as a valid JSON object.

Clinical Transcript: "${transcript}"

Please extract the relevant clinical data and return ONLY a JSON object with the following structure:`;

    switch (pcmaType) {
        case 'cardio':
            return `${basePrompt}
{
  "bp_systolic": <number or null>,
  "bp_diastolic": <number or null>,
  "heart_rate": <number or null>,
  "resting_heart_rate": <number or null>,
  "bp_category": <"normal", "elevated", "stage1_hypertension", "stage2_hypertension" or null>,
  "cardiovascular_symptoms": <array of symptoms or empty array>,
  "family_history_notes": <string or null>,
  "current_medications": <string or null>
}

Extract blood pressure values, heart rate measurements, cardiovascular symptoms, family history, and current medications. If a value is not mentioned, use null.`;

        case 'neurological':
            return `${basePrompt}
{
  "mental_status": <"alert_and_oriented", "confused", "lethargic" or null>,
  "glasgow_coma_scale": <number 3-15 or null>,
  "pupillary_response": <"equal_and_reactive", "unequal", "fixed" or null>,
  "motor_function": <"normal", "weakness", "paralysis" or null>,
  "sensory_function": <"normal", "numbness", "tingling" or null>,
  "neurological_symptoms": <array of symptoms or empty array>
}

Extract mental status, Glasgow Coma Scale, pupillary response, motor function, sensory function, and neurological symptoms. If a value is not mentioned, use null.`;

        case 'musculoskeletal':
            return `${basePrompt}
{
  "range_of_motion": <"full", "limited", "none" or null>,
  "strength_grade": <number 0-5 or null>,
  "pain_level": <number 0-10 or null>,
  "joint_stability": <"stable", "unstable" or null>,
  "injury_type": <"sprain", "strain", "fracture", "dislocation" or null>,
  "affected_body_parts": <array of body parts or empty array>
}

Extract range of motion, strength assessment, pain level, joint stability, injury type, and affected body parts. If a value is not mentioned, use null.`;

        case 'general':
            return `${basePrompt}
{
  "temperature": <number or null>,
  "weight": <number or null>,
  "height": <number or null>,
  "bmi": <number or null>,
  "allergies": <string or null>,
  "general_symptoms": <array of symptoms or empty array>
}

Extract vital signs, anthropometric measurements, allergies, and general symptoms. If a value is not mentioned, use null.`;

        default:
            return `${basePrompt}
{
  "clinical_findings": <string or null>,
  "recommendations": <string or null>
}

Extract any clinical findings and recommendations mentioned in the transcript.`;
    }
}

/**
 * Health check for PCMA extraction service
 * GET /health
 */
router.get('/health', async (req, res) => {
    try {
        const healthStatus = await medGeminiService.healthCheck();
        res.json({
            service: 'PCMA Data Extraction',
            status: healthStatus.status,
            model: healthStatus.model,
            lastCheck: healthStatus.lastCheck,
            responseTime: healthStatus.responseTime
        });
    } catch (error) {
        res.status(500).json({
            service: 'PCMA Data Extraction',
            status: 'unhealthy',
            error: error.message
        });
    }
});

module.exports = router; 