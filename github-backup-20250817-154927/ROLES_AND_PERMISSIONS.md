# Med Predictor - Roles and Permissions Structure

## Overview

Based on the FIFA data model, users in Med Predictor can act as either **Clubs** or **Football Associations**, each with distinct roles and permissions aligned with their real-world responsibilities.

## FIFA Data Model Integration

### Core FIFA Entities

-   **Players**: FIFA ID, personal data, performance metrics
-   **Clubs**: FIFA Club ID, registration data, team composition
-   **Associations**: FIFA Association ID, regulatory oversight
-   **Competitions**: FIFA Competition ID, tournament management
-   **Licenses**: FIFA License ID, player eligibility

## Role Definitions

### 1. CLUB ROLES

#### Club Administrator

**Responsibilities:**

-   Manage club profile and FIFA Connect integration
-   Oversee player registration and licensing
-   Coordinate with football associations
-   Access club-specific analytics

**Permissions:**

-   Full access to Player Registration module
-   Limited access to Competition Management (club-specific)
-   Full access to Healthcare module (club players only)
-   View club-specific FIFA data
-   Manage club teams and lineups
-   Generate player licenses for club members

**FIFA Data Access:**

-   Club players only
-   Club-specific competitions
-   Club licensing data

#### Club Manager

**Responsibilities:**

-   Manage player registrations
-   Handle team composition
-   Coordinate with medical staff
-   Monitor player performance

**Permissions:**

-   Access Player Registration module
-   View Competition Management (club-specific)
-   Access Healthcare module (assigned players)
-   Generate and manage player licenses
-   View FIFA Connect data for club players

#### Club Medical Staff

**Responsibilities:**

-   Manage player health records
-   Generate medical predictions
-   Monitor player fitness
-   Coordinate with associations on medical matters

**Permissions:**

-   Limited access to Player Registration (view only)
-   Access Healthcare module (full access)
-   View FIFA Connect health data
-   Generate medical predictions
-   Submit health reports to associations

### 2. FOOTBALL ASSOCIATION ROLES

#### Association Administrator

**Responsibilities:**

-   Oversee all clubs and players in jurisdiction
-   Manage competition structure
-   Enforce FIFA regulations
-   Coordinate with FIFA directly

**Permissions:**

-   Full access to all modules
-   Oversight of all clubs in jurisdiction
-   Competition management and oversight
-   Player licensing approval
-   FIFA Connect administrative access

**FIFA Data Access:**

-   All players in jurisdiction
-   All clubs in jurisdiction
-   All competitions in jurisdiction
-   Administrative FIFA data

#### Association Registrar

**Responsibilities:**

-   Process player registrations
-   Verify FIFA Connect data
-   Manage licensing approvals
-   Maintain association records

**Permissions:**

-   Full access to Player Registration module
-   Access Competition Management (oversight)
-   Limited access to Healthcare (reports only)
-   Approve/reject player licenses
-   Verify FIFA Connect data integrity

#### Association Medical Director

**Responsibilities:**

-   Oversee medical standards
-   Review health records
-   Approve medical clearances
-   Coordinate with FIFA medical committee

**Permissions:**

-   Access Player Registration (medical data only)
-   View Competition Management (medical aspects)
-   Full access to Healthcare module
-   Approve medical clearances
-   Generate association-wide health reports

## Module Access Matrix

| Role                             | Player Registration | Competition Management | Healthcare     | FIFA Connect |
| -------------------------------- | ------------------- | ---------------------- | -------------- | ------------ |
| **Club Administrator**           | Full (Club)         | Limited (Club)         | Full (Club)    | Club Data    |
| **Club Manager**                 | Full (Club)         | View (Club)            | Limited (Club) | Club Data    |
| **Club Medical Staff**           | View (Club)         | None                   | Full (Club)    | Health Data  |
| **Association Administrator**    | Full (All)          | Full (All)             | Full (All)     | All Data     |
| **Association Registrar**        | Full (All)          | Oversight              | Reports        | All Data     |
| **Association Medical Director** | Medical Only        | Medical Only           | Full (All)     | Health Data  |

## FIFA Data Model Integration

### Player Registration Module

-   **FIFA Player ID**: Unique identifier for each player
-   **FIFA Club ID**: Links player to registered club
-   **FIFA Association ID**: Links player to governing association
-   **Registration Status**: Active, Suspended, Retired
-   **License Status**: Valid, Pending, Expired, Revoked

### Competition Management Module

-   **FIFA Competition ID**: Unique tournament identifier
-   **Participating Clubs**: FIFA Club IDs
-   **Player Eligibility**: Based on FIFA license status
-   **Match Results**: Integrated with FIFA statistics

### Healthcare Module

-   **Medical Clearance**: Required for FIFA competitions
-   **Health Records**: Linked to FIFA Player ID
-   **Medical Predictions**: Based on FIFA performance data
-   **Association Oversight**: Medical standards enforcement

## Implementation Notes

### Database Schema

-   Users table includes `role` and `entity_type` (club/association)
-   Entity relationships link users to specific clubs or associations
-   FIFA Connect integration maintains data consistency

### Security Considerations

-   Role-based access control (RBAC) implementation
-   FIFA data access restrictions based on jurisdiction
-   Audit logging for all FIFA data access
-   Secure API integration with FIFA Connect

### User Experience

-   Dashboard adapts based on user role and entity
-   Navigation shows only accessible modules
-   FIFA Connect status prominently displayed
-   Real-time data synchronization

## Next Steps

1. Implement role-based middleware
2. Create entity-specific dashboards
3. Integrate FIFA Connect API with role restrictions
4. Develop audit logging system
5. Create user onboarding flow for role assignment
