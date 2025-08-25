const express = require('express');
const Joi = require('joi');
const router = express.Router();
const MedGeminiService = require('../services/medGeminiService');

// Initialize Med-Gemini service
const medGeminiService = new MedGeminiService();

// Validation schema for SCA risk assessment
const scaRiskSchema = Joi.object({
    ecgImage: Joi.string().required(), // Base64 encoded image
    clinicalHistory: Joi.string().required().max(5000),
    athleteId: Joi.number().integer().required(),
    hrv: Joi.number().optional(),
    age: Joi.number().integer().optional(),
    gender: Joi.string().valid('male', 'female').optional(),
    familyHistory: Joi.string().optional().max(1000)
});

/**
 * Assess SCA risk using Med-Gemini multi-modal analysis
 * POST /sca-risk-score
 */
router.post('/sca-risk-score', async (req, res) => {
    try {
        // Validate request
        const { error, value } = scaRiskSchema.validate(req.body);
        if (error) {
            return res.status(400).json({
                success: false,
                message: 'Invalid request data',
                error: error.details[0].message
            });
        }

        const { ecgImage, clinicalHistory, athleteId, hrv, age, gender, familyHistory } = value;
        
        console.log(`Assessing SCA risk for athlete ${athleteId}`);

        // Construct comprehensive prompt for Med-Gemini
        const prompt = constructSCARiskPrompt(clinicalHistory, hrv, age, gender, familyHistory);

        // Convert base64 image to buffer
        const imageBuffer = Buffer.from(ecgImage, 'base64');

        // Analyze ECG image with clinical context using Med-Gemini
        const result = await medGeminiService.analyzeMedicalImage(
            prompt, 
            imageBuffer, 
            'image/jpeg', 
            clinicalHistory
        );

        if (result.success) {
            // Parse the response to extract risk score and recommendations
            const analysis = parseSCARiskResponse(result.text);
            
            console.log(`Successfully assessed SCA risk for athlete ${athleteId}`);
            
            res.json({
                success: true,
                message: 'SCA risk assessment completed successfully',
                data: {
                    athleteId: athleteId,
                    riskScore: analysis.riskScore,
                    confidence: analysis.confidence,
                    recommendation: analysis.recommendation,
                    riskFactors: analysis.riskFactors,
                    ecgFindings: analysis.ecgFindings,
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
            console.error(`Failed to assess SCA risk for athlete ${athleteId}:`, result.error);
            
            res.status(500).json({
                success: false,
                message: 'Failed to assess SCA risk',
                error: result.error,
                metadata: {
                    model: result.model,
                    timestamp: result.timestamp
                }
            });
        }

    } catch (error) {
        console.error('Error assessing SCA risk:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error during SCA risk assessment',
            error: error.message
        });
    }
});

/**
 * Construct Med-Gemini prompt for SCA risk assessment
 * @param {string} clinicalHistory - Clinical history
 * @param {number} hrv - Heart rate variability
 * @param {number} age - Athlete age
 * @param {string} gender - Athlete gender
 * @param {string} familyHistory - Family history
 * @returns {string} - The constructed prompt
 */
function constructSCARiskPrompt(clinicalHistory, hrv, age, gender, familyHistory) {
    let prompt = `You are a sports cardiologist specializing in sudden cardiac arrest (SCA) risk assessment. 
    
Analyze the attached ECG image and the following clinical information to determine the SCA risk score (0-1, where 0 is no risk and 1 is highest risk), confidence level, and provide clinical recommendations.

Clinical Information:
- Clinical History: ${clinicalHistory}`;

    if (hrv) prompt += `\n- Heart Rate Variability: ${hrv} ms`;
    if (age) prompt += `\n- Age: ${age} years`;
    if (gender) prompt += `\n- Gender: ${gender}`;
    if (familyHistory) prompt += `\n- Family History: ${familyHistory}`;

    prompt += `

Please provide your analysis in the following JSON format:
{
  "riskScore": <number between 0 and 1>,
  "confidence": <number between 0 and 1>,
  "recommendation": <string with clinical recommendation>,
  "riskFactors": <array of identified risk factors>,
  "ecgFindings": <string describing ECG abnormalities or normal findings>
}

Focus on:
1. ECG rhythm analysis
2. ST segment changes
3. QRS complex abnormalities
4. QT interval assessment
5. Signs of structural heart disease
6. Arrhythmia detection
7. Clinical correlation with symptoms and history

Provide a comprehensive assessment suitable for sports medicine decision-making.`;

    return prompt;
}

/**
 * Parse Med-Gemini response for SCA risk assessment
 * @param {string} response - The model response
 * @returns {Object} - Parsed analysis results
 */
function parseSCARiskResponse(response) {
    try {
        // Try to extract JSON from the response
        const jsonMatch = response.match(/\{[\s\S]*\}/);
        if (jsonMatch) {
            const parsed = JSON.parse(jsonMatch[0]);
            return {
                riskScore: parsed.riskScore || 0.5,
                confidence: parsed.confidence || 0.7,
                recommendation: parsed.recommendation || 'Further evaluation recommended',
                riskFactors: parsed.riskFactors || [],
                ecgFindings: parsed.ecgFindings || 'ECG analysis completed'
            };
        }
    } catch (parseError) {
        console.warn('Failed to parse JSON from Med-Gemini response:', parseError.message);
    }

    // Fallback parsing if JSON extraction fails
    const riskScoreMatch = response.match(/risk.*?score.*?(\d*\.?\d+)/i);
    const confidenceMatch = response.match(/confidence.*?(\d*\.?\d+)/i);
    
    return {
        riskScore: riskScoreMatch ? parseFloat(riskScoreMatch[1]) : 0.5,
        confidence: confidenceMatch ? parseFloat(confidenceMatch[1]) : 0.7,
        recommendation: extractRecommendation(response),
        riskFactors: extractRiskFactors(response),
        ecgFindings: extractECGFindings(response)
    };
}

/**
 * Extract recommendation from text response
 * @param {string} response - The model response
 * @returns {string} - Extracted recommendation
 */
function extractRecommendation(response) {
    const recommendationKeywords = ['recommend', 'suggest', 'advise', 'should', 'must'];
    const sentences = response.split(/[.!?]+/);
    
    for (const sentence of sentences) {
        for (const keyword of recommendationKeywords) {
            if (sentence.toLowerCase().includes(keyword)) {
                return sentence.trim();
            }
        }
    }
    
    return 'Further evaluation recommended based on clinical assessment';
}

/**
 * Extract risk factors from text response
 * @param {string} response - The model response
 * @returns {Array} - Extracted risk factors
 */
function extractRiskFactors(response) {
    const riskFactorKeywords = [
        'arrhythmia', 'bradycardia', 'tachycardia', 'qt prolongation',
        'st elevation', 'st depression', 'qrs widening', 'bundle branch block',
        'family history', 'syncope', 'palpitations', 'chest pain'
    ];
    
    const foundFactors = [];
    const lowerResponse = response.toLowerCase();
    
    for (const factor of riskFactorKeywords) {
        if (lowerResponse.includes(factor)) {
            foundFactors.push(factor);
        }
    }
    
    return foundFactors;
}

/**
 * Extract ECG findings from text response
 * @param {string} response - The model response
 * @returns {string} - Extracted ECG findings
 */
function extractECGFindings(response) {
    const ecgKeywords = ['ecg', 'electrocardiogram', 'rhythm', 'st', 'qrs', 'qt', 'pr'];
    const sentences = response.split(/[.!?]+/);
    
    for (const sentence of sentences) {
        const lowerSentence = sentence.toLowerCase();
        for (const keyword of ecgKeywords) {
            if (lowerSentence.includes(keyword)) {
                return sentence.trim();
            }
        }
    }
    
    return 'ECG analysis completed - detailed findings in full report';
}

/**
 * Health check for SCA risk assessment service
 * GET /health
 */
router.get('/health', async (req, res) => {
    try {
        const healthStatus = await medGeminiService.healthCheck();
        res.json({
            service: 'SCA Risk Assessment',
            status: healthStatus.status,
            model: healthStatus.model,
            lastCheck: healthStatus.lastCheck,
            responseTime: healthStatus.responseTime
        });
    } catch (error) {
        res.status(500).json({
            service: 'SCA Risk Assessment',
            status: 'unhealthy',
            error: error.message
        });
    }
});

module.exports = router; 