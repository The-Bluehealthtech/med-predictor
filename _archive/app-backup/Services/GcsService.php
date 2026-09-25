<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class GcsService
{
    protected $storageClient;
    protected $bucket;
    protected $publicBucket;

    public function __construct()
    {
        $this->storageClient = app(StorageClient::class);
        $this->bucket = config('filesystems.disks.gcs.bucket');
        $this->publicBucket = config('filesystems.disks.gcs.bucket');
    }

    /**
     * Upload a file to GCS
     */
    public function uploadFile(UploadedFile $file, string $path = '', bool $public = true): array
    {
        $disk = $public ? 'gcs' : 'gcs_private';
        
        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path ? $path . '/' . $filename : $filename;
        
        // Store file
        $storedPath = Storage::disk($disk)->putFileAs($path, $file, $filename);
        
        // Get public URL if public
        $url = $public ? Storage::disk($disk)->url($storedPath) : null;
        
        return [
            'path' => $storedPath,
            'filename' => $filename,
            'url' => $url,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => $disk
        ];
    }

    /**
     * Upload multiple files
     */
    public function uploadFiles(array $files, string $path = '', bool $public = true): array
    {
        $results = [];
        
        foreach ($files as $file) {
            $results[] = $this->uploadFile($file, $path, $public);
        }
        
        return $results;
    }

    /**
     * Delete a file from GCS
     */
    public function deleteFile(string $path, string $disk = 'gcs'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get file URL
     */
    public function getFileUrl(string $path, string $disk = 'gcs'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Generate signed URL for private files
     */
    public function getSignedUrl(string $path, int $expiration = 3600): string
    {
        $bucket = $this->storageClient->bucket($this->bucket);
        $object = $bucket->object($path);
        
        return $object->signedUrl(new \DateTime('+' . $expiration . ' seconds'));
    }

    /**
     * List files in a directory
     */
    public function listFiles(string $path = '', string $disk = 'gcs'): array
    {
        return Storage::disk($disk)->files($path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $path, string $disk = 'gcs'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get file size
     */
    public function getFileSize(string $path, string $disk = 'gcs'): int
    {
        return Storage::disk($disk)->size($path);
    }

    /**
     * Copy file within GCS
     */
    public function copyFile(string $fromPath, string $toPath, string $disk = 'gcs'): bool
    {
        return Storage::disk($disk)->copy($fromPath, $toPath);
    }

    /**
     * Move file within GCS
     */
    public function moveFile(string $fromPath, string $toPath, string $disk = 'gcs'): bool
    {
        return Storage::disk($disk)->move($fromPath, $toPath);
    }

    /**
     * Create backup of files
     */
    public function createBackup(array $paths, string $backupPath = ''): array
    {
        $backupDisk = 'gcs_private';
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = $backupPath ?: "backups/{$timestamp}";
        
        $results = [];
        
        foreach ($paths as $path) {
            $filename = basename($path);
            $backupFilePath = "{$backupDir}/{$filename}";
            
            if ($this->fileExists($path)) {
                $copied = $this->copyFile($path, $backupFilePath, 'gcs');
                $results[] = [
                    'original' => $path,
                    'backup' => $backupFilePath,
                    'success' => $copied
                ];
            }
        }
        
        return $results;
    }

    /**
     * Clean up old backups
     */
    public function cleanupOldBackups(int $daysOld = 30): int
    {
        $backupDisk = 'gcs_private';
        $cutoffDate = now()->subDays($daysOld);
        $deletedCount = 0;
        
        $backupDirs = Storage::disk($backupDisk)->directories('backups');
        
        foreach ($backupDirs as $dir) {
            $dirName = basename($dir);
            
            // Parse date from directory name (format: Y-m-d_H-i-s)
            if (preg_match('/^(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})$/', $dirName, $matches)) {
                $dirDate = \Carbon\Carbon::createFromFormat('Y-m-d_H-i-s', $dirName);
                
                if ($dirDate->lt($cutoffDate)) {
                    Storage::disk($backupDisk)->deleteDirectory($dir);
                    $deletedCount++;
                }
            }
        }
        
        return $deletedCount;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        $publicFiles = Storage::disk('gcs')->allFiles();
        $privateFiles = Storage::disk('gcs_private')->allFiles();
        
        $publicSize = 0;
        $privateSize = 0;
        
        foreach ($publicFiles as $file) {
            $publicSize += Storage::disk('gcs')->size($file);
        }
        
        foreach ($privateFiles as $file) {
            $privateSize += Storage::disk('gcs_private')->size($file);
        }
        
        return [
            'public_files_count' => count($publicFiles),
            'private_files_count' => count($privateFiles),
            'public_size_bytes' => $publicSize,
            'private_size_bytes' => $privateSize,
            'total_size_bytes' => $publicSize + $privateSize,
            'public_size_mb' => round($publicSize / 1024 / 1024, 2),
            'private_size_mb' => round($privateSize / 1024 / 1024, 2),
            'total_size_mb' => round(($publicSize + $privateSize) / 1024 / 1024, 2)
        ];
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl(string $url, string $path = '', bool $public = true): array
    {
        $disk = $public ? 'gcs' : 'gcs_private';
        
        // Download file content
        $content = file_get_contents($url);
        
        if ($content === false) {
            throw new \Exception("Failed to download file from URL: {$url}");
        }
        
        // Generate filename
        $filename = basename(parse_url($url, PHP_URL_PATH));
        if (empty($filename)) {
            $filename = Str::uuid() . '.file';
        }
        
        $fullPath = $path ? $path . '/' . $filename : $filename;
        
        // Store file
        Storage::disk($disk)->put($fullPath, $content);
        
        // Get public URL if public
        $url = $public ? Storage::disk($disk)->url($fullPath) : null;
        
        return [
            'path' => $fullPath,
            'filename' => $filename,
            'url' => $url,
            'size' => strlen($content),
            'disk' => $disk
        ];
    }
}

