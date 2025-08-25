# Testing Guide for ICD-11 and Vaccination Integration

## Overview

This guide helps you test the newly implemented ICD-11 diagnostic standardization and vaccination record management features.

## 1. Testing ICD-11 Integration

### Backend Testing

Test the ICD-11 API endpoints:

```bash
# Test health endpoint
curl -X GET "http://localhost:8000/api/v1/icd11/health" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test search endpoint
curl -X GET "http://localhost:8000/api/v1/icd11/search?query=diabetes&language=fr&limit=5" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test chapters endpoint
curl -X GET "http://localhost:8000/api/v1/icd11/chapters?language=fr" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Frontend Testing

1. **Navigate to an athlete profile**
2. **Go to the Injuries section**
3. **Create a new injury**
4. **Test the ICD-11 search input**:
    - Type "diabetes" in the diagnostic field
    - Verify dropdown appears with ICD-11 results
    - Select a diagnostic and verify it populates the injury type

## 2. Testing Vaccination Records

### Backend Testing

Test the vaccination API endpoints:

```bash
# List vaccinations for an athlete
curl -X GET "http://localhost:8000/api/v1/athletes/1/immunisations" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create a new vaccination record
curl -X POST "http://localhost:8000/api/v1/athletes/1/immunisations" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "vaccine_code": "207",
    "vaccine_name": "COVID-19 vaccine, mRNA, Pfizer-BioNTech",
    "date_administered": "2024-01-15",
    "lot_number": "ABC123",
    "manufacturer": "Pfizer",
    "expiration_date": "2025-01-15",
    "dose_number": 1,
    "total_doses": 2,
    "route": "IM",
    "site": "LA",
    "notes": "First dose administered"
  }'

# Get vaccination statistics
curl -X GET "http://localhost:8000/api/v1/athletes/1/immunisations/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test FHIR connectivity
curl -X GET "http://localhost:8000/api/v1/fhir/connectivity" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Frontend Testing

1. **Navigate to an athlete profile**
2. **Click on the "Vaccinations" tab** (💉 icon)
3. **Test the vaccination interface**:
    - View vaccination statistics
    - Add a new vaccination record
    - Test the ICD-11 search for vaccine codes
    - Test filtering and pagination
    - Test the FHIR sync functionality

## 3. Testing Integration Points

### InjuryForm with ICD-11

1. **Open the injury form**
2. **Test the ICD-11 diagnostic search**:
    - Type "fracture" in the diagnostic field
    - Select an ICD-11 code
    - Verify the injury type is automatically populated
    - Submit the form and verify the diagnostic is saved

### VaccinationRecordView

1. **Open the vaccination tab**
2. **Test all features**:
    - View statistics cards
    - Add new vaccination with ICD-11 search
    - Test filtering by status, vaccine, date range
    - Test pagination
    - Test FHIR sync button
    - Test export functionality

## 4. Expected Behaviors

### ICD-11 Search Input

-   ✅ Debounced search (300ms delay)
-   ✅ Dropdown with ICD-11 results
-   ✅ Keyboard navigation (arrow keys, enter, escape)
-   ✅ Error handling for API failures
-   ✅ Loading states
-   ✅ v-model support for selected diagnostic

### Vaccination Records

-   ✅ Statistics dashboard
-   ✅ Add/edit modal with form validation
-   ✅ ICD-11 integration for vaccine codes
-   ✅ FHIR sync functionality
-   ✅ Filtering and pagination
-   ✅ Export capabilities
-   ✅ Status badges and color coding

## 5. Troubleshooting

### Common Issues

1. **ICD-11 API not responding**:

    - Check environment variables
    - Verify API credentials
    - Check network connectivity

2. **Vaccination tab not showing**:

    - Verify component is imported correctly
    - Check browser console for errors
    - Ensure athlete ID is passed correctly

3. **FHIR sync failing**:
    - Check FHIR server configuration
    - Verify athlete has FHIR ID
    - Check network connectivity to FHIR server

### Debug Commands

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check API routes
php artisan route:list | grep -E "(icd11|immunisation)"

# Check database migration
php artisan migrate:status | grep immunisations

# Test database connection
php artisan tinker
>>> App\Models\Immunisation::count()
```

## 6. Performance Testing

### Load Testing

```bash
# Test ICD-11 search performance
ab -n 100 -c 10 "http://localhost:8000/api/v1/icd11/search?query=diabetes"

# Test vaccination list performance
ab -n 100 -c 10 "http://localhost:8000/api/v1/athletes/1/immunisations"
```

### Memory Usage

Monitor memory usage during:

-   Large vaccination record lists
-   ICD-11 search with many results
-   FHIR sync operations

## 7. Security Testing

### Authentication

-   ✅ Verify all endpoints require authentication
-   ✅ Test with invalid tokens
-   ✅ Test with expired tokens

### Authorization

-   ✅ Verify athlete data access controls
-   ✅ Test cross-athlete data access prevention
-   ✅ Verify FHIR sync permissions

## 8. Next Steps After Testing

1. **Configure production environment variables**
2. **Set up monitoring for API calls**
3. **Implement rate limiting for ICD-11 API**
4. **Set up alerts for FHIR sync failures**
5. **Train users on the new features**
6. **Document any customizations needed**
