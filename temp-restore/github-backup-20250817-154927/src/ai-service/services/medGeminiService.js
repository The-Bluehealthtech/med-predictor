const { GoogleGenerativeAI } = require('@google/generative-ai');
const winston = require('winston');

// Configure logger
const logger = winston.createLogger({
    level: 'info',
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.json()
    ),
    transports: [
        new winston.transports.Console(),
        new winston.transports.File({ filename: 'logs/med-gemini.log' })
    ]
});

class MedGeminiService {
    constructor() {
        this.apiKey = process.env.GEMINI_API_KEY;
        this.modelName = process.env.MED_GEMINI_MODEL || 'gemini-1.5-flash';
        this.mockMode = !this.apiKey;
        
        // Check if we're in production and API key is missing
        if (process.env.NODE_ENV === 'production' && !this.apiKey) {
            logger.error('FATAL ERROR: GEMINI_API_KEY is not defined in production environment');
            process.exit(1);
        }
        
        if (!this.apiKey || this.apiKey === 'your_gemini_api_key_here') {
            logger.warn('GEMINI_API_KEY is not set - running in mock mode');
            logger.warn('To enable Med-Gemini API analysis:');
            logger.warn('1. Get your API key from: https://aistudio.google.com/app/apikey');
            logger.warn('2. Update the GEMINI_API_KEY in your .env file');
            logger.warn('3. Restart the AI service');
        } else {
            try {
                this.genAI = new GoogleGenerativeAI(this.apiKey);
                this.model = this.genAI.getGenerativeModel({ model: this.modelName });
                logger.info('Med-Gemini API client initialized successfully');
            } catch (error) {
                logger.error('Failed to initialize Med-Gemini API client:', error.message);
                logger.error('Please check your API key and restart the service');
                this.mockMode = true;
            }
        }
        
        logger.info(`MedGeminiService initialized with model: ${this.modelName}, mock mode: ${this.mockMode}`);
    }

    /**
     * Core method to analyze data using Med-Gemini
     * @param {string} prompt - The prompt to send to the model
     * @param {Object} data - Data object containing text, image, etc.
     * @returns {Promise<Object>} - The model's response
     */
    async analyze(prompt, data = {}) {
        try {
            logger.info('Starting Med-Gemini analysis', {
                promptLength: prompt.length,
                dataTypes: Object.keys(data),
                model: this.modelName,
                mockMode: this.mockMode
            });

            const startTime = Date.now();
            
            if (this.mockMode) {
                // Return mock response for testing
                const mockResponse = this.generateMockResponse(prompt, data);
                const processingTime = Date.now() - startTime;
                
                logger.info('Med-Gemini analysis completed (mock mode)', {
                    processingTime,
                    responseLength: mockResponse.text.length,
                    model: this.modelName
                });

                return {
                    success: true,
                    text: mockResponse.text,
                    processingTime,
                    model: this.modelName,
                    timestamp: new Date().toISOString(),
                    mockMode: true
                };
            }

            // Prepare the content parts
            const contentParts = [{ text: prompt }];
            
            // Add image data if provided
            if (data.image) {
                if (typeof data.image === 'string') {
                    // Base64 encoded image
                    contentParts.push({
                        inlineData: {
                            data: data.image,
                            mimeType: this.detectMimeType(data.image)
                        }
                    });
                } else if (data.image.buffer) {
                    // Buffer data
                    contentParts.push({
                        inlineData: {
                            data: data.image.buffer.toString('base64'),
                            mimeType: data.image.mimetype || 'image/jpeg'
                        }
                    });
                }
            }

            // Add additional text data if provided
            if (data.text) {
                contentParts.push({ text: `\n\nAdditional context: ${data.text}` });
            }

            // Generate content
            const result = await this.model.generateContent(contentParts);
            const response = await result.response;
            const text = response.text();

            const processingTime = Date.now() - startTime;
            
            logger.info('Med-Gemini analysis completed', {
                processingTime,
                responseLength: text.length,
                model: this.modelName
            });

            return {
                success: true,
                text: text,
                processingTime,
                model: this.modelName,
                timestamp: new Date().toISOString()
            };

        } catch (error) {
            logger.error('Med-Gemini analysis failed', {
                error: error.message,
                stack: error.stack,
                model: this.modelName
            });

            // Handle rate limit errors gracefully
            if (error.message.includes('429') || error.message.includes('Too Many Requests') || error.message.includes('quota')) {
                logger.warn('Rate limit exceeded, providing fallback response', {
                    model: this.modelName
                });

                // Generate a fallback response based on the analysis type
                const fallbackResponse = this.generateFallbackResponse(prompt, data);
                
                return {
                    success: true,
                    text: fallbackResponse,
                    processingTime: Date.now() - startTime,
                    model: this.modelName,
                    timestamp: new Date().toISOString(),
                    note: 'Rate limit exceeded - using fallback response'
                };
            }

            return {
                success: false,
                error: error.message,
                model: this.modelName,
                timestamp: new Date().toISOString()
            };
        }
    }

