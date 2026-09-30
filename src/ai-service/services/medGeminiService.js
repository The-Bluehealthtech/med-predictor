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
        this.modelName = process.env.MED_GEMINI_MODEL || null;
        this.mockMode = !this.apiKey || !this.modelName || this.apiKey === 'your_gemini_api_key_here';
        
        // Check if we're in production and API key is missing
        if (process.env.NODE_ENV === 'production' && !this.apiKey) {
            logger.error('FATAL ERROR: GEMINI_API_KEY is not defined in production environment');
            // Le service reste disponible pour répondre 503 sans résultat simulé.
        }
        
        if (this.mockMode) {
            logger.warn('Analyse IA indisponible : GEMINI_API_KEY et MED_GEMINI_MODEL doivent être configurés.');
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
        
        logger.info(`MedGeminiService initialized with model: ${this.modelName}, provider unavailable: ${this.mockMode}`);
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
                return {success:false, error:'AI provider is not configured', model:this.modelName};
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
                error: 'Provider unavailable',
                model: this.modelName
            });

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
            const result = await this.analyze(prompt, {text});
            const structuredData = require('../utils/medicalResult').normalizeMedicalResult(result);
            return {...result, structuredData, confidence:null};
        } catch (error) {
            return {success:false, error:'Structured extraction unavailable', confidence:null};
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
    calculateConfidence(response) { return null; }

    // Transcription réelle via le fournisseur déjà prévu dans la configuration du service.
    async transcribeAudio({ audioFilePath, language = 'fr' }) {
        const key = process.env.OPENAI_API_KEY;
        if (!key || key.startsWith('your-')) return {success:false, error:'Transcription provider not configured'};
        try {
            const fs = require('fs/promises');
            const path = require('path');
            const body = new FormData();
            body.append('file', new Blob([await fs.readFile(audioFilePath)]), path.basename(audioFilePath));
            body.append('model', process.env.WHISPER_MODEL || 'whisper-1');
            body.append('response_format', 'json');
            if (/^[a-z]{2}$/i.test(language)) body.append('language', language);
            const base = (process.env.OPENAI_BASE_URL || 'https://api.openai.com/v1').replace(/\/$/, '');
            const response = await fetch(base + '/audio/transcriptions', {
                method:'POST', headers:{Authorization:'Bearer ' + key}, body,
                signal:AbortSignal.timeout(30000)
            });
            if (!response.ok) throw new Error('Transcription provider unavailable');
            const data = await response.json();
            if (typeof data.text !== 'string' || !data.text.trim()) throw new Error('Empty transcription');
            return {success:true, transcription:data.text, confidence:null,
                model:process.env.WHISPER_MODEL || 'whisper-1'};
        } catch (error) {
            return {success:false, error:'Transcription unavailable'};
        }
    }

    // Extraire uniquement le texte de la pièce ; ne jamais inventer une observation clinique.
    async extractTextFromImage({ imageFilePath }) {
        try {
            const fs = require('fs/promises');
            const ext = require('path').extname(imageFilePath).slice(1).toLowerCase();
            const mime = {jpg:'image/jpeg',jpeg:'image/jpeg',png:'image/png',pdf:'application/pdf'}[ext];
            if (!mime) return {success:false,error:'Unsupported document format'};
            const result = await this.analyzeMedicalImage(
                'Transcribe only text visible in this document. Do not infer missing text or clinical findings. Return JSON with the single field extracted_text. If no text is legible, use an empty string.',
                await fs.readFile(imageFilePath), mime);
            const data = require('../utils/medicalResult').normalizeMedicalResult(result);
            if (typeof data.extracted_text !== 'string' || !data.extracted_text.trim()) {
                return {success:false,error:'No legible text extracted'};
            }
            return {success:true, extracted_text:data.extracted_text, confidence:null,
                word_count:data.extracted_text.trim().split(/\s+/).length};
        } catch (error) {
            return {success:false,error:'OCR unavailable'};
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
