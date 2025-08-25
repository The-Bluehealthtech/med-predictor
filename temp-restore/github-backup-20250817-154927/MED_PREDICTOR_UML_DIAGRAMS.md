# 🏥 Med Predictor Platform - UML Diagrams

## Comprehensive UML Documentation for All Workflows

---

## 📋 **UML Diagram Overview**

This document contains comprehensive UML diagrams for the Med Predictor platform, covering:

-   **Class Diagrams** - System architecture and relationships
-   **Sequence Diagrams** - User interaction workflows
-   **Activity Diagrams** - Process flows and decision points
-   **State Diagrams** - Entity lifecycle management

---

## 🏗️ **Class Diagrams**

### **1. Core System Architecture**

```mermaid
classDiagram
    class User {
        +id: int
        +name: string
        +email: string
        +role: string
        +entity_type: string
        +club_id: int
        +association_id: int
        +fifa_connect_id: string
        +permissions: json
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +generateFifaConnectId()
        +hasPermission()
        +getEntity()
    }

    class Player {
        +id: int
        +name: string
        +first_name: string
        +last_name: string
        +date_of_birth: date
        +nationality: string
        +position: string
        +height: int
        +weight: int
        +fifa_connect_id: string
        +overall_rating: int
        +potential_rating: int
        +value_eur: int
        +wage_eur: int
        +club_id: int
        +association_id: int
        +player_picture: string
        +medical_clearance_status: string
        +created_at: timestamp
        +updated_at: timestamp
        +getAge()
        +getFullName()
        +isEligibleForCompetition()
        +getPerformanceStats()
    }

    class Club {
        +id: int
        +name: string
        +fifa_connect_id: string
        +fifa_id: string
        +association_id: int
        +logo_url: string
        +website: string
        +email: string
        +phone: string
        +address: string
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +getPlayers()
        +getTeams()
        +getLicenses()
    }

    class Association {
        +id: int
        +name: string
        +fifa_connect_id: string
        +logo_url: string
        +website: string
        +email: string
        +phone: string
        +address: string
        +country: string
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +getClubs()
        +getCompetitions()
        +getUsers()
    }

    class Competition {
        +id: int
        +name: string
        +type: string
        +association_id: int
        +start_date: date
        +end_date: date
        +registration_deadline: date
        +max_teams: int
        +fifa_compliance_required: boolean
        +rules: text
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +generateSchedule()
        +calculateStandings()
        +getTeams()
    }

    class Team {
        +id: int
        +name: string
        +club_id: int
        +competition_id: int
        +formation: string
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +getPlayers()
        +getMatches()
        +getStats()
    }

    class Match {
        +id: int
        +competition_id: int
        +home_team_id: int
        +away_team_id: int
        +match_date: datetime
        +venue: string
        +status: string
        +home_score: int
        +away_score: int
        +matchday: int
        +created_at: timestamp
        +updated_at: timestamp
        +getEvents()
        +getLineups()
        +getMatchSheet()
    }

    class MatchEvent {
        +id: int
        +match_id: int
        +event_type: string
        +minute: int
        +player_id: int
        +team_id: int
        +details: json
        +logged_by: int
        +created_at: timestamp
        +updated_at: timestamp
        +getPlayer()
        +getTeam()
    }

    class PlayerLicense {
        +id: int
        +player_id: int
        +club_id: int
        +competition_id: int
        +license_type: string
        +status: string
        +medical_clearance: boolean
        +submitted_by: int
        +approved_by: int
        +submitted_at: timestamp
        +approved_at: timestamp
        +expires_at: timestamp
        +created_at: timestamp
        +updated_at: timestamp
        +isValid()
        +isExpired()
    }

    class HealthRecord {
        +id: int
        +player_id: int
        +record_type: string
        +record_date: date
        +description: text
        +doctor_id: int
        +file_path: string
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +getPlayer()
        +getDoctor()
    }

    class MedicalPrediction {
        +id: int
        +player_id: int
        +prediction_type: string
        +risk_level: string
        +confidence_score: float
        +factors: json
        +recommendations: text
        +created_at: timestamp
        +updated_at: timestamp
        +getPlayer()
        +isHighRisk()
    }

    %% Relationships
    User ||--o{ Player : manages
    User ||--o{ Club : administers
    User ||--o{ Association : administers

    Association ||--o{ Club : contains
    Association ||--o{ Competition : organizes
    Association ||--o{ User : has_users

    Club ||--o{ Player : has_players
    Club ||--o{ Team : has_teams
    Club ||--o{ PlayerLicense : manages_licenses

    Competition ||--o{ Team : has_teams
    Competition ||--o{ Match : has_matches
    Competition ||--o{ PlayerLicense : requires_licenses

    Team ||--o{ Player : has_players
    Team ||--o{ Match : plays_in

    Match ||--o{ MatchEvent : has_events
    Match ||--o{ Team : involves_teams

    Player ||--o{ PlayerLicense : has_licenses
    Player ||--o{ HealthRecord : has_records
    Player ||--o{ MedicalPrediction : has_predictions
    Player ||--o{ MatchEvent : participates_in
```

