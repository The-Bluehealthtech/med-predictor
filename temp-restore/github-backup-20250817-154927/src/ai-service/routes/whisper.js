const express = require('express');
const router = express.Router();
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const medGeminiService = require('../services/medGeminiService');

// Configure multer for audio file uploads
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        const uploadDir = path.join(__dirname, '../uploads/whisper');
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: function (req, file, cb) {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, 'whisper-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({ 
    storage: storage,
    limits: {
        fileSize: 10 * 1024 * 1024 // 10MB limit
    },
    fileFilter: (req, file, cb) => {
        const allowedTypes = ['audio/wav', 'audio/mp3', 'audio/m4a', 'audio/mpeg'];
        if (allowedTypes.includes(file.mimetype)) {
            cb(null, true);
        } else {
            cb(new Error('Invalid file type. Only WAV, MP3, and M4A files are allowed.'));
        }
    }
});

/**
 * POST /api/ai/whisper/transcribe
 * Transcribe audio using Whisper
 */
router.post('/transcribe', upload.single('audio'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({
                success: false,
                message: 'No audio file provided'
            });
        }

        const audioFilePath = req.file.path;
        const language = req.body.language || 'fr';
        const model = req.body.model || 'whisper-1';

        console.log(`Processing audio file: ${audioFilePath}`);
        console.log(`Language: ${language}, Model: ${model}`);

        // Use Med-Gemini service for transcription
        const transcriptionResult = await medGeminiService.transcribeAudio({
            audioFilePath: audioFilePath,
            language: language,
            model: model
        });

        // Clean up uploaded file
        fs.unlinkSync(audioFilePath);

        if (transcriptionResult.success) {
            return res.json({
                success: true,
                message: 'Audio transcribed successfully',
                transcription: transcriptionResult.transcription,
                confidence: transcriptionResult.confidence || 0.0,
                language: language,
                model: model
            });
        } else {
            return res.status(500).json({
                success: false,
                message: 'Transcription failed',
                error: transcriptionResult.error
            });
        }

    } catch (error) {
        console.error('Error in Whisper transcription:', error);
        
        // Clean up file if it exists
        if (req.file && fs.existsSync(req.file.path)) {
            fs.unlinkSync(req.file.path);
        }

        return res.status(500).json({
            success: false,
            message: 'Internal server error during transcription',
            error: error.message
        });
    }
});

module.exports = router; 