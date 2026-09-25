<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;

class GcsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StorageClient::class, function ($app) {
            $config = $app['config']['filesystems.disks.gcs'];
            
            $clientConfig = [
                'projectId' => $config['project_id'],
            ];
            
            // Add key file if provided
            if (!empty($config['key_file'])) {
                $clientConfig['keyFilePath'] = $config['key_file'];
            }
            
            // Add storage API URI if provided
            if (!empty($config['storage_api_uri'])) {
                $clientConfig['apiEndpoint'] = $config['storage_api_uri'];
            }
            
            return new StorageClient($clientConfig);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register GCS disk driver
        Storage::extend('gcs', function ($app, $config) {
            $client = $app[StorageClient::class];
            $bucket = $client->bucket($config['bucket']);
            
            $adapter = new \League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter(
                $bucket,
                $config['path_prefix'] ?? ''
            );
            
            return new \League\Flysystem\Filesystem($adapter);
        });
    }
}