### **2. FIFA Connect Integration Classes**

```mermaid
classDiagram
    class FifaConnectService {
        +baseUrl: string
        +apiKey: string
        +timeout: int
        +maxRetries: int
        +syncPlayer(Player)
        +syncClub(Club)
        +syncAssociation(Association)
        +handleWebhook(payload)
        +fetchFifaPlayerData(fifaId)
        +generateFifaConnectId(entity)
    }

    class FifaCacheService {
        +redis: Redis
        +defaultTtl: int
        +cacheFifaData(key, data, ttl)
        +getCachedFifaData(key)
        +invalidateFifaCache(pattern)
        +isDataFresh(key)
    }

    class FifaWebhookHandler {
        +handlePlayerTransfer(payload)
        +handlePlayerSuspension(payload)
        +handleMatchResult(payload)
        +validateWebhook(payload)
        +processWebhookEvent(eventType, data)
    }

    class FifaDataValidator {
        +validatePlayerData(data)
        +validateClubData(data)
        +validateAssociationData(data)
        +checkDataIntegrity(entity, fifaData)
        +resolveDataConflicts(localData, fifaData)
    }

    %% Relationships
    FifaConnectService --> FifaCacheService : uses
    FifaConnectService --> FifaDataValidator : uses
    FifaWebhookHandler --> FifaConnectService : triggers
    FifaDataValidator --> FifaConnectService : validates
```

---

## 🔄 **Sequence Diagrams**

### **1. Association Administrator - Club Registration Workflow**

```mermaid
sequenceDiagram
    participant AA as Association Admin
    participant S as System
    participant C as Club
    participant F as FIFA Connect
    participant N as Notification

    AA->>S: Login to Dashboard
    S->>AA: Display Pending Clubs

    AA->>S: Select Club for Review
    S->>C: Load Club Details
    C->>S: Return Club Information

    AA->>S: Review Club Application
    S->>F: Verify FIFA Connect ID
    F->>S: Return FIFA Data

    alt Club Approved
        AA->>S: Approve Club
        S->>C: Update Status to Approved
        S->>F: Generate FIFA Connect ID
        F->>S: Return FIFA ID
        S->>C: Store FIFA Connect ID
        S->>N: Send Approval Notification
        N->>C: Notify Club Admin
    else Club Rejected
        AA->>S: Reject Club
        S->>C: Update Status to Rejected
        S->>N: Send Rejection Notification
        N->>C: Notify Club Admin
    end

    S->>AA: Update Dashboard
    AA->>S: View Updated Status
```

### **2. Club Administrator - Player Registration Workflow**

```mermaid
sequenceDiagram
    participant CA as Club Admin
    participant S as System
    participant P as Player
    participant F as FIFA Connect
    participant L as License System
    participant H as Health System

    CA->>S: Access Player Registration
    S->>CA: Display Registration Form

    CA->>S: Submit Player Data
    S->>P: Create Player Record

    alt FIFA Connect ID Provided
        S->>F: Sync Player Data
        F->>S: Return FIFA Data
        S->>P: Update Player Attributes
    end

    CA->>S: Upload Player Photo
    S->>P: Store Photo

    CA->>S: Submit License Application
    S->>L: Create License Record
    L->>S: Return License Status

    CA->>S: Upload Health Records
    S->>H: Store Health Data
    H->>S: Update Medical Status

    S->>CA: Confirm Registration
    CA->>S: View Player Profile
```

### **3. Player Self-Service - Profile Management Workflow**

