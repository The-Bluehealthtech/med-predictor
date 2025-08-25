# Environment Variables for ICD-11 and FHIR Integration

## ICD-11 API Configuration

Add these variables to your `.env` file for ICD-11 API integration:

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

### Getting ICD-11 API Credentials

1. Visit the WHO ICD-11 API portal: https://icd.who.int/icdapi
2. Register for an account and request API access
3. Obtain your client ID and client secret
4. Add them to your `.env` file

## FHIR API Configuration

Add these variables to your `.env` file for FHIR API integration:

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

### FHIR Server Options

You can connect to various FHIR servers:

1. **NHS Immunisation API** (UK)
2. **HAPI FHIR** (Open source)
3. **Azure FHIR** (Microsoft)
4. **Google Cloud Healthcare API**
5. **AWS HealthLake**

## Testing the Integration

### Test ICD-11 API

```bash
curl -X GET "http://localhost:8000/api/v1/icd11/health" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test FHIR Connectivity

```bash
curl -X GET "http://localhost:8000/api/v1/fhir/connectivity" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Features Implemented

### ICD-11 Integration

-   ✅ OAuth 2 authentication
-   ✅ Search functionality with debouncing
-   ✅ Multi-language support
-   ✅ Code details and chapters
-   ✅ Reusable Vue.js component
-   ✅ Error handling and caching

### Vaccination Records

-   ✅ Full FHIR Immunization resource support
-   ✅ Bidirectional sync with external FHIR APIs
-   ✅ Multi-dose vaccine tracking
-   ✅ Expiration date monitoring
-   ✅ Verification workflow
-   ✅ Comprehensive audit trail
-   ✅ Integration with AthleteProfile

### Database Schema

-   ✅ Complete immunization tracking
-   ✅ Proper indexing for performance
-   ✅ Soft deletes for data integrity
-   ✅ Comprehensive field coverage

## Next Steps

1. **Configure API credentials** in your `.env` file
2. **Test the integrations** with real API endpoints
3. **Replace existing diagnostic inputs** with ICD-11 search
4. **Deploy and monitor** the FHIR sync functionality
