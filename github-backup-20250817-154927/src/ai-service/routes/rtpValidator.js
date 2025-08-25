const express = require('express');
const Joi = require('joi');
const router = express.Router();
const MedGeminiService = require('../services/medGeminiService');

// Initialize Med-Gemini service
const medGeminiService = new MedGeminiService();

// Validation schema for RTP validation
const rtpValidationSchema = Joi.object({
    injuryType: Joi.string().required().max(200),
    daysInRehab: Joi.number().integer().min(0).required(),
    rehabData: Joi.object({
        painLevel: Joi.number().min(0).max(10).optional(),
        rangeOfMotion: Joi.string().valid('full', 'limited', 'none').optional(),
        strengthGrade: Joi.number().min(0).max(5).optional(),
        functionalTests: Joi.array().items(Joi.string()).optional(),
        imagingResults: Joi.string().optional(),
        symptoms: Joi.array().items(Joi.string()).optional()
    }).optional(),
    performanceMetrics: Joi.object({
        heartRate: Joi.number().optional(),
        bloodPressure: Joi.object({
            systolic: Joi.number().optional(),
            diastolic: Joi.number().optional()
        }).optional(),
        balanceScore: Joi.number().min(0).max(100).optional(),
        agilityScore: Joi.number().min(0).max(100).optional(),
        enduranceScore: Joi.number().min(0).max(100).optional()
    }).optional(),
    athleteId: Joi.number().integer().required(),
    clinicalNotes: Joi.string().optional().max(2000)
});

/**
 * Validate Return-to-Play readiness using Med-Gemini
 * POST /rtp-validator
 */
