# Match Sheet System Specification

## Overview

The Match Sheet System is a comprehensive digital solution for managing football match documentation, including all structured data categories required for professional league-level implementation.

## System Architecture

### 1. Data Models

#### MatchSheet Model

-   **Primary Table**: `match_sheets`
-   **Key Fields**:
    -   `match_id` - Foreign key to game_matches
    -   `match_number` - Unique match identifier
    -   `stadium_venue` - Match location
    -   `weather_conditions` - Weather during match
    -   `pitch_conditions` - Pitch quality assessment
    -   `kickoff_time` - Match start time
    -   `home_team_roster` - JSON array of starting XI
    -   `away_team_roster` - JSON array of starting XI
    -   `home_team_substitutes` - JSON array of substitutes
    -   `away_team_substitutes` - JSON array of substitutes
    -   `home_team_coach` - Home team coach name
    -   `away_team_coach` - Away team coach name
    -   `home_team_manager` - Home team manager name
    -   `away_team_manager` - Away team manager name
    -   `main_referee_id` - Main referee user ID
    -   `assistant_referee_1_id` - First assistant referee
    -   `assistant_referee_2_id` - Second assistant referee
    -   `fourth_official_id` - Fourth official
    -   `var_referee_id` - VAR referee
    -   `var_assistant_id` - VAR assistant
    -   `match_statistics` - JSON object with match stats
    -   `home_team_score` - Final home team score
    -   `away_team_score` - Final away team score
    -   `referee_report` - Detailed match report
    -   `match_status` - completed/suspended/abandoned
    -   `suspension_reason` - Reason if match suspended
    -   `crowd_issues` - Crowd-related incidents
    -   `protests_incidents` - Protests or incidents
    -   `notes` - Additional notes
    -   `home_team_signature` - Digital signature
    -   `away_team_signature` - Digital signature
    -   `home_team_signed_at` - Signature timestamp
    -   `away_team_signed_at` - Signature timestamp
    -   `match_observer_id` - Observer user ID
    -   `observer_comments` - Observer notes
    -   `observer_signed_at` - Observer signature time
    -   `referee_digital_signature` - Referee signature
    -   `referee_signed_at` - Referee signature time
    -   `penalty_shootout_data` - JSON penalty data
    -   `var_decisions` - JSON VAR decisions
    -   `status` - draft/submitted/validated/rejected
    -   `version` - Match sheet version number
    -   `change_log` - JSON change history

#### MatchEvent Model

-   **Primary Table**: `match_events`
-   **Key Fields**:
    -   `match_id` - Foreign key to game_matches
    -   `player_id` - Player involved
    -   `team_id` - Team involved
    -   `event_type` - Type of event (goal, card, substitution, etc.)
    -   `minute` - Match minute
    -   `extra_time_minute` - Extra time minute
    -   `period` - Match period
    -   `description` - Event description
    -   `location` - Event location on pitch
    -   `severity` - Event severity (low/medium/high)
    -   `assisted_by_player_id` - Player who assisted
    -   `substituted_player_id` - Player substituted
    -   `recorded_by_user_id` - User who recorded event
    -   `is_confirmed` - Event confirmation status
    -   `is_contested` - Event contestation status

#### MatchRoster Model

-   **Primary Table**: `match_rosters`
-   **Key Fields**:
    -   `match_id` - Foreign key to game_matches
    -   `team_id` - Team ID
    -   `submitted_at` - Roster submission time

#### MatchRosterPlayer Model

-   **Primary Table**: `match_roster_players`
-   **Key Fields**:
    -   `match_roster_id` - Foreign key to match_rosters
    -   `player_id` - Player ID
    -   `jersey_number` - Player jersey number
    -   `is_starter` - Whether player is in starting XI
    -   `position` - Player position

## 2. Structured Data Categories

### 2.1 Match Information

-   Competition name and season
-   Matchday number
-   Date and kickoff time
-   Stadium/venue
-   Match number/code
-   Weather conditions
-   Pitch conditions

### 2.2 Teams (Home & Away)

-   Team names and federation IDs
-   Coach and staff names
-   Team logos/identifiers

### 2.3 Player Rosters

-   Starting XI for each team
-   Substitutes for each team
-   Player name, jersey number, position, and player ID

### 2.4 Match Events

-   **Goals**: scorer, minute, type (regular, penalty, own goal)
-   **Cards**: yellow, red, second yellow; player and reason
-   **Substitutions**: player in/out, minute, reason
-   **Penalty shootout data** (if applicable)
-   **VAR decisions** with timestamps

### 2.5 Officials

-   Main referee, assistants, 4th official, VAR team
-   Referee IDs and roles
-   Digital signatures

### 2.6 Referee Match Report

-   Narrative remarks (injuries, protests, crowd issues)
-   Match status (completed, suspended, abandoned)
-   Digital signature with timestamp

### 2.7 Validation & Admin

