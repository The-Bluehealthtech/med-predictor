# Med Predictor - Back Office User Management System

## Overview

The Back Office User Management System is a comprehensive FIFA Connect compliant solution for managing users within the Med Predictor platform. It provides role-based access control, FIFA Connect integration, and complete user lifecycle management.

## Features

### 🔐 Role-Based Access Control

-   **System Administrator**: Full access to all users and system functions
-   **Association Administrator**: Can manage users within their association and affiliated clubs
-   **Club Administrator**: Can view and manage users within their club
-   **Other Roles**: Limited access based on their specific permissions

### 🌐 FIFA Connect Compliance

-   Automatic FIFA Connect ID generation for all users
-   Role-based FIFA Connect ID prefixes
-   Compliance with FIFA data standards
-   Integration with FIFA Connect services

### 👥 User Management Features

-   **User Creation**: Create new users with role-based permissions
-   **User Editing**: Modify user information and permissions
-   **User Deactivation**: Suspend or deactivate users
-   **Bulk Operations**: Activate, deactivate, suspend, or delete multiple users
-   **User Export**: Export user data in CSV format
-   **Search & Filtering**: Advanced search and filtering capabilities

## User Roles & Permissions

### System Administrator

-   **FIFA Connect ID Prefix**: `FIFA_SYS`
-   **Permissions**: All system permissions
-   **Access**: All users, all modules, system administration
-   **Entity**: System-wide

### Club Roles

-   **Club Administrator**

    -   FIFA Connect ID Prefix: `FIFA_CLUB_ADMIN`
    -   Permissions: Player registration, competition management, healthcare
    -   Entity: Specific club

-   **Club Manager**

    -   FIFA Connect ID Prefix: `FIFA_CLUB_MGR`
    -   Permissions: Player registration, competition management, healthcare
    -   Entity: Specific club

-   **Club Medical Staff**
    -   FIFA Connect ID Prefix: `FIFA_CLUB_MED`
    -   Permissions: Healthcare only
    -   Entity: Specific club

### Association Roles

-   **Association Administrator**

    -   FIFA Connect ID Prefix: `FIFA_ASSOC_ADMIN`
    -   Permissions: All modules, user management
    -   Entity: Specific association

-   **Association Registrar**

    -   FIFA Connect ID Prefix: `FIFA_ASSOC_REG`
    -   Permissions: Player registration, competition management
    -   Entity: Specific association

-   **Association Medical Director**
    -   FIFA Connect ID Prefix: `FIFA_ASSOC_MED`
    -   Permissions: Healthcare only
    -   Entity: Specific association

## FIFA Connect Integration

### FIFA Connect ID Format

```
FIFA_[ROLE_PREFIX]_[TIMESTAMP]_[RANDOM_STRING]
```

Example: `FIFA_CLUB_ADMIN_20250711123456_ABC123`

### Compliance Features

-   **Unique Identification**: Each user has a unique FIFA Connect ID
-   **Role-Based Prefixes**: IDs reflect user roles and responsibilities
-   **Timestamp Integration**: Includes creation timestamp for tracking
-   **Automatic Generation**: IDs are generated automatically during user creation
-   **Regeneration**: IDs are regenerated when roles or entities change

## User Management Workflow

### 1. User Creation Process

1. **Access Control**: Only System Admins and Association Admins can create users
2. **Role Selection**: Choose appropriate role based on responsibilities
3. **Entity Assignment**: Assign to specific club or association
4. **Permission Assignment**: Automatic permission assignment based on role
5. **FIFA Connect ID**: Automatic generation of compliant FIFA Connect ID
6. **Status Setting**: Set initial user status (active, inactive, suspended)

### 2. User Editing Process

1. **Information Update**: Modify user details, role, or entity
2. **Permission Review**: Review and adjust permissions if needed
3. **FIFA Connect ID Update**: Regenerate ID if role or entity changes
4. **Status Management**: Change user status as needed

### 3. User Deactivation Process

1. **Status Change**: Change status to inactive or suspended
2. **Access Revocation**: User loses access to system
3. **Audit Trail**: Maintain record of status changes
4. **Reactivation**: Can be reactivated by administrators

## Technical Implementation

### Controllers

-   **UserManagementController**: Main controller for user management operations
-   **RoleMiddleware**: Middleware for role-based access control

### Models

-   **User Model**: Enhanced with role-based fields and FIFA Connect integration
-   **Club Model**: For club entity relationships
-   **Association Model**: For association entity relationships

### Views

-   **Dashboard**: Overview of user statistics and recent activity
-   **Index**: List all users with search and filtering
-   **Create**: Form for creating new users
-   **Edit**: Form for editing existing users
-   **Show**: Detailed user information view

### Routes

```php
// User Management Routes
Route::prefix('user-management')->name('user-management.')->middleware('role:system_admin,association_admin')->group(function () {
    Route::get('/', [UserManagementController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserManagementController::class);
    Route::post('bulk-action', [UserManagementController::class, 'bulkAction'])->name('bulk-action');
    Route::get('export', [UserManagementController::class, 'export'])->name('export');
});
```

## Security Features

### Access Control

-   **Role-Based Middleware**: Ensures only authorized users can access
-   **Entity-Based Filtering**: Users can only manage users within their scope
-   **Permission Validation**: All actions are validated against user permissions

### Data Protection

