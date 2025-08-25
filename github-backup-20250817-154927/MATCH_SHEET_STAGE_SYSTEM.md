# Match Sheet Stage-Based Lifecycle System

## Overview

The Med Predictor application now features a comprehensive 4-stage match sheet lifecycle system that manages the entire process from match creation to final FA validation. This system ensures proper workflow, role-based access control, and audit trails for all match-related activities.

## 🎯 Core Functional Requirements

### Match Sheet Lifecycle (4 Stages)

#### Stage 1 – In Progress

-   **Status**: `in_progress`
-   **Description**: Match created, teams can input line-ups
-   **Actions Available**:
    -   FA Admins can assign referees
    -   Team officials can enter and edit lineups
    -   Team officials can sign lineups
-   **Data Editable**:
    -   Team rosters (starting 11 + substitutes)
    -   Match metadata (stadium, weather, kickoff time)
    -   Team staff information

#### Stage 2 – Before Game Signed

-   **Status**: `before_game_signed`
-   **Description**: Line-ups locked after both teams sign; match events can be entered by referee
-   **Actions Available**:
    -   Referees can input match events (goals, cards, substitutions)
    -   Referees can write referee report
    -   Referees can sign match sheet
-   **Data Editable**:
    -   Match events (goals, cards, substitutions, penalties)
    -   Referee report and comments
    -   Match statistics

#### Stage 3 – After Game Signed

-   **Status**: `after_game_signed`
-   **Description**: Referee signs after entering match events; team officials also sign and comment
-   **Actions Available**:
    -   Team officials can sign post-match
    -   Team officials can add post-match comments
    -   FA Admins can validate final sheet
-   **Data Editable**:
    -   Team post-match signatures
    -   Team post-match comments
    -   FA validation notes

#### Stage 4 – FA Validated

-   **Status**: `fa_validated`
-   **Description**: Final validation by federation/association; sheet locked permanently
-   **Actions Available**:
    -   Read-only access for all users
    -   Export functionality
-   **Data Editable**: None (locked)

### Actors & Roles

#### Referee

-   Can input match events, write report, and sign
-   Assigned by FA Admin in Stage 1
-   Can edit match events in Stage 2
-   Can sign match sheet after entering all events

#### Team Official (Club Admin/Manager)

-   Can enter and sign line-ups in Stage 1
-   Can comment post-match in Stage 3
-   Can sign post-match in Stage 3
-   Access limited to their team's data

#### FA Admin (Association Admin/System Admin)

-   Can assign referees in Stage 1
-   Can validate final sheet in Stage 3
-   Can view all match sheets
-   Can export validated sheets

## 🧱 Technical Implementation

### Database Schema

#### New Fields Added to `match_sheets` Table

```sql
-- Stage-based lifecycle fields
stage ENUM('in_progress', 'before_game_signed', 'after_game_signed', 'fa_validated') DEFAULT 'in_progress'

-- Stage timestamps
stage_in_progress_at TIMESTAMP NULL
stage_before_game_signed_at TIMESTAMP NULL
stage_after_game_signed_at TIMESTAMP NULL
stage_fa_validated_at TIMESTAMP NULL

-- Team lineup signatures
home_team_lineup_signature VARCHAR(255) NULL
away_team_lineup_signature VARCHAR(255) NULL
home_team_lineup_signed_at TIMESTAMP NULL
away_team_lineup_signed_at TIMESTAMP NULL

-- Team post-match signatures and comments
home_team_post_match_signature VARCHAR(255) NULL
away_team_post_match_signature VARCHAR(255) NULL
home_team_post_match_signed_at TIMESTAMP NULL
away_team_post_match_signed_at TIMESTAMP NULL
home_team_post_match_comments TEXT NULL
away_team_post_match_comments TEXT NULL

-- FA validation fields
fa_validated_by BIGINT UNSIGNED NULL (FK to users.id)
fa_validation_notes TEXT NULL

-- Referee assignment
assigned_referee_id BIGINT UNSIGNED NULL (FK to users.id)
referee_assigned_at TIMESTAMP NULL

-- Lock status
lineups_locked BOOLEAN DEFAULT FALSE
lineups_locked_at TIMESTAMP NULL
match_events_locked BOOLEAN DEFAULT FALSE
match_events_locked_at TIMESTAMP NULL

-- Audit trail
stage_transition_log JSON NULL
user_action_log JSON NULL
```

