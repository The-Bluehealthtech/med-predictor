<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FifaXsdPreparationTest extends TestCase
{
    public function test_invalid_source_does_not_replace_existing_validation_bundle(): void
    {
        $base = storage_path('framework/testing/fifa-xsd-' . uniqid());
        $source = $base . '/source';
        $target = $base . '/validation';
        File::ensureDirectoryExists($source);
        File::ensureDirectoryExists($target);
        File::put($source . '/competition.xsd', 'wrong version');
        File::put($target . '/marker', 'preserved');
        config()->set('services.fifa_connect.xsd_path', $source);
        config()->set('services.fifa_connect.xsd_validation_path', $target);

        try {
            $this->assertSame(1, Artisan::call('fifa:data-standard:prepare-validation'));
            $this->assertSame('preserved', File::get($target . '/marker'));
        } finally {
            File::deleteDirectory($base);
        }
    }
}