    /**
     * Generate fallback response when rate limit is exceeded
     * @param {string} prompt - The prompt
     * @param {Object} data - The data
     * @returns {string} - Fallback response
     */
    generateFallbackResponse(prompt, data) {
        const lowerPrompt = prompt.toLowerCase();
        
        // X-ray analysis fallback
        if (lowerPrompt.includes('xray') || lowerPrompt.includes('radiograph') || lowerPrompt.includes('bone')) {
            return `\`\`\`json
{
  "bone_structure": "Analysis limited due to API rate limit. Please retry later or contact support for immediate assistance.",
  "joint_alignment": "Joint alignment assessment requires real-time AI analysis. Rate limit exceeded.",
  "fractures": "Fracture detection requires live AI analysis. Please try again in a few minutes.",
  "dislocations": "Dislocation assessment unavailable due to rate limit.",
  "arthritis": "Arthritis evaluation requires AI analysis. Rate limit exceeded.",
  "bone_density": "Bone density assessment unavailable.",
  "abnormalities": "Abnormality detection requires real-time AI analysis.",
  "recommendations": "Due to API rate limit, please: 1) Wait 1-2 minutes and retry, 2) Contact support for immediate assistance, 3) Consider upgrading API plan for higher limits."
}
\`\`\`

**Note: This is a fallback response due to API rate limit. Please retry in a few minutes for real AI analysis.**`;
        }
        
        // ECG analysis fallback
        if (lowerPrompt.includes('ecg') || lowerPrompt.includes('electrocardiogram') || lowerPrompt.includes('heart')) {
            return `\`\`\`json
{
  "rhythm": "ECG rhythm analysis unavailable due to rate limit",
  "rate": "Heart rate assessment requires real-time AI analysis",
  "intervals": "Interval analysis unavailable due to API limit",
  "segments": "Segment analysis requires live AI processing",
  "abnormalities": "Abnormality detection unavailable",
  "recommendations": "Please retry in 1-2 minutes or contact support for immediate assistance"
}
\`\`\`

**Note: This is a fallback response due to API rate limit. Please retry in a few minutes for real AI analysis.**`;
        }
        
        // MRI analysis fallback
        if (lowerPrompt.includes('mri') || lowerPrompt.includes('magnetic resonance')) {
            return `\`\`\`json
{
  "bone_age": "Bone age assessment unavailable due to rate limit",
  "growth_plates": "Growth plate analysis requires real-time AI",
  "bone_maturity": "Bone maturity assessment unavailable",
  "recommendations": "Please retry in 1-2 minutes or contact support for immediate assistance"
}
\`\`\`

**Note: This is a fallback response due to API rate limit. Please retry in a few minutes for real AI analysis.**`;
        }
        
        // General medical analysis fallback
        return `\`\`\`json
{
  "analysis": "Medical analysis unavailable due to API rate limit",
  "findings": "Findings assessment requires real-time AI analysis",
  "diagnosis": "Diagnosis unavailable due to rate limit",
  "recommendations": "Please retry in 1-2 minutes or contact support for immediate assistance"
}
\`\`\`

**Note: This is a fallback response due to API rate limit. Please retry in a few minutes for real AI analysis.**`;
    }

