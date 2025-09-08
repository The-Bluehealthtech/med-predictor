# Google Cloud Storage (GCS) Integration Guide

## 🚀 Overview

This guide covers the complete integration of Google Cloud Storage (GCS) with the Med-Predictor application, including Kubernetes deployment and file management.

## 📋 Prerequisites

- Google Cloud Platform account
- Google Cloud CLI (`gcloud`) installed
- Kubernetes CLI (`kubectl`) installed
- Docker installed
- PHP 8.1+ and Composer

## 🛠️ Installation Steps

### 1. Setup Google Cloud Storage

```bash
# Run the GCS setup script
./scripts/setup-gcs.sh
```

This script will:
- Create GCP project (if needed)
- Enable required APIs
- Create GCS buckets
- Create service account
- Generate service account key
- Configure CORS and lifecycle policies

### 2. Configure Environment Variables

Copy the configuration from `gcs-config-example.env` to your `.env` file:

```bash
cp gcs-config-example.env .env
# Edit .env with your actual values
```

Required environment variables:
```env
GOOGLE_CLOUD_PROJECT_ID=med-predictor-project
GOOGLE_CLOUD_REGION=us-central1
GOOGLE_CLOUD_STORAGE_BUCKET=med-predictor-storage
GOOGLE_CLOUD_KEY_FILE=/path/to/service-account-key.json
FILESYSTEM_DISK=gcs
DEFAULT_STORAGE_DISK=gcs
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Test Integration

```bash
# Run the integration test
./scripts/test-gcs-integration.sh
```

## 🏗️ Architecture

### Components

1. **GcsServiceProvider** - Laravel service provider for GCS integration
2. **GcsService** - Service class for file operations
3. **GcsController** - API controller for file management
4. **Kubernetes Manifests** - K8s configuration for GCS storage

### File Structure

```
app/
├── Providers/
│   └── GcsServiceProvider.php
├── Services/
│   └── GcsService.php
└── Http/Controllers/
    └── GcsController.php

deploy/k8s/gcp/
└── gcs-storage.yaml

scripts/
├── setup-gcs.sh
├── test-gcs-integration.sh
└── deploy-gcs-k8s.sh
```

## 🔧 Configuration

### Laravel Filesystem Configuration

The GCS integration adds two new disk configurations:

```php
// config/filesystems.php
'gcs' => [
    'driver' => 'gcs',
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
    'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
    'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
    'visibility' => 'public',
],

'gcs_private' => [
    'driver' => 'gcs',
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
    'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
    'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
    'visibility' => 'private',
],
```

## 📡 API Endpoints

### File Upload

```bash
# Upload single file
POST /api/gcs/upload
Content-Type: multipart/form-data

{
  "file": <file>,
  "path": "uploads/",
  "public": true
}

# Upload multiple files
POST /api/gcs/upload-multiple
Content-Type: multipart/form-data

{
  "files": [<file1>, <file2>],
  "path": "uploads/",
  "public": true
}

# Upload from URL
POST /api/gcs/upload-from-url
Content-Type: application/json

{
  "url": "https://example.com/image.jpg",
  "path": "uploads/",
  "public": true
}
```

### File Management

```bash
# Get file URL
GET /api/gcs/file-url?path=uploads/file.jpg&disk=gcs&signed=false

# Get signed URL (for private files)
GET /api/gcs/file-url?path=private/file.jpg&disk=gcs_private&signed=true&expiration=3600

# List files
GET /api/gcs/files?path=uploads/&disk=gcs

# Delete file
DELETE /api/gcs/file?path=uploads/file.jpg&disk=gcs
```

### Storage Management

```bash
# Get storage statistics
GET /api/gcs/stats

# Create backup
POST /api/gcs/backup
Content-Type: application/json

{
  "paths": ["uploads/file1.jpg", "uploads/file2.jpg"],
  "backup_path": "backups/2024-01-01"
}

# Cleanup old backups
DELETE /api/gcs/cleanup-backups?days_old=30
```

## 🐳 Kubernetes Deployment

### Deploy Complete Infrastructure

```bash
# Deploy GCS + Kubernetes infrastructure
./scripts/deploy-gcs-k8s.sh
```

This script will:
- Create GKE cluster
- Create GCS buckets
- Deploy Kubernetes manifests
- Configure service accounts
- Set up storage classes and PVCs
- Deploy the application

### Manual Deployment

```bash
# Create namespace
kubectl create namespace med-predictor

