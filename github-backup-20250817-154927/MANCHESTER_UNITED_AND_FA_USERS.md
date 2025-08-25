# Manchester United and The Football Association Users

This document lists all the users that have been created for Manchester United and The Football Association in the Med Predictor system.

## Manchester United Users

### Club Administrators

-   **Erik ten Hag** (Manager)
    -   Email: `erik.tenhag@manutd.com`
    -   Password: `password123`
    -   Role: `club_admin`
    -   FIFA Connect ID: `MUFC_ADMIN_001`
    -   Permissions: Full access to all modules

### Club Managers

-   **John Murtough** (Director of Football)

    -   Email: `john.murtough@manutd.com`
    -   Password: `password123`
    -   Role: `club_manager`
    -   FIFA Connect ID: `MUFC_MGR_001`
    -   Permissions: Player registration, competition management, healthcare

-   **Steve McClaren** (Assistant Manager)
    -   Email: `steve.mcclaren@manutd.com`
    -   Password: `password123`
    -   Role: `club_manager`
    -   FIFA Connect ID: `MUFC_MGR_002`
    -   Permissions: Player registration, competition management

### Club Medical Staff

-   **Dr. Gary O'Driscoll** (Head of Medical)

    -   Email: `gary.odriscoll@manutd.com`
    -   Password: `password123`
    -   Role: `club_medical`
    -   FIFA Connect ID: `MUFC_MED_001`
    -   Permissions: Healthcare access only

-   **Richard Hawkins** (Fitness Coach)
    -   Email: `richard.hawkins@manutd.com`
    -   Password: `password123`
    -   Role: `club_medical`
    -   FIFA Connect ID: `MUFC_MED_002`
    -   Permissions: Healthcare access only

## The Football Association Users

### Association Administrators

-   **Mark Bullingham** (Chief Executive)

    -   Email: `mark.bullingham@thefa.com`
    -   Password: `password123`
    -   Role: `association_admin`
    -   FIFA Connect ID: `FA_ADMIN_001`
    -   Permissions: Full access to all modules

-   **Debbie Hewitt** (Chair)
    -   Email: `debbie.hewitt@thefa.com`
    -   Password: `password123`
    -   Role: `association_admin`
    -   FIFA Connect ID: `FA_ADMIN_002`
    -   Permissions: Full access to all modules

### Association Registrars

-   **James Kendall** (Director of Football Development)

    -   Email: `james.kendall@thefa.com`
    -   Password: `password123`
    -   Role: `association_registrar`
    -   FIFA Connect ID: `FA_REG_001`
    -   Permissions: Player registration, competition management

-   **Kay Cossington** (Head of Women's Technical)
    -   Email: `kay.cossington@thefa.com`
    -   Password: `password123`
    -   Role: `association_registrar`
    -   FIFA Connect ID: `FA_REG_002`
    -   Permissions: Player registration, competition management

### Association Medical Staff

-   **Dr. Charlotte Cowie** (Head of Performance Medicine)
    -   Email: `charlotte.cowie@thefa.com`
    -   Password: `password123`
    -   Role: `association_medical`
    -   FIFA Connect ID: `FA_MED_001`
    -   Permissions: Healthcare access, player registration (medical data only)

## User Creation Commands

### Creating Club Users

```bash
php artisan user:create-club "User Name" "email@club.com" "Club Name" "role" --password="password"
```

Available roles for clubs:

-   `club_admin` - Full access to all modules
-   `club_manager` - Player registration, competition management, healthcare
-   `club_medical` - Healthcare access only

### Creating Association Users

```bash
php artisan user:create-association "User Name" "email@association.com" "Association Name" "role" --password="password"
```

Available roles for associations:

-   `association_admin` - Full access to all modules
-   `association_registrar` - Player registration, competition management
-   `association_medical` - Healthcare access, player registration (medical data only)

## Examples

### Create a new Manchester United coach

```bash
php artisan user:create-club "New Coach" "coach@manutd.com" "Manchester United" "club_manager"
```

### Create a new FA medical officer

```bash
php artisan user:create-association "Dr. Medical Officer" "medical@thefa.com" "The Football Association" "association_medical"
```

## Database Relationships

All users are linked to both their club and association:

-   **Club users**: `club_id` and `association_id` are both set
-   **Association users**: `club_id` is null, `association_id` is set

This allows for proper role-based access control and data filtering based on the user's organizational context.

## Security Notes

-   All users have strong passwords (minimum 6 characters)
-   Passwords are hashed using Laravel's Hash facade
-   Users are assigned appropriate permissions based on their role
-   FIFA Connect IDs are automatically generated for tracking
-   All users are set to 'active' status by default