```mermaid
sequenceDiagram
    participant PL as Player
    participant S as System
    participant P as Player Profile
    participant H as Health Records
    participant F as FIFA Connect

    PL->>S: Login to Player Portal
    S->>PL: Display Player Dashboard

    PL->>S: Request Profile Update
    S->>P: Load Current Profile
    P->>S: Return Profile Data
    S->>PL: Display Edit Form

    PL->>S: Submit Updated Information
    S->>P: Update Profile Data

    alt Photo Upload
        PL->>S: Upload New Photo
        S->>P: Store New Photo
    end

    PL->>S: View Health Records
    S->>H: Load Health Data
    H->>S: Return Health Records
    S->>PL: Display Health Information

    PL->>S: Check FIFA Integration
    S->>F: Verify FIFA Data
    F->>S: Return FIFA Status
    S->>PL: Display FIFA Status

    S->>PL: Confirm Updates
    PL->>S: View Updated Profile
```

### **4. Competition Management Workflow**

```mermaid
sequenceDiagram
    participant AA as Association Admin
    participant S as System
    participant C as Competition
    participant T as Team
    participant M as Match
    participant F as FIFA Connect

    AA->>S: Create Competition
    S->>C: Create Competition Record
    C->>S: Return Competition ID

    AA->>S: Configure Competition Rules
    S->>C: Update Competition Settings

    loop Team Registration
        T->>S: Submit Registration
        S->>C: Add Team to Competition
        S->>F: Verify Team FIFA Compliance
        F->>S: Return Compliance Status
        S->>T: Confirm Registration
    end

    AA->>S: Generate Match Schedule
    S->>C: Create Match Schedule
    C->>M: Create Match Records

    loop Match Execution
        M->>S: Update Match Status
        S->>C: Update Competition Progress
        S->>T: Update Team Statistics
    end

    AA->>S: Finalize Competition
    S->>C: Calculate Final Standings
    C->>S: Generate Competition Report
    S->>AA: Display Results
```

---

## 📊 **Activity Diagrams**

### **1. Player Registration Process**

```mermaid
flowchart TD
    A[Start Registration] --> B{User Type?}

    B -->|Club Admin| C[Access Club Dashboard]
    B -->|Association Admin| D[Access Association Dashboard]

    C --> E[Select Player Registration]
    D --> F[Select Club Management]

    E --> G[Fill Player Information]
    F --> G

    G --> H{FIFA Connect ID?}

    H -->|Yes| I[Sync FIFA Data]
    H -->|No| J[Create Local Record]

    I --> K[Update Player Attributes]
    J --> L[Generate FIFA Connect ID]

    K --> M[Upload Player Photo]
    L --> M

    M --> N[Submit License Application]
    N --> O[Upload Health Records]

    O --> P{Medical Clearance?}

    P -->|Yes| Q[Approve Registration]
    P -->|No| R[Request Medical Clearance]

    Q --> S[Send Approval Notification]
    R --> T[Send Medical Request]

    S --> U[End Process]
    T --> V[Wait for Medical Clearance]
    V --> P
```

### **2. Competition Management Process**

```mermaid
flowchart TD
    A[Start Competition Creation] --> B[Define Competition Type]

    B --> C{Competition Type?}

    C -->|League| D[Set League Parameters]
    C -->|Cup| E[Set Cup Parameters]
    C -->|Tournament| F[Set Tournament Parameters]

    D --> G[Configure Rules]
    E --> G
    F --> G

    G --> H[Set Registration Deadline]
    H --> I[Set FIFA Compliance Requirements]

    I --> J[Notify Eligible Clubs]
    J --> K[Wait for Team Registrations]

    K --> L{Registration Deadline Reached?}

    L -->|No| K
    L -->|Yes| M[Review Team Applications]

    M --> N{All Teams Eligible?}

    N -->|No| O[Request Additional Documentation]
    N -->|Yes| P[Generate Match Schedule]

    O --> M
    P --> Q[Assign Match Officials]

    Q --> R[Execute Competition]
    R --> S[Monitor Match Results]

    S --> T{Competition Complete?}

    T -->|No| S
    T -->|Yes| U[Calculate Final Standings]

    U --> V[Generate Competition Report]
    V --> W[End Process]
```

### **3. License Management Process**