# Deploy GCS storage configuration
kubectl apply -f deploy/k8s/gcp/gcs-storage.yaml

# Deploy application
kubectl apply -f deploy/k8s/gcp/fit-production-deployment.yaml
kubectl apply -f deploy/k8s/gcp/fit-production-database.yaml
kubectl apply -f deploy/k8s/gcp/fit-production-redis.yaml
kubectl apply -f deploy/k8s/gcp/fit-production-ingress.yaml
```

## 🔒 Security

### Service Account Permissions

The service account has the following roles:
- `roles/storage.objectAdmin` - Full access to storage objects
- `roles/storage.bucketAdmin` - Full access to buckets
- `roles/container.developer` - Kubernetes development access

### File Access Control

- **Public files**: Accessible via public URLs
- **Private files**: Require signed URLs for access
- **Backup files**: Stored in separate private bucket

## 📊 Monitoring

### Storage Statistics

The GCS service provides detailed storage statistics:

```php
$stats = app(GcsService::class)->getStorageStats();

// Returns:
[
    'public_files_count' => 150,
    'private_files_count' => 25,
    'public_size_bytes' => 104857600,
    'private_size_bytes' => 52428800,
    'total_size_bytes' => 157286400,
    'public_size_mb' => 100.0,
    'private_size_mb' => 50.0,
    'total_size_mb' => 150.0
]
```

### Automated Cleanup

A Kubernetes CronJob runs daily to clean up old backups:

```yaml
apiVersion: batch/v1
kind: CronJob
metadata:
  name: gcs-backup-cleanup
spec:
  schedule: "0 2 * * *"  # Daily at 2 AM
```

## 🚨 Troubleshooting

### Common Issues

1. **Authentication Error**
   ```bash
   # Check service account key
   gcloud auth activate-service-account --key-file=service-account-key.json
   ```

2. **Bucket Not Found**
   ```bash
   # List buckets
   gsutil ls
   ```

3. **Permission Denied**
   ```bash
   # Check IAM permissions
   gcloud projects get-iam-policy med-predictor-project
   ```

4. **Kubernetes Pod Issues**
   ```bash
   # Check pod logs
   kubectl logs -f deployment/med-predictor-deployment -n med-predictor
   ```

### Debug Commands

```bash
# Test GCS connection
gsutil ls gs://med-predictor-storage

# Check Kubernetes resources
kubectl get all -n med-predictor

# Test Laravel GCS integration
php artisan tinker
>>> Storage::disk('gcs')->put('test.txt', 'Hello GCS!');
>>> Storage::disk('gcs')->get('test.txt');
```

## 📈 Performance Optimization

### Best Practices

1. **Use appropriate storage classes**:
   - `STANDARD` for frequently accessed files
   - `NEARLINE` for files accessed less than once per month
   - `COLDLINE` for archival data

2. **Implement caching**:
   - Use CDN for public files
   - Cache file metadata in Redis

3. **Optimize uploads**:
   - Use multipart uploads for large files
   - Implement resumable uploads

4. **Monitor costs**:
   - Set up billing alerts
   - Use lifecycle policies for automatic cleanup

## 🔄 Backup Strategy

### Automated Backups

1. **Daily backups** of critical files
2. **Weekly full backups** of all data
3. **Monthly archival** to Coldline storage
4. **Automatic cleanup** of old backups

### Manual Backup

```bash
# Create backup using API
curl -X POST http://your-app.com/api/gcs/backup \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "paths": ["uploads/critical-file.jpg"],
    "backup_path": "backups/manual-2024-01-01"
  }'
```

## 📚 Additional Resources

- [Google Cloud Storage Documentation](https://cloud.google.com/storage/docs)
- [Laravel Filesystem Documentation](https://laravel.com/docs/filesystem)
- [Kubernetes Storage Documentation](https://kubernetes.io/docs/concepts/storage/)
- [GCS PHP Client Library](https://github.com/googleapis/google-cloud-php-storage)

## 🆘 Support

For issues or questions:
1. Check the troubleshooting section
2. Review the test reports generated by the scripts
3. Check Kubernetes pod logs
4. Verify GCS bucket permissions
5. Test with the integration test script

---

**Note**: Always keep your service account keys secure and never commit them to version control!

