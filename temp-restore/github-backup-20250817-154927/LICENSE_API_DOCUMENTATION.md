# License Management API Documentation

## Overview

The License Management API provides comprehensive functionality for managing player licenses in the Med-Predictor system. This API follows FIFA Connect standards and supports the complete license lifecycle from creation to renewal.

## Base URL

```
http://localhost:8000/api/v1
```

## Authentication

All API endpoints require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. License Management

#### Get All Licenses

```http
GET /licenses
```

**Query Parameters:**

-   `status` (string, optional): Filter by status (active, pending, expired, rejected, suspended)
-   `club_id` (integer, optional): Filter by club ID
-   `player_id` (integer, optional): Filter by player ID
-   `page` (integer, optional): Page number for pagination
-   `per_page` (integer, optional): Items per page (default: 15)

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "license_number": "LIC-202507-000001",
            "player": {
                "id": 1,
                "name": "John Doe",
                "position": "Forward",
                "nationality": "England"
            },
            "club": {
                "id": 1,
                "name": "Manchester United"
            },
            "status": "active",
            "license_type": "professional",
            "expires_at": "2026-07-17T00:00:00.000000Z",
            "created_at": "2025-07-17T00:00:00.000000Z",
            "updated_at": "2025-07-17T00:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

#### Get Single License

```http
GET /licenses/{id}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "license_number": "LIC-202507-000001",
        "player": {
            "id": 1,
            "name": "John Doe",
            "position": "Forward",
            "nationality": "England",
            "date_of_birth": "1995-03-15"
        },
        "club": {
            "id": 1,
            "name": "Manchester United",
            "country": "England"
        },
        "association": {
            "id": 1,
            "name": "The Football Association"
        },
        "status": "active",
        "license_type": "professional",
        "license_category": "senior",
        "contract_start_date": "2025-07-01",
        "contract_end_date": "2026-06-30",
        "salary": 50000.0,
        "medical_check_date": "2025-07-10",
        "expires_at": "2026-07-17T00:00:00.000000Z",
        "approved_at": "2025-07-15T10:30:00.000000Z",
        "approved_by": {
            "id": 1,
            "name": "Admin User"
        },
        "fifa_connect_id": "FIFA123456",
        "fifa_connect_status": "approved",
        "documents": [
            {
                "type": "passport",
                "url": "https://example.com/documents/passport.pdf",
                "uploaded_at": "2025-07-10T09:00:00.000000Z"
            }
        ],
        "created_at": "2025-07-17T00:00:00.000000Z",
        "updated_at": "2025-07-17T00:00:00.000000Z"
    }
}
```

#### Create License

```http
POST /licenses
```

**Request Body:**

```json
{
    "player_id": 1,
    "club_id": 1,
    "license_type": "professional",
    "license_category": "senior",
    "contract_start_date": "2025-07-01",
    "contract_end_date": "2026-06-30",
    "salary": 50000.0,
    "medical_check_date": "2025-07-10",
    "documents": [
        {
            "type": "passport",
            "file": "base64_encoded_file_content"
        }
    ]
}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "license_number": "LIC-202507-000001",
        "status": "pending",
        "message": "License created successfully"
    }
}
```

#### Update License

```http
PUT /licenses/{id}
```

**Request Body:**

```json
{
    "contract_end_date": "2027-06-30",
    "salary": 55000.0,
    "documents": [
        {
            "type": "contract_extension",
            "file": "base64_encoded_file_content"
        }
    ]
}
```

#### Delete License

```http
DELETE /licenses/{id}
```

### 2. License Actions

#### Approve License

```http
POST /licenses/{id}/approve
```

**Request Body:**

```json
{
    "approval_reason": "All requirements met",
    "fifa_connect_id": "FIFA123456"
}
```

#### Reject License

```http
POST /licenses/{id}/reject
```

**Request Body:**

```json
{
    "rejection_reason": "Missing medical certificate",
    "required_actions": "Please upload medical certificate"
}
```

#### Suspend License

```http
POST /licenses/{id}/suspend
```

**Request Body:**

```json
{
    "suspension_reason": "Disciplinary action",
    "suspension_duration": 30,
    "suspension_end_date": "2025-08-17"
}
```

#### Renew License

```http
POST /licenses/{id}/renew
```

**Request Body:**

```json
{
    "renewal_reason": "Contract extension",
    "new_contract_end_date": "2027-06-30",
    "medical_check_date": "2025-07-15",
    "documents": [
        {
            "type": "renewal_contract",
            "file": "base64_encoded_file_content"
        }
    ]
}
```

### 3. Bulk Operations

#### Bulk Approve Licenses

```http
POST /licenses/bulk-approve
```

**Request Body:**

```json
{
    "license_ids": [1, 2, 3],
    "approval_reason": "Bulk approval for new season"
}
```

#### Bulk Export Licenses

```http
GET /licenses/export
```

**Query Parameters:**

-   `club_id` (integer, optional): Export licenses for specific club
-   `status` (string, optional): Filter by status
-   `format` (string, optional): Export format (xlsx, csv, pdf)

### 4. License Templates

#### Get All Templates

```http
GET /license-templates
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Senior Professional Player",
            "code": "senior_professional",
            "description": "License template for senior professional players",
            "required_fields": ["player_name", "date_of_birth", "nationality"],
            "optional_fields": ["emergency_contact"],
            "validity_period_months": 12,
            "fee": 500.0,
            "requires_medical_check": true,
            "requires_documents": true,
            "document_requirements": ["passport", "medical_certificate"],
            "is_active": true
        }
    ]
}
```