#### New Fields Added to `users` Table

```sql
team_id BIGINT UNSIGNED NULL (FK to teams.id)
```

### Models

#### MatchSheet Model Enhancements

```php
// Stage Methods
public function getStageLabelAttribute(): string
public function getStageColorAttribute(): string
public function isInProgress(): bool
public function isBeforeGameSigned(): bool
public function isAfterGameSigned(): bool
public function isFaValidated(): bool
public function canTransitionToStage(string $newStage): bool
public function transitionToStage(string $newStage, User $user): bool
public function logStageTransition(string $newStage, User $user): void
public function logUserAction(string $action, User $user, array $details = []): void

// Permission Methods
public function canTeamOfficialEdit(User $user): bool
public function canRefereeEdit(User $user): bool
public function canFaAdminValidate(User $user): bool
public function canAssignReferee(User $user): bool
public function canSignLineup(User $user, string $teamType): bool
public function canSignPostMatch(User $user, string $teamType): bool

// Business Logic Methods
public function assignReferee(User $referee, User $admin): bool
public function signLineup(string $teamType, User $user, string $signature): bool
public function lockLineups(): void
public function unlockLineups(): void
public function lockMatchEvents(): void
public function unlockMatchEvents(): void
public function signPostMatch(string $teamType, User $user, string $signature, ?string $comments = null): bool
public function faValidate(User $admin, ?string $notes = null): bool
public function getStageProgress(): array
```

#### User Model Enhancements

```php
// New relationship
public function team(): BelongsTo

// New role method
public function isTeamOfficial(): bool
```

### Controllers

#### MatchSheetController New Methods

```php
public function assignReferee(Request $request, GameMatch $match)
public function signLineup(Request $request, GameMatch $match)
public function signPostMatch(Request $request, GameMatch $match)
public function faValidate(Request $request, GameMatch $match)
```

### Routes

#### New Stage-Based Routes

```php
// Stage-based match sheet routes
Route::post('/matches/{match}/match-sheet/assign-referee', [MatchSheetController::class, 'assignReferee'])
Route::post('/matches/{match}/match-sheet/sign-lineup', [MatchSheetController::class, 'signLineup'])
Route::post('/matches/{match}/match-sheet/sign-post-match', [MatchSheetController::class, 'signPostMatch'])
Route::post('/matches/{match}/match-sheet/fa-validate', [MatchSheetController::class, 'faValidate'])
```

## 🛡️ Security & Audit

### Role-Based Access Control

#### Stage 1 (In Progress)

-   **FA Admin**: Can assign referees, view all data
-   **Team Official**: Can edit lineups for their team, sign lineups
-   **Referee**: Read-only access

#### Stage 2 (Before Game Signed)

-   **FA Admin**: Can view all data, assign referees
-   **Team Official**: Read-only access to lineups
-   **Assigned Referee**: Can edit match events, write report, sign

#### Stage 3 (After Game Signed)

-   **FA Admin**: Can validate final sheet, view all data
-   **Team Official**: Can sign post-match, add comments
-   **Referee**: Read-only access

#### Stage 4 (FA Validated)

-   **All Users**: Read-only access
-   **FA Admin**: Can export data

### Audit Trail

#### Stage Transition Log

```json
[
    {
        "from_stage": "in_progress",
        "to_stage": "before_game_signed",
        "user_id": 123,
        "user_name": "John Doe",
        "timestamp": "2025-07-15T10:30:00Z"
    }
]
```

#### User Action Log

```json
[
    {
        "action": "lineup_signed",
        "user_id": 123,
        "user_name": "John Doe",
        "timestamp": "2025-07-15T10:30:00Z",
        "details": {
            "team_type": "home"
        }
    }
]
```

