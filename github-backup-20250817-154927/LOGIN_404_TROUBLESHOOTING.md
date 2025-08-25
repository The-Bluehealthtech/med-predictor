# Login 404 Error Troubleshooting Guide

## Issue

Getting a 404 error when trying to access the login page.

## Quick Fixes

### 1. Clear All Caches

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 2. Check Server Status

```bash
# Check if server is running
lsof -i :8000

# Restart server if needed
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Verify Correct URLs

-   **Login Page**: `http://localhost:8000/login`
-   **Player Dashboard**: `http://localhost:8000/player-dashboard`
-   **Stakeholder Gallery**: `http://localhost:8000/stakeholder-gallery`

## Detailed Troubleshooting

### Step 1: Check Route Registration

```bash
php artisan route:list --name=login
```

Expected output:

```
GET|HEAD       login ........................................... login › Auth\AuthenticatedSessionController@create
```

### Step 2: Test Route Directly

```bash
curl -I http://localhost:8000/login
```

Expected output: `HTTP/1.1 200 OK`

### Step 3: Check File Permissions

```bash
# Ensure storage and bootstrap/cache are writable
chmod -R 775 storage bootstrap/cache
```

### Step 4: Check .htaccess (if using Apache)

Make sure your `.htaccess` file exists in the `public` directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Step 5: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### Step 6: Browser Cache Issues

-   **Clear browser cache** (Ctrl+Shift+R or Cmd+Shift+R)
-   **Try incognito/private mode**
-   **Try different browser**

## Common Issues and Solutions

### Issue 1: Server Not Running

**Symptoms**: Connection refused, timeout
**Solution**:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Issue 2: Route Caching

**Symptoms**: Routes not found despite being defined
**Solution**:

```bash
php artisan route:clear
php artisan config:clear
```

### Issue 3: Wrong URL

**Symptoms**: 404 on incorrect URL
**Solution**: Use correct URLs:

-   Login: `/login`
-   Dashboard: `/dashboard`
-   Player Dashboard: `/player-dashboard`

### Issue 4: File Permissions

**Symptoms**: 500 errors, permission denied
**Solution**:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Linux
```

### Issue 5: Missing Dependencies

**Symptoms**: Class not found errors
**Solution**:

```bash
composer install
composer dump-autoload
```

## Test URLs

### Working URLs (should return 200):

-   `http://localhost:8000/` - Welcome page
-   `http://localhost:8000/login` - Login page
-   `http://localhost:8000/register` - Registration page

### Test Login Flow:

1. Visit: `http://localhost:8000/login`
2. Use test credentials:
    - Email: `john.doe@testfc.com`
    - Password: `password123`
3. Should redirect to dashboard

## Alternative Ports

If port 8000 is busy, try:

```bash
php artisan serve --host=0.0.0.0 --port=8001
```

Then access: `http://localhost:8001/login`

## Debug Mode

Enable debug mode in `.env`:

```env
APP_DEBUG=true
```

This will show detailed error messages instead of generic 404s.

## Contact Information

If issues persist:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check server logs
3. Verify all files are in correct locations
4. Ensure all dependencies are installed

## Quick Commands Summary

```bash
# Clear all caches
php artisan route:clear && php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Restart server
php artisan serve --host=0.0.0.0 --port=8000

# Check routes
php artisan route:list --name=login

# Test login page
curl -I http://localhost:8000/login

# Fix permissions
chmod -R 775 storage bootstrap/cache
```