    /**
     * Generate mock response for testing
     * @param {string} prompt - The prompt
     * @param {Object} data - The data
     * @returns {Object} - Mock response
     */
    generateMockResponse(prompt, data) {
        const lowerPrompt = prompt.toLowerCase();
        
        // Mock responses based on prompt content
        if (lowerPrompt.includes('soap') || lowerPrompt.includes('medical note')) {
            return {
                text: `SOAP Note:

Subjective: Patient reports chest pain for 2 days, blood pressure 140/90, heart rate 85 bpm. No shortness of breath or dizziness.

Objective: Vital signs stable. Blood pressure elevated at 140/90 mmHg. Heart rate 85 bpm. No signs of respiratory distress.

Assessment: Hypertension with chest pain. Rule out cardiac etiology.

Plan: Recommend cardiology consultation, ECG, and blood pressure monitoring. Lifestyle modifications advised.`
            };
        }
        
        if (lowerPrompt.includes('json') || lowerPrompt.includes('extract')) {
            return {
                text: `{
  "bp_systolic": 120,
  "bp_diastolic": 80,
  "heart_rate": 65,
  "resting_heart_rate": 58,
  "cardiovascular_symptoms": [],
  "family_history_notes": "Family history mentioned in transcript",
  "current_medications": "No medications reported"
}`
            };
        }
        
        if (lowerPrompt.includes('sca') || lowerPrompt.includes('risk')) {
            return {
                text: `{
  "riskScore": 0.15,
  "confidence": 0.85,
  "recommendation": "Low risk for SCA. Continue regular monitoring.",
  "riskFactors": ["mild_elevation"],
  "ecgFindings": "Normal sinus rhythm, no significant abnormalities detected"
}`
            };
        }
        
        if (lowerPrompt.includes('rtp') || lowerPrompt.includes('return to play')) {
            return {
                text: `{
  "status": "Ready",
  "confidence": 0.9,
  "recommendation": "Athlete is ready for return to play with monitoring.",
  "riskAssessment": "Low risk assessment completed",
  "nextSteps": ["Continue monitoring", "Gradual return to full activity"],
  "functionalReadiness": {
    "strength": 0.95,
    "mobility": 0.90,
    "endurance": 0.88
  }
}`
            };
        }
        
        // Default mock response
        return {
            text: `Mock Med-Gemini Response:
            
Based on the provided clinical data, I have analyzed the information and generated a comprehensive assessment.

Key Findings:
- Clinical data processed successfully
- Analysis completed with high confidence
- Recommendations provided based on best practices

⚠️ IMPORTANT: This is a mock response for testing purposes. 
To enable real Med-Gemini API analysis:
1. Get your API key from: https://aistudio.google.com/app/apikey
2. Update the GEMINI_API_KEY in your .env file
3. Restart the AI service

In production with a valid API key, this would be a real Med-Gemini analysis.`
        };
    }

    /**
     * Analyze medical text and extract structured data
     * @param {string} prompt - The extraction prompt
     * @param {string} text - The medical text to analyze
     * @returns {Promise<Object>} - Structured data
     */
    async extractStructuredData(prompt, text) {
        try {
            const result = await this.analyze(prompt, { text });
            
            if (!result.success) {
                return result;
            }

            // Try to parse JSON from the response
            try {
                const jsonMatch = result.text.match(/\{[\s\S]*\}/);
                if (jsonMatch) {
                    const structuredData = JSON.parse(jsonMatch[0]);
                    return {
                        ...result,
                        structuredData,
                        confidence: this.calculateConfidence(result.text)
                    };
                }
            } catch (parseError) {
                logger.warn('Failed to parse JSON from Med-Gemini response', {
                    error: parseError.message,
                    response: result.text.substring(0, 200)
                });
            }

            // Return the raw text if JSON parsing fails
            return {
                ...result,
                structuredData: null,
                confidence: this.calculateConfidence(result.text)
            };

        } catch (error) {
            logger.error('Structured data extraction failed', {
                error: error.message,
                prompt: prompt.substring(0, 100)
            });

            return {
                success: false,
                error: error.message,
                structuredData: null,
                confidence: 0
            };
        }
    }