```mermaid
flowchart TD
    A[Start License Application] --> B[Club Admin Submits Application]

    B --> C[Upload Required Documents]
    C --> D[Verify Player Eligibility]

    D --> E{Player Eligible?}

    E -->|No| F[Reject Application]
    E -->|Yes| G[Check Medical Clearance]

    G --> H{Medical Clearance Valid?}

    H -->|No| I[Request Medical Assessment]
    H -->|Yes| J[Review FIFA Compliance]

    I --> K[Wait for Medical Results]
    K --> G

    J --> L{FIFA Compliant?}

    L -->|No| M[Request FIFA Documentation]
    L -->|Yes| N[Association Review]

    M --> O[Wait for FIFA Data]
    O --> J

    N --> P{Application Approved?}

    P -->|No| Q[Reject with Reasons]
    P -->|Yes| R[Issue License]

    R --> S[Set Expiry Date]
    S --> T[Send Approval Notification]

    Q --> U[Send Rejection Notification]
    T --> V[Monitor License Status]
    U --> W[End Process]

    V --> X{License Expiring Soon?}
    X -->|Yes| Y[Send Renewal Reminder]
    X -->|No| V
    Y --> Z[End Process]
```

---

## 🔄 **State Diagrams**

### **1. Player Lifecycle States**

```mermaid
stateDiagram-v2
    [*] --> PendingRegistration
    PendingRegistration --> Registered
    PendingRegistration --> Rejected

    Registered --> Active
    Registered --> Inactive

    Active --> Licensed
    Active --> Unlicensed

    Licensed --> Playing
    Licensed --> Suspended
    Licensed --> Injured

    Playing --> Licensed
    Playing --> Retired

    Suspended --> Licensed
    Injured --> Licensed
    Injured --> Retired

    Unlicensed --> Active
    Unlicensed --> Retired

    Inactive --> Active
    Inactive --> Retired

    Retired --> [*]
    Rejected --> [*]
```

### **2. Competition States**

```mermaid
stateDiagram-v2
    [*] --> Planning
    Planning --> RegistrationOpen
    RegistrationOpen --> RegistrationClosed
    RegistrationClosed --> TeamsReview

    TeamsReview --> Approved
    TeamsReview --> Rejected

    Rejected --> Planning
    Approved --> Scheduled

    Scheduled --> InProgress
    InProgress --> Completed
    InProgress --> Suspended

    Suspended --> InProgress
    Suspended --> Cancelled

    Completed --> [*]
    Cancelled --> [*]
```

### **3. License States**

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Submitted
    Submitted --> UnderReview

    UnderReview --> Approved
    UnderReview --> Rejected
    UnderReview --> PendingDocuments

    PendingDocuments --> UnderReview

    Approved --> Active
    Rejected --> [*]

    Active --> Expired
    Active --> Suspended
    Active --> Revoked

    Expired --> RenewalPending
    Suspended --> Active
    Revoked --> [*]

    RenewalPending --> Draft
    RenewalPending --> Expired
```

---

## 🏗️ **Component Diagrams**

### **1. System Architecture Components**

```mermaid
graph TB
    subgraph "Frontend Layer"
        UI[User Interface]
        VUE[Vue.js Components]
        TAIL[Tailwind CSS]
    end

    subgraph "Backend Layer"
        API[REST API]
        AUTH[Authentication]
        RBAC[Role-Based Access Control]
    end

    subgraph "Business Logic Layer"
        USR[User Management]
        PLR[Player Management]
        CMP[Competition Management]
        MAT[Match Management]
        HLT[Healthcare Management]
    end

    subgraph "Data Layer"
        DB[(SQLite Database)]
        CACHE[(Redis Cache)]
        FILE[File Storage]
    end

    subgraph "External Services"
        FIFA[FIFA Connect API]
        EMAIL[Email Service]
        SMS[SMS Service]
    end

    UI --> API
    VUE --> API
    API --> AUTH
    API --> RBAC

    AUTH --> USR
    RBAC --> USR
    USR --> PLR
    USR --> CMP
    USR --> MAT
    USR --> HLT

    PLR --> DB
    CMP --> DB
    MAT --> DB
    HLT --> DB

    USR --> CACHE
    PLR --> CACHE
    CMP --> CACHE

    PLR --> FILE
    HLT --> FILE

    PLR --> FIFA
    CMP --> FIFA

    USR --> EMAIL
    PLR --> EMAIL
    CMP --> EMAIL