-   **Password Hashing**: All passwords are securely hashed
-   **Input Validation**: Comprehensive validation for all user inputs
-   **SQL Injection Prevention**: Parameterized queries and validation

### Audit Trail

-   **User Activity Logging**: All user management actions are logged
-   **Change Tracking**: Track changes to user information
-   **Login History**: Monitor user login activity

## User Interface Features

### Dashboard

-   **Statistics Overview**: Total users, active users, inactive users, suspended users
-   **Recent Users**: List of recently created users
-   **Role Distribution**: Chart showing users by role
-   **Status Distribution**: Chart showing users by status
-   **FIFA Connect Status**: Integration status and compliance indicators

### User List

-   **Advanced Search**: Search by name, email, or FIFA Connect ID
-   **Filtering**: Filter by role, status, or entity type
-   **Bulk Operations**: Select multiple users for bulk actions
-   **Export Functionality**: Export user data to CSV
-   **Responsive Design**: Works on desktop and mobile devices

### User Forms

-   **Dynamic Entity Selection**: Entity options change based on role
-   **Permission Preview**: Show permissions based on selected role
-   **FIFA Connect ID Display**: Show generated FIFA Connect ID
-   **Validation Feedback**: Real-time validation and error messages

## FIFA Connect Compliance Checklist

### ✅ Required Features

-   [x] Unique FIFA Connect ID for each user
-   [x] Role-based ID prefixes
-   [x] Timestamp integration
-   [x] Entity relationship tracking
-   [x] Permission-based access control
-   [x] Audit trail maintenance
-   [x] Data export capabilities
-   [x] Status management
-   [x] Secure authentication
-   [x] Input validation

### ✅ Data Standards

-   [x] FIFA Player ID format compliance
-   [x] FIFA Club ID integration
-   [x] FIFA Association ID integration
-   [x] Role-based data access
-   [x] Entity relationship validation
-   [x] Permission hierarchy
-   [x] Status tracking
-   [x] Activity logging

## Usage Instructions

### For System Administrators

1. **Access User Management**: Navigate to User Management in the main navigation
2. **Create Users**: Use the "Create User" button to add new users
3. **Manage Users**: Use the user list to view, edit, or manage users
4. **Bulk Operations**: Select multiple users for bulk actions
5. **Export Data**: Export user data for reporting or backup

### For Association Administrators

1. **Access User Management**: Navigate to User Management in the main navigation
2. **Create Club Users**: Create users for clubs within your association
3. **Manage Association Users**: Manage users within your association
4. **Monitor Activity**: Use the dashboard to monitor user activity
5. **Export Reports**: Export user data for compliance reporting

### For Club Administrators

1. **View Club Users**: Access user information for your club
2. **Request Changes**: Contact association administrators for user changes
3. **Monitor Activity**: View user activity within your club

## API Integration

### FIFA Connect API

The system is designed to integrate with FIFA Connect APIs:

-   **User Synchronization**: Sync user data with FIFA Connect
-   **ID Validation**: Validate FIFA Connect IDs
-   **Permission Sync**: Sync permissions with FIFA Connect
-   **Status Updates**: Update user status in FIFA Connect

### External Systems

-   **SSO Integration**: Single Sign-On with external systems
-   **LDAP Integration**: Lightweight Directory Access Protocol support
-   **API Authentication**: Secure API authentication for external access

## Monitoring and Analytics

### User Analytics

-   **User Growth**: Track user creation over time
-   **Role Distribution**: Monitor role distribution across the system
-   **Activity Metrics**: Track user login and activity patterns
-   **Status Changes**: Monitor user status changes

### Compliance Reporting

-   **FIFA Connect Compliance**: Generate compliance reports
-   **Permission Audits**: Audit user permissions
-   **Access Logs**: Review access logs for security
-   **Export Reports**: Generate reports for external stakeholders

## Troubleshooting

### Common Issues

1. **Permission Denied**: Check user role and permissions
2. **Entity Not Found**: Verify club or association exists
3. **FIFA Connect ID Error**: Check FIFA Connect service status
4. **Bulk Operation Failed**: Verify selected users and action

### Support

-   **Documentation**: Refer to this documentation for guidance
-   **Logs**: Check application logs for detailed error information
-   **Admin Support**: Contact system administrators for assistance

## Future Enhancements

### Planned Features

-   **Advanced Analytics**: Enhanced user analytics and reporting
-   **Workflow Automation**: Automated user onboarding workflows
-   **Integration APIs**: Additional API integrations
-   **Mobile App**: Mobile application for user management
-   **Advanced Security**: Enhanced security features

### FIFA Connect Enhancements

-   **Real-time Sync**: Real-time synchronization with FIFA Connect
-   **Advanced Compliance**: Enhanced FIFA compliance features
-   **API Extensions**: Extended FIFA Connect API integration
-   **Data Validation**: Enhanced FIFA data validation

---

## Quick Start Guide

1. **Login**: Access the system with admin credentials
2. **Navigate**: Go to User Management in the navigation
3. **Create User**: Click "Create User" to add a new user
4. **Fill Form**: Complete the user creation form
5. **Review**: Review the generated FIFA Connect ID
6. **Save**: Save the user and verify creation

The Back Office User Management System provides a comprehensive, FIFA Connect compliant solution for managing users within the Med Predictor platform, ensuring security, compliance, and efficient user administration.