## 📊 Data Models

### Match Sheet Data Includes

#### Match Metadata

-   Date, stadium, competition, weather
-   Kickoff time, pitch conditions
-   Match number, version

#### Teams

-   Names, coaches, staff
-   Lineup signatures and timestamps
-   Post-match signatures and comments

#### Players

-   Starting 11 + substitutes
-   Positions, jersey numbers
-   Squad numbers, roles

#### Match Events

-   Goals, cards, substitutions
-   Penalties, VAR decisions
-   Timestamps, descriptions

#### Officials

-   Assigned referee
-   Assistant referees
-   VAR officials
-   Referee report and signature

#### Validation

-   FA validation timestamp
-   Validation notes
-   Validator information

## 🎨 UI Components

### Stage Progress Indicator

-   Visual progress bar showing current stage
-   Color-coded stages (blue, yellow, orange, green)
-   Timestamps for each stage transition

### Role-Based Action Buttons

-   **FA Admin**: Assign referee, validate sheet
-   **Team Official**: Sign lineup, sign post-match
-   **Referee**: Add events, sign match sheet

### Dynamic Forms

-   Lineup forms per team with validation
-   Match event logger for referees
-   Signature buttons with state tracking

### Status Display

-   Current stage with color coding
-   Lock status indicators
-   Signature status for teams

## 🔄 Workflow Examples

### Complete Match Sheet Workflow

1. **Stage 1 - In Progress**

    - FA Admin assigns referee to match
    - Team officials enter lineups
    - Both teams sign lineups
    - System automatically transitions to Stage 2

2. **Stage 2 - Before Game Signed**

    - Referee enters match events during/after game
    - Referee writes report and signs
    - System transitions to Stage 3

3. **Stage 3 - After Game Signed**

    - Team officials sign post-match
    - Team officials add comments
    - FA Admin validates final sheet
    - System transitions to Stage 4

4. **Stage 4 - FA Validated**
    - Sheet is locked permanently
    - All users can view but not edit
    - FA Admin can export data

## 🚀 Future Enhancements

### Planned Features

-   Email notifications for stage transitions
-   Mobile app support for referees
-   Real-time collaboration features
-   Advanced analytics and reporting
-   Integration with video analysis tools
-   Automated event detection from video feeds

### Technical Improvements

-   WebSocket support for real-time updates
-   Advanced caching for performance
-   API rate limiting and security
-   Comprehensive testing suite
-   Performance monitoring and logging

## 📝 Usage Examples

### Assigning a Referee (FA Admin)

```php
$matchSheet->assignReferee($referee, $admin);
```

### Signing Lineup (Team Official)

```php
$matchSheet->signLineup('home', $user, 'digital_signature_here');
```

### Adding Match Event (Referee)

```php
MatchEvent::create([
    'match_id' => $match->id,
    'player_id' => $player->id,
    'team_id' => $team->id,
    'event_type' => 'goal',
    'minute' => 23,
    'period' => 'first_half',
    'recorded_by_user_id' => Auth::id(),
]);
```

### FA Validation (FA Admin)

```php
$matchSheet->faValidate($admin, 'All documentation verified and approved');
```

## 🔧 Configuration

### Environment Variables

```env
MATCH_SHEET_AUTO_TRANSITION=true
MATCH_SHEET_NOTIFICATIONS=true
MATCH_SHEET_AUDIT_LOGGING=true
```

### Database Configuration

```php
// config/database.php
'match_sheet' => [
    'auto_transition' => env('MATCH_SHEET_AUTO_TRANSITION', true),
    'notifications' => env('MATCH_SHEET_NOTIFICATIONS', true),
    'audit_logging' => env('MATCH_SHEET_AUDIT_LOGGING', true),
],
```

This comprehensive stage-based match sheet system provides a robust, secure, and user-friendly solution for managing football match documentation from creation to final validation, ensuring proper workflow and audit trails throughout the entire process.