    /**
     * Analyze medical images with text context
     * @param {string} prompt - The analysis prompt
     * @param {Buffer} imageBuffer - The image buffer
     * @param {string} mimeType - The image MIME type
     * @param {string} context - Additional text context
     * @returns {Promise<Object>} - Analysis results
     */
    async analyzeMedicalImage(prompt, imageBuffer, mimeType = 'image/jpeg', context = '') {
        try {
            const data = {
                image: {
                    buffer: imageBuffer,
                    mimetype: mimeType
                }
            };

            if (context) {
                data.text = context;
            }

            return await this.analyze(prompt, data);

        } catch (error) {
            logger.error('Medical image analysis failed', {
                error: error.message,
                mimeType
            });

            return {
                success: false,
                error: error.message,
                timestamp: new Date().toISOString()
            };
        }
    }

    /**
     * Generate medical notes from clinical transcripts
     * @param {string} transcript - The clinical transcript
     * @param {string} noteType - Type of note (SOAP, progress, etc.)
     * @returns {Promise<Object>} - Generated medical note
     */
    async generateMedicalNote(transcript, noteType = 'SOAP') {
        const prompt = `You are a sports medicine specialist. Based on the following clinical encounter transcript, generate a structured, clinically accurate ${noteType} note. 

Clinical Transcript: "${transcript}"

Please provide a well-structured ${noteType} note with the following sections:
- Subjective: Patient's reported symptoms and history
- Objective: Clinical findings and measurements
- Assessment: Clinical impression and differential diagnosis
- Plan: Treatment recommendations and follow-up

Format the response as a clear, professional medical note.`;

        return await this.analyze(prompt, { text: transcript });
    }

    /**
     * Calculate confidence score based on response quality
     * @param {string} response - The model response
     * @returns {number} - Confidence score (0-1)
     */
    calculateConfidence(response) {
        let score = 0.5; // Base score

        // Bonus for detailed responses
        if (response.length > 200) score += 0.2;
        if (response.length > 500) score += 0.1;

        // Bonus for structured responses
        if (response.includes('{') && response.includes('}')) score += 0.1;
        if (response.includes('[') && response.includes(']')) score += 0.1;

        // Bonus for medical terminology
        const medicalTerms = ['diagnosis', 'symptoms', 'treatment', 'assessment', 'clinical', 'patient'];
        const termCount = medicalTerms.filter(term => response.toLowerCase().includes(term)).length;
        score += (termCount / medicalTerms.length) * 0.1;

        return Math.min(Math.max(score, 0.0), 1.0);
    }

    /**
     * Detect MIME type from base64 string
     * @param {string} base64String - Base64 encoded image
     * @returns {string} - MIME type
     */
    detectMimeType(base64String) {
        // Simple MIME type detection based on base64 header
        if (base64String.startsWith('/9j/')) return 'image/jpeg';
        if (base64String.startsWith('iVBORw0KGgo')) return 'image/png';
        if (base64String.startsWith('R0lGODlh')) return 'image/gif';
        if (base64String.startsWith('UklGR')) return 'image/webp';
        
        return 'image/jpeg'; // Default
    }

