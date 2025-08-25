const express = require('express');
const router = express.Router();
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const medGeminiService = require('../services/medGeminiService');

// Configure multer for image file uploads
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        const uploadDir = path.join(__dirname, '../uploads/ocr');
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: function (req, file, cb) {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, 'ocr-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({ 
    storage: storage,
    limits: {
        fileSize: 10 * 1024 * 1024 // 10MB limit
    },
    fileFilter: (req, file, cb) => {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (allowedTypes.includes(file.mimetype)) {
            cb(null, true);
        } else {
            cb(new Error('Invalid file type. Only JPEG, PNG, and PDF files are allowed.'));
        }
    }
});

/**
 * POST /api/ai/ocr/extract
 * Extract text from image using OCR
 */
router.post('/extract', upload.single('image'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({
                success: false,
                message: 'No image file provided'
            });
        }

        const imageFilePath = req.file.path;
        const language = req.body.language || 'fra'; // French language code
        const isMedicalDocument = req.body.medical_document === 'true';

        console.log(`Processing image file: ${imageFilePath}`);
        console.log(`Language: ${language}, Medical Document: ${isMedicalDocument}`);

        // Use Med-Gemini service for OCR extraction
        const ocrResult = await medGeminiService.extractTextFromImage({
            imageFilePath: imageFilePath,
            language: language,
            isMedicalDocument: isMedicalDocument
        });

        // Clean up uploaded file
        fs.unlinkSync(imageFilePath);

        if (ocrResult.success) {
            return res.json({
                success: true,
                message: 'Text extracted successfully from image',
                extracted_text: ocrResult.extracted_text,
                confidence: ocrResult.confidence || 0.0,
                language: language,
                word_count: ocrResult.word_count || 0,
                is_medical_document: isMedicalDocument
            });
        } else {
            return res.status(500).json({
                success: false,
                message: 'OCR extraction failed',
                error: ocrResult.error
            });
        }

    } catch (error) {
        console.error('Error in OCR extraction:', error);
        
        // Clean up file if it exists
        if (req.file && fs.existsSync(req.file.path)) {
            fs.unlinkSync(req.file.path);
        }

        return res.status(500).json({
            success: false,
            message: 'Internal server error during OCR extraction',
            error: error.message
        });
    }
});

module.exports = router; 