#### Create Template

```http
POST /license-templates
```

**Request Body:**

```json
{
    "name": "Youth Player",
    "code": "youth_player",
    "description": "License template for youth players",
    "required_fields": ["player_name", "date_of_birth", "parent_consent"],
    "optional_fields": ["emergency_contact"],
    "validation_rules": {
        "age_min": 16,
        "age_max": 18
    },
    "validity_period_months": 6,
    "fee": 200.0,
    "requires_medical_check": true,
    "requires_documents": true,
    "document_requirements": ["passport", "birth_certificate"]
}
```

### 5. Audit Trail

#### Get License Audit Trail

```http
GET /licenses/{id}/audit-trail
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "action": "created",
            "old_status": null,
            "new_status": "pending",
            "reason": "Initial license creation",
            "user": {
                "id": 1,
                "name": "Club Manager"
            },
            "created_at": "2025-07-17T10:00:00.000000Z"
        },
        {
            "id": 2,
            "action": "approved",
            "old_status": "pending",
            "new_status": "active",
            "reason": "All requirements met",
            "user": {
                "id": 2,
                "name": "Association Admin"
            },
            "created_at": "2025-07-17T14:30:00.000000Z"
        }
    ]
}
```

### 6. Compliance Reports

#### Get Compliance Report

```http
GET /licenses/compliance-report
```

**Query Parameters:**

-   `club_id` (integer, optional): Report for specific club
-   `date_from` (date, optional): Start date for report
-   `date_to` (date, optional): End date for report

**Response:**

```json
{
    "data": {
        "total_licenses": 150,
        "active_licenses": 120,
        "pending_licenses": 20,
        "expired_licenses": 8,
        "rejected_licenses": 2,
        "compliance_rate": 80.0,
        "licenses_needing_renewal": 15,
        "licenses_by_status": {
            "active": 120,
            "pending": 20,
            "expired": 8,
            "rejected": 2
        },
        "licenses_by_month": {
            "2025-07": 25,
            "2025-06": 30,
            "2025-05": 20
        }
    }
}
```

### 7. FIFA Connect Integration

#### Sync with FIFA Connect

```http
POST /licenses/{id}/sync-fifa
```

**Response:**

```json
{
    "data": {
        "fifa_connect_id": "FIFA123456",
        "fifa_connect_status": "synced",
        "last_sync": "2025-07-17T15:00:00.000000Z"
    }
}
```

#### Get FIFA Connect Status

```http
GET /fifa-connect/status
```

**Response:**

```json
{
    "data": {
        "connection_status": "connected",
        "last_sync": "2025-07-17T15:00:00.000000Z",
        "pending_syncs": 5,
        "api_version": "2.1"
    }
}
```

## Error Responses

### Validation Error

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "player_id": ["The player id field is required."],
        "contract_end_date": [
            "The contract end date must be a date after contract start date."
        ]
    }
}
```

### Not Found Error

```json
{
    "message": "License not found.",
    "error_code": "LICENSE_NOT_FOUND"
}
```

### Unauthorized Error

```json
{
    "message": "Unauthenticated.",
    "error_code": "UNAUTHORIZED"
}
```

### Forbidden Error

```json
{
    "message": "You do not have permission to perform this action.",
    "error_code": "FORBIDDEN"
}
```

## Rate Limiting

The API implements rate limiting to ensure fair usage:

-   **Authenticated users**: 1000 requests per hour
-   **Unauthenticated users**: 60 requests per hour

Rate limit headers are included in responses:

```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## Webhooks

The API supports webhooks for real-time notifications:

### Webhook Events

-   `license.created`
-   `license.updated`
-   `license.approved`
-   `license.rejected`
-   `license.suspended`
-   `license.renewed`
-   `license.expired`

### Webhook Payload Example

```json
{
    "event": "license.approved",
    "data": {
        "license_id": 1,
        "license_number": "LIC-202507-000001",
        "player_name": "John Doe",
        "club_name": "Manchester United",
        "old_status": "pending",
        "new_status": "active",
        "approved_by": "Association Admin",
        "approved_at": "2025-07-17T14:30:00.000000Z"
    },
    "timestamp": "2025-07-17T14:30:00.000000Z"
}
```

## SDKs and Libraries

### PHP Laravel

```php
use App\Models\PlayerLicense;

// Create license
$license = PlayerLicense::create([
    'player_id' => 1,
    'club_id' => 1,
    'license_type' => 'professional'
]);

// Approve license
$license->approve('All requirements met');
```

### JavaScript/Node.js

```javascript
const axios = require("axios");

// Get licenses
const response = await axios.get("/api/v1/licenses", {
    headers: {
        Authorization: `Bearer ${token}`,
    },
});

// Create license
const license = await axios.post(
    "/api/v1/licenses",
    {
        player_id: 1,
        club_id: 1,
        license_type: "professional",
    },
    {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    }
);
```

## Support

For API support and questions:

-   Email: api-support@med-predictor.com
-   Documentation: https://docs.med-predictor.com/api
-   Status Page: https://status.med-predictor.com

## Changelog

### v1.0.0 (2025-07-17)

-   Initial release
-   Basic CRUD operations for licenses
-   FIFA Connect integration
-   Audit trail functionality
-   Bulk operations
-   Compliance reporting