-   Team representative signatures
-   Match observer comments
-   Admin validation workflow
-   Rejection handling with reasons

### 2.8 Access Roles

-   **Referee**: Can input and edit events pre-validation
-   **Team Manager**: Can submit lineups before match
-   **Admin**: Validates and finalizes report
-   **Public**: View only after match validation

### 2.9 Match Statistics (Optional)

-   Possession, shots, fouls, corners, offsides
-   Player metrics (distance, passes, heatmap)

## 3. System Features

### 3.1 UI Components

-   **Create Match Sheet**: For matches not yet played
-   **Edit Match Sheet**: For referees to input data
-   **View Match Sheet**: For all users after validation
-   **Validate Match**: For admin role
-   **Reject Match**: For admin role with reasons

### 3.2 Role-Based Access Control

-   **Referee Permissions**:

    -   Edit match sheet in draft status
    -   Add/remove match events
    -   Submit match sheet for validation
    -   View their assigned matches

-   **Admin Permissions**:

    -   View all match sheets
    -   Validate submitted match sheets
    -   Reject match sheets with reasons
    -   Export match sheet data
    -   Edit any match sheet

-   **Public Permissions**:
    -   View validated match sheets only

### 3.3 Workflow States

1. **Draft**: Initial state, referees can edit
2. **Submitted**: Referee has submitted for validation
3. **Validated**: Admin has approved the match sheet
4. **Rejected**: Admin has rejected, referee can resubmit

### 3.4 Data Export

-   JSON export functionality
-   Database compatibility
-   Structured data format
-   Version control with change logs

## 4. Controller Methods

### MatchSheetController

-   `show()` - Display match sheet
-   `edit()` - Edit match sheet form
-   `update()` - Update match sheet data
-   `submit()` - Submit for validation
-   `validate()` - Admin validation
-   `reject()` - Admin rejection
-   `addEvent()` - Add match event
-   `removeEvent()` - Remove match event
-   `export()` - Export to JSON

## 5. Routes

```php
// Match sheet routes
Route::get('/matches/{match}/match-sheet', [MatchSheetController::class, 'show']);
Route::get('/matches/{match}/match-sheet/edit', [MatchSheetController::class, 'edit']);
Route::put('/matches/{match}/match-sheet', [MatchSheetController::class, 'update']);
Route::post('/matches/{match}/match-sheet/submit', [MatchSheetController::class, 'submit']);
Route::post('/matches/{match}/match-sheet/validate', [MatchSheetController::class, 'validate']);
Route::post('/matches/{match}/match-sheet/reject', [MatchSheetController::class, 'reject']);
Route::post('/matches/{match}/match-sheet/events', [MatchSheetController::class, 'addEvent']);
Route::delete('/matches/{match}/match-sheet/events/{event}', [MatchSheetController::class, 'removeEvent']);
Route::get('/matches/{match}/match-sheet/export', [MatchSheetController::class, 'export']);
```

## 6. Views

### 6.1 Edit View (`edit.blade.php`)

-   Comprehensive form with all data categories
-   Color-coded sections for different data types
-   Role-based form controls
-   Validation feedback
-   Submit for validation workflow

### 6.2 Show View (`show.blade.php`)

-   Read-only display of match sheet data
-   Event timeline
-   Team rosters
-   Official information
-   Validation status and history

## 7. Security Features

### 7.1 Permission Checks

-   Role-based access control
-   Match-specific permissions
-   Status-based editing restrictions

### 7.2 Data Validation

-   Input validation for all fields
-   Required field enforcement
-   Data type validation
-   Business rule validation

### 7.3 Audit Trail

-   Change logging
-   Version control
-   Signature tracking
-   Timestamp recording

## 8. Integration Points

### 8.1 Competition Management

-   Links to standings page
-   Match result integration
-   Team roster management

### 8.2 User Management

-   Referee role assignment
-   Admin role management
-   Team manager permissions

### 8.3 Data Export

-   JSON format for external systems
-   Database backup compatibility
-   API integration ready

## 9. Future Enhancements

### 9.1 Mobile Support

-   Mobile-responsive design
-   Offline capability
-   Real-time synchronization

### 9.2 Advanced Analytics

-   Match statistics dashboard
-   Performance metrics
-   Trend analysis

### 9.3 Integration APIs

-   FIFA Connect integration
-   External system APIs
-   Real-time data feeds

## 10. Technical Specifications

### 10.1 Database

-   MySQL/PostgreSQL compatible
-   JSON field support
-   Foreign key constraints
-   Index optimization

### 10.2 Performance

-   Eager loading for relationships
-   Database query optimization
-   Caching strategies
-   Pagination for large datasets

### 10.3 Scalability

-   Modular architecture
-   Extensible data models
-   Plugin system ready
-   Multi-tenant support

This comprehensive match sheet system provides a professional, scalable solution for digital match documentation with full role-based access control, validation workflows, and data export capabilities suitable for league-level implementation.
