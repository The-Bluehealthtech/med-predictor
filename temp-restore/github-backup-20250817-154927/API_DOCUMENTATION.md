# Med Predictor API Documentation

## Overview

The Med Predictor API is a RESTful API built with Laravel 10+ and Laravel Sanctum for authentication. It provides comprehensive endpoints for managing football players, clubs, licenses, and medical records with multi-tenancy support.

## Base URL

```
https://your-domain.com/api/v1
```

## Authentication

The API uses Laravel Sanctum for token-based authentication.

### Login

**POST** `/auth/login`

Request body:

```json
{
    "email": "user@example.com",
    "password": "password",
    "device_name": "Mobile App" // Optional
}
```

Response:

```json
{
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "admin",
            "association_id": null,
            "club_id": null,
            "status": "active",
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Using the Token

Include the token in the Authorization header:

```
Authorization: Bearer 1|abc123...
```

### Profile

**GET** `/auth/profile`

Returns the authenticated user's profile.

### Logout

**POST** `/auth/logout`

Revokes the current token.

### Refresh Token

**POST** `/auth/refresh`

Revokes all existing tokens and returns a new one.

## Players

### List Players

**GET** `/players`

Query parameters:

-   `club_id` (optional): Filter by club
-   `team_id` (optional): Filter by team
-   `status` (optional): Filter by status (active, inactive, suspended)
-   `search` (optional): Search by name or email
-   `per_page` (optional): Number of items per page (default: 15)

Response:

```json
{
    "message": "Players retrieved successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "full_name": "John Doe",
                "email": "john.doe@example.com",
                "phone": "+1234567890",
                "date_of_birth": "1995-01-01",
                "age": 29,
                "nationality": "English",
                "position": "Forward",
                "club_id": 1,
                "team_id": 1,
                "status": "active",
                "fifa_connect_id": "FIFA123",
                "emergency_contact_name": "Jane Doe",
                "emergency_contact_phone": "+1234567890",
                "medical_conditions": "None",
                "allergies": "None",
                "blood_type": "O+",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z",
                "club": {
                    "id": 1,
                    "name": "Manchester United",
                    "short_name": "MUFC"
                },
                "team": {
                    "id": 1,
                    "name": "First Team",
                    "short_name": "1ST"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75,
            "from": 1,
            "to": 15,
            "has_more_pages": true
        }
    }
}
```

### Create Player

**POST** `/players`

Request body:

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "date_of_birth": "1995-01-01",
    "nationality": "English",
    "position": "Forward",
    "club_id": 1,
    "team_id": 1,
    "status": "active",
    "fifa_connect_id": "FIFA123",
    "emergency_contact_name": "Jane Doe",
    "emergency_contact_phone": "+1234567890",
    "medical_conditions": "None",
    "allergies": "None",
    "blood_type": "O+"
}
```

### Get Player

**GET** `/players/{id}`

Returns detailed player information including relationships.

### Update Player

**PUT** `/players/{id}`

Request body: Same as create, but all fields are optional.

### Delete Player

**DELETE** `/players/{id}`

### Get Player Profile

**GET** `/players/{id}/profile`

Returns player with recent health records, licenses, and statistics.

### Get Player Statistics

**GET** `/players/{id}/statistics`

Returns player's season statistics.

## Clubs

### List Clubs

**GET** `/clubs`

Query parameters:

-   `association_id` (optional): Filter by association
-   `status` (optional): Filter by status (active, inactive, suspended)
-   `search` (optional): Search by name
-   `per_page` (optional): Number of items per page (default: 15)

### Create Club

**POST** `/clubs`

Request body:

```json
{
    "name": "Manchester United",
    "short_name": "MUFC",
    "association_id": 1,
    "status": "active",
    "founded_year": 1878,
    "address": "Old Trafford, Sir Matt Busby Way",
    "city": "Manchester",
    "postal_code": "M16 0RA",
    "country": "England",
    "phone": "+441616868000",
    "email": "info@manutd.com",
    "website": "https://www.manutd.com",
    "logo_url": "https://example.com/logo.png",
    "description": "Professional football club"
}
```

### Get Club

**GET** `/clubs/{id}`

### Update Club

**PUT** `/clubs/{id}`

### Delete Club

**DELETE** `/clubs/{id}`

### Get Club Players

**GET** `/clubs/{id}/players`

### Get Club Teams

**GET** `/clubs/{id}/teams`

### Get Club Statistics

**GET** `/clubs/{id}/statistics`

Returns club statistics including player counts, team counts, and license information.

## Error Handling

The API returns consistent error responses:

### Validation Errors (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Errors (401)

```json
{
    "message": "Unauthenticated."
}
```

### Authorization Errors (403)

```json
{
    "message": "Forbidden. Insufficient permissions.",
    "error": "INSUFFICIENT_PERMISSIONS"
}
```

### Not Found Errors (404)

```json
{
    "message": "Resource not found."
}
```

### Server Errors (500)

```json
{
    "message": "Internal server error.",
    "error": "INTERNAL_ERROR"
}
```

## Multi-Tenancy

The API supports multi-tenancy through the TenantScope global scope. Users are automatically scoped to their association or club based on their role:

-   **System Admin**: Can access all data
-   **Association Users**: Scoped to their association
-   **Club Users**: Scoped to their club

## Rate Limiting

The API implements rate limiting to prevent abuse:

-   **Authentication endpoints**: 5 requests per minute
-   **Other endpoints**: 60 requests per minute

## Pagination

All list endpoints support pagination with the following parameters:

-   `page`: Page number (default: 1)
-   `per_page`: Items per page (default: 15, max: 100)

## Filtering and Search

Most list endpoints support filtering and search:

-   **Filtering**: Use query parameters like `club_id`, `status`, etc.
-   **Search**: Use the `search` parameter for text-based searches
-   **Sorting**: Use `sort` and `order` parameters (implementation varies by endpoint)

## Audit Trail

All create, update, and delete operations are automatically logged in the audit trail for compliance and security purposes.

## Security Features

-   **Token-based authentication** with Laravel Sanctum
-   **Role-based access control** with policies
-   **Multi-tenancy** with automatic data scoping
-   **Input validation** with comprehensive request classes
-   **Audit logging** for all data modifications
-   **Rate limiting** to prevent abuse
-   **CORS support** for cross-origin requests

## SDKs and Libraries

### JavaScript/TypeScript

```javascript
// Using fetch
const response = await fetch("/api/v1/players", {
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// Using axios
const response = await axios.get("/api/v1/players", {
    headers: {
        Authorization: `Bearer ${token}`,
    },
});
```

### PHP

```php
// Using Guzzle
$response = $client->get('/api/v1/players', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ]
]);
```

## Testing

The API includes comprehensive test coverage. To run tests:

```bash
php artisan test --filter=Api
```

## Support

For API support and questions, please contact the development team or refer to the internal documentation.
