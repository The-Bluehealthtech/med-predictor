# ICD-11 and FHIR Integration Setup Guide

This guide provides step-by-step instructions for setting up and testing the ICD-11 and FHIR integrations in the Med Predictor application.

## Prerequisites

-   Laravel application running on localhost:8000
-   PHP 8.0 or higher
-   Composer dependencies installed
-   Database migrations run

## 1. Environment Configuration

### Step 1: Configure ICD-11 API

Add these variables to your `.env` file:

```env
# ICD-11 API Configuration
ICD11_CLIENT_ID=your_icd11_client_id
ICD11_CLIENT_SECRET=your_icd11_client_secret
ICD11_BASE_URL=https://icd.who.int/icdapi
ICD11_TIMEOUT=30
ICD11_CACHE_TTL=3600
ICD11_RETRY_ATTEMPTS=3
ICD11_RETRY_DELAY=5
```

### Step 2: Configure FHIR API

Add these variables to your `.env` file:

```env
# FHIR API Configuration
FHIR_BASE_URL=https://your-fhir-server.com/fhir
FHIR_TIMEOUT=30
FHIR_RETRY_ATTEMPTS=3
FHIR_RETRY_DELAY=5
FHIR_AUDIT_LOGGING=true
FHIR_BATCH_SIZE=100
FHIR_VERSION=R4
```

## 2. Getting API Credentials

### ICD-11 API Credentials

1. Visit the WHO ICD-11 API portal: https://icd.who.int/icdapi
2. Register for an account
3. Request API access
4. Obtain your client ID and client secret
5. Add them to your `.env` file

### FHIR Server Options

You can connect to various FHIR servers:

1. **HAPI FHIR** (Open source) - https://hapifhir.io/
2. **Azure FHIR** (Microsoft) - https://azure.microsoft.com/en-us/services/healthcare-apis/
3. **Google Cloud Healthcare API** - https://cloud.google.com/healthcare-api
4. **AWS HealthLake** - https://aws.amazon.com/healthlake/
5. **NHS Immunisation API** (UK) - https://digital.nhs.uk/developer/api-catalogue/immunisation-api

## 3. Testing the Integrations

### Step 1: Run the Test Script

```bash
php scripts/test-integrations.php
```

This script will test:

-   Environment configuration
-   Service configuration
-   ICD-11 API connectivity
-   FHIR API connectivity
-   Search functionality
-   Code retrieval

### Step 2: Manual API Testing

#### Test ICD-11 Health Check

```bash
curl -X GET "http://localhost:8000/api/v1/icd11/health"
```

Expected response:

```json
{
    "success": true,
    "status": "healthy",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

#### Test ICD-11 Search

```bash
curl -X GET "http://localhost:8000/api/v1/icd11/search?query=diabetes&language=en&limit=5"
```

Expected response:

```json
{
    "success": true,
    "data": [
        {
            "code": "5A10",
            "label": "Type 1 diabetes mellitus",
            "definition": "...",
            "chapter": "Endocrine, nutritional or metabolic diseases"
        }
    ],
    "total": 1,
    "query": "diabetes"
}
```

#### Test FHIR Connectivity

```bash
curl -X GET "http://localhost:8000/api/v1/fhir/connectivity"
```

Expected response:

```json
{
    "success": true,
    "status": "connected",
    "message": "FHIR server is accessible",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

#### Test FHIR Capabilities

```bash
curl -X GET "http://localhost:8000/api/v1/fhir/capabilities"
```

Expected response:

```json
{
    "success": true,
    "data": {
        "fhir_version": "4.0.1",
        "software": "HAPI FHIR",
        "version": "6.0.0",
        "resources": 145,
        "supported_resources": ["Patient", "Immunization", "Observation"]
    }
}
```

## 4. Frontend Integration

### ICD-11 Search Component

The application includes a reusable Vue.js component for ICD-11 search:

```vue
<ICDSearchInput
    v-model="selectedDiagnostic"
    :language="'en'"
    :limit="10"
    placeholder="Search ICD-11 codes..."
/>
```

### FHIR Sync Integration

The immunization records are automatically synced with FHIR:

```javascript
// Sync immunization records
await this.$http.post(`/api/v1/athletes/${athleteId}/immunisations/sync`);
```

## 5. Troubleshooting

### Common ICD-11 Issues

1. **Authentication Failed**

    - Check your client ID and secret
    - Verify the token endpoint is accessible
    - Check network connectivity

2. **Search Not Working**

    - Verify the search endpoint is correct
    - Check the API version parameter
    - Ensure proper language codes

3. **Rate Limiting**
    - Implement caching for repeated searches
    - Add delays between requests
    - Use the fallback data when API is unavailable

### Common FHIR Issues

1. **Connection Failed**

    - Verify the FHIR server URL
    - Check network connectivity
    - Ensure proper authentication

2. **Resource Not Found**

    - Verify the resource type exists
    - Check the FHIR version compatibility
    - Ensure proper resource IDs

3. **Sync Errors**
    - Check athlete FHIR ID exists
    - Verify resource format
    - Check for validation errors

## 6. Production Deployment

### Environment Variables

Ensure all environment variables are set in production:

```bash
# Check configuration
php artisan config:cache
php artisan route:cache
```

### Monitoring

Set up monitoring for:

-   API response times
-   Error rates
-   Sync success rates
-   Cache hit rates

### Logging

Enable detailed logging for debugging:

```env
LOG_LEVEL=debug
FHIR_AUDIT_LOGGING=true
ICD11_DEBUG=true
```

## 7. Security Considerations

### API Keys

-   Store credentials securely
-   Use environment variables
-   Rotate keys regularly
-   Monitor for unauthorized access

### Data Privacy

-   Implement proper data encryption
-   Follow HIPAA guidelines
-   Audit data access
-   Implement data retention policies

## 8. Performance Optimization

### Caching

-   Cache ICD-11 search results
-   Cache FHIR metadata
-   Use Redis for session storage
-   Implement response caching

### Rate Limiting

-   Implement request throttling
-   Use exponential backoff
-   Monitor API quotas
-   Handle rate limit errors gracefully

## 9. Next Steps

1. **Configure real API credentials** in your `.env` file
2. **Test with actual FHIR server** endpoints
3. **Implement error handling** for production use
4. **Set up monitoring** and alerting
5. **Train users** on the new features
6. **Deploy to production** environment

## Support

For issues or questions:

-   Check the application logs
-   Review the API documentation
-   Test with the provided scripts
-   Contact the development team

---

**Note**: This integration provides a robust foundation for medical data management with international standards compliance.
