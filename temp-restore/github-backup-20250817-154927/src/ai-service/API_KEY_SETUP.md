# Med-Gemini API Key Integration Guide

## Overview

The FIT Medical AI Service has been updated to securely integrate with the Med-Gemini API using environment-based configuration. This guide explains how to set up and use the API key.

## Environment Configuration

### 1. API Key Variable

The service now uses `GEMINI_API_KEY` instead of the previous `GOOGLE_AI_API_KEY` variable.

### 2. Environment File Setup

Create or update your `.env` file in the `src/ai-service/` directory:

```bash
# Med-Gemini AI Service Configuration
GEMINI_API_KEY=your_actual_gemini_api_key_here
MED_GEMINI_MODEL=gemini-1.5-flash

# Server Configuration
PORT=3001
NODE_ENV=development

# Logging
LOG_LEVEL=info
```

## Security Features

### 1. Graceful Degradation

-   **Development**: If no API key is provided, the service runs in mock mode
-   **Production**: If no API key is provided, the service exits with a fatal error

### 2. Error Handling

-   Invalid API keys are handled gracefully
-   Failed API client initialization falls back to mock mode
-   Comprehensive logging for debugging

### 3. Environment Validation

```javascript
// Production environment validation
if (process.env.NODE_ENV === "production" && !this.apiKey) {
    logger.error(
        "FATAL ERROR: GEMINI_API_KEY is not defined in production environment"
    );
    process.exit(1);
}
```

## Usage Modes

### Mock Mode (Development)

When no API key is provided:

-   Returns simulated responses for testing
-   Logs warning messages
-   Allows development without API costs

### Production Mode

When API key is provided:

-   Authenticates with Med-Gemini API
-   Processes real AI requests
-   Provides actual medical analysis

## API Endpoints

The following endpoints are available and will use the configured API key:

-   `POST /api/ai/sca-risk/sca-risk-score` - SCA Risk Assessment
-   `POST /api/ai/injury-prediction/analyze` - Injury Prediction
-   `POST /api/ai/medical-notes/generate` - Medical Note Generation
-   `POST /api/ai/compliance/check` - Compliance Checking
-   `POST /api/ai/whisper/transcribe` - Audio Transcription
-   `POST /api/ai/ocr/extract` - Text Extraction from Images

## Testing

### Test Script

Run the included test script to verify API key integration:

```bash
node test-api-key.js
```

### Manual Testing

Test the API endpoints:

```bash
# Test SCA Risk Assessment
curl -X POST http://localhost:3001/api/ai/sca-risk/sca-risk-score \
  -H "Content-Type: application/json" \
  -d '{
    "ecgImage": "base64data",
    "clinicalHistory": "Patient has chest pain",
    "athleteId": 123
  }'
```

## Troubleshooting

### Common Issues

1. **"GEMINI_API_KEY is not set"**

    - Add your API key to the `.env` file
    - Ensure the variable name is exactly `GEMINI_API_KEY`

2. **"Failed to initialize Med-Gemini API client"**

    - Check API key validity
    - Verify network connectivity
    - Review API quota limits

3. **Production deployment fails**
    - Ensure `GEMINI_API_KEY` is set in production environment
    - Check environment variable configuration in deployment platform

### Logs

Monitor logs for debugging:

-   Mock mode warnings
-   API client initialization messages
-   Error details for failed requests

## Security Best Practices

1. **Never commit API keys to version control**
2. **Use environment variables for all deployments**
3. **Rotate API keys regularly**
4. **Monitor API usage and costs**
5. **Implement rate limiting for production use**

## Migration from Previous Version

If upgrading from the previous version:

1. Update your `.env` file:

    ```bash
    # Old
    GOOGLE_AI_API_KEY=your_key

    # New
    GEMINI_API_KEY=your_key
    ```

2. Restart the service:

    ```bash
    npm start
    ```

3. Verify the service is running correctly:
    ```bash
    curl http://localhost:3001/health
    ```
