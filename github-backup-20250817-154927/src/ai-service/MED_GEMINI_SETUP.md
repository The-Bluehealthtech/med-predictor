# Med-Gemini API Setup Guide

## 🚀 Quick Setup

### 1. Get Your Med-Gemini API Key

1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy your API key (starts with `AIza...`)

### 2. Configure the API Key

1. Open the `.env` file in `src/ai-service/`
2. Replace `your_gemini_api_key_here` with your actual API key:
    ```
    GEMINI_API_KEY=AIzaSyC...your_actual_key_here
    ```

### 3. Restart the AI Service

```bash
cd src/ai-service
node server.js
```

## 🔧 Configuration Details

### Environment Variables

| Variable           | Description               | Default            |
| ------------------ | ------------------------- | ------------------ |
| `GEMINI_API_KEY`   | Your Med-Gemini API key   | Required           |
| `MED_GEMINI_MODEL` | Model to use for analysis | `gemini-1.5-flash` |
| `PORT`             | AI service port           | `3001`             |
| `NODE_ENV`         | Environment mode          | `development`      |

### Supported Models

-   `gemini-1.5-flash` (Recommended for medical analysis)
-   `gemini-1.5-pro`
-   `gemini-pro-vision`

## 🏥 Medical Image Analysis Features

### ECG Analysis

-   **DICOM Files**: Full DICOM metadata extraction and analysis
-   **Image Files**: JPG, PNG, BMP, TIFF support
-   **PDF Reports**: Medical report interpretation

### MRI Analysis

-   **Bone Age Assessment**: Skeletal maturity evaluation
-   **Growth Analysis**: Age comparison and recommendations
-   **Abnormality Detection**: Pathological finding identification

### Analysis Types

1. **ECG Analysis** (`ecg_dicom`, `ecg_image`)

    - Rhythm analysis
    - Heart rate calculation
    - ST segment evaluation
    - T wave abnormalities
    - Clinical recommendations

2. **MRI Bone Age** (`mri_dicom_bone_age`, `mri_image_bone_age`)
    - Skeletal maturity assessment
    - Age estimation
    - Growth pattern analysis
    - Clinical recommendations

## 🔍 Testing the API

### Health Check

```bash
curl http://localhost:3001/health
```

### Test Medical Image Analysis

```bash
curl -X POST http://localhost:3001/api/v1/med-gemini/analyze \
  -H "Content-Type: application/json" \
  -d '{
    "analysis_type": "ecg_image",
    "file_content": "base64_encoded_image",
    "file_type": "jpg",
    "prompt": "Analyze this ECG for abnormalities"
  }'
```

## 🛡️ Security Best Practices

1. **Never commit API keys** to version control
2. **Use environment variables** for sensitive data
3. **Rotate API keys** regularly
4. **Monitor API usage** for cost control

## 📊 API Response Format

### Success Response

```json
{
    "success": true,
    "text": "Analysis results...",
    "processingTime": 1234,
    "model": "gemini-1.5-flash",
    "timestamp": "2025-08-01T12:00:00.000Z"
}
```

### Error Response

```json
{
    "success": false,
    "error": "Error message",
    "model": "gemini-1.5-flash",
    "timestamp": "2025-08-01T12:00:00.000Z"
}
```

## 🔗 Integration with Laravel

The AI service is automatically integrated with the Laravel application:

-   **URL**: `http://localhost:3001`
-   **Routes**: `/api/v1/med-gemini/*`
-   **Authentication**: None required (internal service)

### Laravel Environment Configuration

Add to your Laravel `.env`:

```
AI_SERVICE_URL=http://localhost:3001
```

## 🚨 Troubleshooting

### Common Issues

1. **"GEMINI_API_KEY is not set"**

    - Check your `.env` file
    - Ensure the API key is correctly formatted
    - Restart the service

2. **"Failed to initialize Med-Gemini API client"**

    - Verify your API key is valid
    - Check internet connectivity
    - Ensure you have API quota

3. **"Address already in use :::3001"**
    - Kill existing process: `lsof -ti:3001 | xargs kill -9`
    - Or change port in `.env`

### Debug Mode

Set `LOG_LEVEL=debug` in `.env` for detailed logging.

## 📈 Performance Monitoring

-   **Response Time**: Typically 2-5 seconds for image analysis
-   **File Size Limit**: 10MB per file
-   **Supported Formats**: DICOM, JPG, PNG, BMP, TIFF, PDF

## 🆘 Support

For API issues:

1. Check [Google AI Studio Documentation](https://ai.google.dev/docs)
2. Verify your API key permissions
3. Monitor your usage quota

For integration issues:

1. Check the AI service logs
2. Verify network connectivity
3. Test with the health check endpoint