router.post('/rtp-validator', async (req, res) => {
    try {
        // Validate request
        const { error, value } = rtpValidationSchema.validate(req.body);
        if (error) {
            return res.status(400).json({
                success: false,
                message: 'Invalid request data',
                error: error.details[0].message
            });
        }

        const { injuryType, daysInRehab, rehabData, performanceMetrics, athleteId, clinicalNotes } = value;
        
        console.log(`Validating RTP for athlete ${athleteId}, injury: ${injuryType}`);

        // Construct comprehensive prompt for Med-Gemini
        const prompt = constructRTPPrompt(injuryType, daysInRehab, rehabData, performanceMetrics, clinicalNotes);

        // Analyze using Med-Gemini
        const result = await medGeminiService.analyze(prompt, { 
            text: JSON.stringify({ injuryType, daysInRehab, rehabData, performanceMetrics, clinicalNotes })
        });

        if (result.success) {
            // Parse the response to extract RTP assessment
            const assessment = parseRTPResponse(result.text);
            
            console.log(`Successfully validated RTP for athlete ${athleteId}`);
            
            res.json({
                success: true,
                message: 'RTP validation completed successfully',
                data: {
                    athleteId: athleteId,
                    status: assessment.status,
                    confidence: assessment.confidence,
                    recommendation: assessment.recommendation,
                    riskAssessment: assessment.riskAssessment,
                    nextSteps: assessment.nextSteps,
                    functionalReadiness: assessment.functionalReadiness,
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
            console.error(`Failed to validate RTP for athlete ${athleteId}:`, result.error);
            
            res.status(500).json({
                success: false,
                message: 'Failed to validate RTP',
                error: result.error,
                metadata: {
                    model: result.model,
                    timestamp: result.timestamp
                }
            });
        }

    } catch (error) {
        console.error('Error validating RTP:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error during RTP validation',
            error: error.message
        });
    }
});

/**
 * Construct Med-Gemini prompt for RTP validation
 * @param {string} injuryType - Type of injury
 * @param {number} daysInRehab - Days in rehabilitation
 * @param {Object} rehabData - Rehabilitation data
 * @param {Object} performanceMetrics - Performance metrics
 * @param {string} clinicalNotes - Clinical notes
 * @returns {string} - The constructed prompt
 */
function constructRTPPrompt(injuryType, daysInRehab, rehabData, performanceMetrics, clinicalNotes) {
    let prompt = `You are a sports medicine specialist conducting a Return-to-Play (RTP) assessment. 
    
Based on the following clinical data, determine if the athlete is ready to return to play, assess the risk level, and provide specific recommendations.

Clinical Data:
- Injury Type: ${injuryType}
- Days in Rehabilitation: ${daysInRehab} days`;

    if (rehabData) {
        prompt += `\n- Rehabilitation Data:`;
        if (rehabData.painLevel !== undefined) prompt += `\n  * Pain Level: ${rehabData.painLevel}/10`;
        if (rehabData.rangeOfMotion) prompt += `\n  * Range of Motion: ${rehabData.rangeOfMotion}`;
        if (rehabData.strengthGrade !== undefined) prompt += `\n  * Strength Grade: ${rehabData.strengthGrade}/5`;
        if (rehabData.functionalTests && rehabData.functionalTests.length > 0) {
            prompt += `\n  * Functional Tests: ${rehabData.functionalTests.join(', ')}`;
        }
        if (rehabData.imagingResults) prompt += `\n  * Imaging Results: ${rehabData.imagingResults}`;
        if (rehabData.symptoms && rehabData.symptoms.length > 0) {
            prompt += `\n  * Symptoms: ${rehabData.symptoms.join(', ')}`;
        }
    }

    if (performanceMetrics) {
        prompt += `\n- Performance Metrics:`;
        if (performanceMetrics.heartRate) prompt += `\n  * Heart Rate: ${performanceMetrics.heartRate} bpm`;
        if (performanceMetrics.bloodPressure) {
            prompt += `\n  * Blood Pressure: ${performanceMetrics.bloodPressure.systolic}/${performanceMetrics.bloodPressure.diastolic} mmHg`;
        }
        if (performanceMetrics.balanceScore !== undefined) prompt += `\n  * Balance Score: ${performanceMetrics.balanceScore}/100`;
        if (performanceMetrics.agilityScore !== undefined) prompt += `\n  * Agility Score: ${performanceMetrics.agilityScore}/100`;
        if (performanceMetrics.enduranceScore !== undefined) prompt += `\n  * Endurance Score: ${performanceMetrics.enduranceScore}/100`;
    }

    if (clinicalNotes) {
        prompt += `\n- Clinical Notes: ${clinicalNotes}`;
    }

    prompt += `

Please provide your assessment in the following JSON format:
{
  "status": <"Ready", "Conditional", "At Risk", "Not Ready">,
  "confidence": <number between 0 and 1>,
  "recommendation": <string with specific clinical recommendation>,
  "riskAssessment": <string describing risk factors and concerns>,
  "nextSteps": <array of specific next steps or requirements>,
  "functionalReadiness": <object with readiness scores for different aspects>
}

Consider:
1. Injury-specific healing timelines
2. Functional capacity restoration
3. Risk of re-injury
4. Sport-specific demands
5. Psychological readiness
6. Medical clearance requirements

Provide a comprehensive assessment suitable for sports medicine decision-making.`;

    return prompt;
}

/**
 * Parse Med-Gemini response for RTP validation
 * @param {string} response - The model response
 * @returns {Object} - Parsed RTP assessment
 */
function parseRTPResponse(response) {
    try {
        // Try to extract JSON from the response
        const jsonMatch = response.match(/\{[\s\S]*\}/);
        if (jsonMatch) {
            const parsed = JSON.parse(jsonMatch[0]);
            return {
                status: parsed.status || 'Conditional',
                confidence: parsed.confidence || 0.7,
                recommendation: parsed.recommendation || 'Further evaluation recommended',
                riskAssessment: parsed.riskAssessment || 'Standard risk assessment completed',
                nextSteps: parsed.nextSteps || ['Continue monitoring'],
                functionalReadiness: parsed.functionalReadiness || {}
            };
        }
    } catch (parseError) {
        console.warn('Failed to parse JSON from Med-Gemini response:', parseError.message);
    }

    // Fallback parsing if JSON extraction fails
    const statusMatch = response.match(/(ready|conditional|at risk|not ready)/i);
    const confidenceMatch = response.match(/confidence.*?(\d*\.?\d+)/i);
    
    return {
        status: statusMatch ? statusMatch[1].toLowerCase() : 'Conditional',
        confidence: confidenceMatch ? parseFloat(confidenceMatch[1]) : 0.7,
        recommendation: extractRTPRecommendation(response),
        riskAssessment: extractRiskAssessment(response),
        nextSteps: extractNextSteps(response),
        functionalReadiness: {}
    };
}

/**
 * Extract RTP recommendation from text response
 * @param {string} response - The model response
 * @returns {string} - Extracted recommendation
 */
function extractRTPRecommendation(response) {
    const recommendationKeywords = ['recommend', 'suggest', 'advise', 'should', 'must', 'clear', 'return'];
    const sentences = response.split(/[.!?]+/);
    
    for (const sentence of sentences) {
        for (const keyword of recommendationKeywords) {
            if (sentence.toLowerCase().includes(keyword)) {
                return sentence.trim();
            }
        }
    }
    
    return 'Further evaluation recommended before return to play';
}

/**
 * Extract risk assessment from text response
 * @param {string} response - The model response
 * @returns {string} - Extracted risk assessment
 */
function extractRiskAssessment(response) {
    const riskKeywords = ['risk', 'concern', 'caution', 'warning', 'danger'];
    const sentences = response.split(/[.!?]+/);
    
    for (const sentence of sentences) {
        const lowerSentence = sentence.toLowerCase();
        for (const keyword of riskKeywords) {
            if (lowerSentence.includes(keyword)) {
                return sentence.trim();
            }
        }
    }
    
    return 'Standard risk assessment completed';
}

/**
 * Extract next steps from text response
 * @param {string} response - The model response
 * @returns {Array} - Extracted next steps
 */
function extractNextSteps(response) {
    const stepKeywords = ['next', 'step', 'continue', 'monitor', 'evaluate', 'test'];
    const sentences = response.split(/[.!?]+/);
    const steps = [];
    
    for (const sentence of sentences) {
        const lowerSentence = sentence.toLowerCase();
        for (const keyword of stepKeywords) {
            if (lowerSentence.includes(keyword)) {
                steps.push(sentence.trim());
                break;
            }
        }
    }
    
    return steps.length > 0 ? steps : ['Continue monitoring and reassess'];
}

/**
 * Get RTP guidelines
 * GET /rtp-validator/guidelines
 */
router.get('/guidelines', async (req, res) => {
    try {
        const guidelines = {
            injuryTypes: {
                'hamstring_strain': {
                    typicalHealingTime: '3-6 weeks',
                    criteria: ['Pain-free movement', 'Full range of motion', 'Strength restoration'],
                    riskFactors: ['Incomplete healing', 'Previous injury', 'Poor rehabilitation compliance']
                },
                'ankle_sprain': {
                    typicalHealingTime: '2-4 weeks',
                    criteria: ['Stable joint', 'Full range of motion', 'Balance restoration'],
                    riskFactors: ['Chronic instability', 'Previous sprains', 'Incomplete rehabilitation']
                },
                'knee_ligament': {
                    typicalHealingTime: '6-12 months',
                    criteria: ['Surgical clearance', 'Strength restoration', 'Functional testing'],
                    riskFactors: ['Graft failure', 'Muscle weakness', 'Poor proprioception']
                }
            },
            generalCriteria: [
                'Pain-free movement',
                'Full range of motion',
                'Strength restoration',
                'Functional testing completion',
                'Medical clearance',
                'Psychological readiness'
            ],
            riskFactors: [
                'Incomplete healing',
                'Previous injury history',
                'Poor rehabilitation compliance',
                'Psychological factors',
                'Sport-specific demands'
            ]
        };

        res.json({
            success: true,
            data: guidelines,
            message: 'RTP guidelines retrieved successfully'
        });

    } catch (error) {
        console.error('Error retrieving RTP guidelines:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error retrieving RTP guidelines',
            error: error.message
        });
    }
});

/**
 * Health check for RTP validation service
 * GET /health
 */
router.get('/health', async (req, res) => {
    try {
        const healthStatus = await medGeminiService.healthCheck();
        res.json({
            service: 'RTP Validation',
            status: healthStatus.status,
            model: healthStatus.model,
            lastCheck: healthStatus.lastCheck,
            responseTime: healthStatus.responseTime
        });
    } catch (error) {
        res.status(500).json({
            service: 'RTP Validation',
            status: 'unhealthy',
            error: error.message
        });
    }
});

module.exports = router; 