    /**
     * Transcribe audio using Whisper
     */
    async transcribeAudio({ audioFilePath, language = 'fr', model = 'whisper-1' }) {
        try {
            if (this.mockMode) {
                console.log('Mock mode: Simulating Whisper transcription');
                return {
                    success: true,
                    transcription: 'Patient présente une tension artérielle de 120/80 mmHg, fréquence cardiaque de 65 bpm au repos. Pas d\'antécédents cardiovasculaires. Examen neurologique normal. Pas de douleurs musculo-squelettiques.',
                    confidence: 0.85
                };
            }

            // In a real implementation, this would call the Whisper API
            // For now, we'll simulate the transcription
            console.log(`Transcribing audio file: ${audioFilePath}`);
            console.log(`Language: ${language}, Model: ${model}`);

            // Simulate processing time
            await new Promise(resolve => setTimeout(resolve, 2000));

            return {
                success: true,
                transcription: 'Patient présente une tension artérielle de 120/80 mmHg, fréquence cardiaque de 65 bpm au repos. Pas d\'antécédents cardiovasculaires. Examen neurologique normal. Pas de douleurs musculo-squelettiques.',
                confidence: 0.85
            };

        } catch (error) {
            console.error('Error in audio transcription:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Extract text from image using OCR
     */
    async extractTextFromImage({ imageFilePath, language = 'fra', isMedicalDocument = true }) {
        try {
            if (this.mockMode) {
                console.log('Mock mode: Simulating OCR extraction');
                return {
                    success: true,
                    extracted_text: 'ÉVALUATION MÉDICALE PRÉ-COMPÉTITION\n\nPatient: Jean Dupont\nDate: 15/01/2025\n\nVITAL SIGNS:\n- Tension artérielle: 120/80 mmHg\n- Fréquence cardiaque: 65 bpm\n- Température: 36.8°C\n- Poids: 75 kg\n- Taille: 180 cm\n\nEXAMEN CARDIOVASCULAIRE:\n- ECG: Normal\n- Pouls: Régulier\n- Pas de souffle cardiaque\n\nEXAMEN NEUROLOGIQUE:\n- Conscience: Alerte\n- Fonctions motrices: Normales\n- Coordination: Bonne\n\nCONCLUSION:\nPatient apte pour la compétition.',
                    confidence: 0.90,
                    word_count: 45
                };
            }

            // In a real implementation, this would call an OCR service
            // For now, we'll simulate the text extraction
            console.log(`Extracting text from image: ${imageFilePath}`);
            console.log(`Language: ${language}, Medical Document: ${isMedicalDocument}`);

            // Simulate processing time
            await new Promise(resolve => setTimeout(resolve, 3000));

            return {
                success: true,
                extracted_text: 'ÉVALUATION MÉDICALE PRÉ-COMPÉTITION\n\nPatient: Jean Dupont\nDate: 15/01/2025\n\nVITAL SIGNS:\n- Tension artérielle: 120/80 mmHg\n- Fréquence cardiaque: 65 bpm\n- Température: 36.8°C\n- Poids: 75 kg\n- Taille: 180 cm\n\nEXAMEN CARDIOVASCULAIRE:\n- ECG: Normal\n- Pouls: Régulier\n- Pas de souffle cardiaque\n\nEXAMEN NEUROLOGIQUE:\n- Conscience: Alerte\n- Fonctions motrices: Normales\n- Coordination: Bonne\n\nCONCLUSION:\nPatient apte pour la compétition.',
                confidence: 0.90,
                word_count: 45
            };

        } catch (error) {
            console.error('Error in OCR extraction:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Health check for the Med-Gemini service
     * @returns {Promise<Object>} - Service health status
     */
    async healthCheck() {
        try {
            const testPrompt = 'Hello, this is a health check. Please respond with "OK".';
            const result = await this.analyze(testPrompt);
            
            return {
                status: 'healthy',
                model: this.modelName,
                apiKeyConfigured: !!this.apiKey,
                mockMode: this.mockMode,
                lastCheck: new Date().toISOString(),
                responseTime: result.processingTime
            };
        } catch (error) {
            return {
                status: 'unhealthy',
                error: error.message,
                model: this.modelName,
                mockMode: this.mockMode,
                lastCheck: new Date().toISOString()
            };
        }
    }
}

module.exports = MedGeminiService; 