```

### **2. FIFA Integration Components**

```mermaid
graph TB
    subgraph "Med Predictor System"
        API[API Gateway]
        SYNC[Data Synchronization]
        CACHE[Cache Manager]
        VAL[Data Validator]
    end

    subgraph "FIFA Connect Integration"
        FIFA_API[FIFA Connect API]
        WEBHOOK[Webhook Handler]
        AUTH[FIFA Authentication]
        RATE[Rate Limiter]
    end

    subgraph "Data Processing"
        TRANS[Data Transformer]
        MERGE[Data Merger]
        CONFLICT[Conflict Resolver]
    end

    subgraph "Storage"
        DB[(Local Database)]
        CACHE_STORE[(Redis Cache)]
        AUDIT[Audit Log]
    end

    API --> SYNC
    SYNC --> FIFA_API
    FIFA_API --> AUTH
    FIFA_API --> RATE

    SYNC --> CACHE
    SYNC --> VAL

    VAL --> TRANS
    TRANS --> MERGE
    MERGE --> CONFLICT

    CONFLICT --> DB
    CACHE --> CACHE_STORE
    SYNC --> AUDIT

    WEBHOOK --> SYNC
    FIFA_API --> WEBHOOK
```

---

## 📊 **Deployment Diagram**

```mermaid
graph TB
    subgraph "Client Layer"
        WEB[Web Browser]
        MOB[Mobile App]
        TAB[Tablet App]
    end

    subgraph "Load Balancer"
        LB[NGINX Load Balancer]
    end

    subgraph "Application Servers"
        APP1[Laravel App Server 1]
        APP2[Laravel App Server 2]
        APP3[Laravel App Server 3]
    end

    subgraph "Database Layer"
        DB1[(Primary SQLite)]
        DB2[(Replica SQLite)]
        REDIS[(Redis Cache)]
    end

    subgraph "File Storage"
        STORAGE[File Storage Server]
    end

    subgraph "External Services"
        FIFA[FIFA Connect API]
        EMAIL[Email Service]
        SMS[SMS Gateway]
    end

    WEB --> LB
    MOB --> LB
    TAB --> LB

    LB --> APP1
    LB --> APP2
    LB --> APP3

    APP1 --> DB1
    APP2 --> DB1
    APP3 --> DB1

    APP1 --> REDIS
    APP2 --> REDIS
    APP3 --> REDIS

    DB1 --> DB2

    APP1 --> STORAGE
    APP2 --> STORAGE
    APP3 --> STORAGE

    APP1 --> FIFA
    APP2 --> FIFA
    APP3 --> FIFA

    APP1 --> EMAIL
    APP2 --> EMAIL
    APP3 --> EMAIL

    APP1 --> SMS
    APP2 --> SMS
    APP3 --> SMS
```

---

## 🎯 **UML Diagram Summary**

### **📋 Diagram Types Created**

1. **Class Diagrams** - System architecture and relationships
2. **Sequence Diagrams** - User interaction workflows
3. **Activity Diagrams** - Process flows and decision points
4. **State Diagrams** - Entity lifecycle management
5. **Component Diagrams** - System architecture components
6. **Deployment Diagram** - Infrastructure and deployment

### **🔧 Key Workflows Documented**

-   **Association Administrator Workflows**
-   **Club Administrator Workflows**
-   **Player Self-Service Workflows**
-   **Competition Management Workflows**
-   **License Management Workflows**
-   **FIFA Connect Integration Workflows**

### **📊 Technical Architecture**

-   **Frontend**: Vue.js, Tailwind CSS, Alpine.js
-   **Backend**: Laravel 11.x, PHP 8.2+
-   **Database**: SQLite with Eloquent ORM
-   **Caching**: Redis for performance optimization
-   **External APIs**: FIFA Connect integration
-   **Security**: Role-based access control (RBAC)

### **🚀 Benefits of UML Documentation**

1. **Clear System Understanding** - Visual representation of complex workflows
2. **Development Guidance** - Detailed technical specifications
3. **Stakeholder Communication** - Easy-to-understand diagrams
4. **Maintenance Support** - Clear system architecture documentation
5. **Scalability Planning** - Component and deployment architecture

---

_Med Predictor - Comprehensive UML Documentation for Modern Football Management_
