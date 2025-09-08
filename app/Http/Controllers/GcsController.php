<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GcsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class GcsController extends Controller
{
    protected $gcsService;

    public function __construct(GcsService $gcsService)
    {
        $this->gcsService = $gcsService;
    }

    /**
     * Upload a single file
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'path' => 'nullable|string|max:255',
            'public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $path = $request->input('path', 'uploads');
            $public = $request->input('public', true);

            $result = $this->gcsService->uploadFile($file, $path, $public);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadFiles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'file|max:10240', // 10MB max per file
            'path' => 'nullable|string|max:255',
            'public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $files = $request->file('files');
            $path = $request->input('path', 'uploads');
            $public = $request->input('public', true);

            $results = $this->gcsService->uploadFiles($files, $path, $public);

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload from URL
     */
    public function uploadFromUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'path' => 'nullable|string|max:255',
            'public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            $path = $request->input('path', 'uploads');
            $public = $request->input('public', true);

            $result = $this->gcsService->uploadFromUrl($url, $path, $public);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded from URL successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload from URL failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file URL
     */
    public function getFileUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'disk' => 'nullable|string|in:gcs,gcs_private',
            'signed' => 'boolean',
            'expiration' => 'nullable|integer|min:60|max:604800' // 1 minute to 7 days
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $path = $request->input('path');
            $disk = $request->input('disk', 'gcs');
            $signed = $request->input('signed', false);
            $expiration = $request->input('expiration', 3600);

            if ($signed) {
                $url = $this->gcsService->getSignedUrl($path, $expiration);
            } else {
                $url = $this->gcsService->getFileUrl($path, $disk);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'disk' => $disk,
                    'signed' => $signed
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get file URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'disk' => 'nullable|string|in:gcs,gcs_private'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $path = $request->input('path');
            $disk = $request->input('disk', 'gcs');

            $deleted = $this->gcsService->deleteFile($path, $disk);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'File deleted successfully' : 'File not found or deletion failed',
                'data' => [
                    'path' => $path,
                    'disk' => $disk,
                    'deleted' => $deleted
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List files
     */
    public function listFiles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'nullable|string',
            'disk' => 'nullable|string|in:gcs,gcs_private'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $path = $request->input('path', '');
            $disk = $request->input('disk', 'gcs');

            $files = $this->gcsService->listFiles($path, $disk);

            return response()->json([
                'success' => true,
                'data' => [
                    'files' => $files,
                    'path' => $path,
                    'disk' => $disk,
                    'count' => count($files)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list files',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): JsonResponse
    {
        try {
            $stats = $this->gcsService->getStorageStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get storage statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create backup
     */
    public function createBackup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'paths' => 'required|array|min:1',
            'paths.*' => 'string',
            'backup_path' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paths = $request->input('paths');
            $backupPath = $request->input('backup_path', '');

            $results = $this->gcsService->createBackup($paths, $backupPath);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup old backups
     */
    public function cleanupOldBackups(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days_old' => 'nullable|integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $daysOld = $request->input('days_old', 30);
            $deletedCount = $this->gcsService->cleanupOldBackups($daysOld);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} old backup directories",
                'data' => [
                    'deleted_count' => $deletedCount,
                    'days_old' => $daysOld
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

