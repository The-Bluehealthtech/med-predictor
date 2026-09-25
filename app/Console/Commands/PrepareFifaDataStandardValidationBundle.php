<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class PrepareFifaDataStandardValidationBundle extends Command
{
    protected $signature = 'fifa:data-standard:prepare-validation';

    protected $description =
        'Prepare a libxml-compatible FIFA Connect Data 3.3 validation bundle';

    public function handle(): int
    {
        $source = rtrim(
            (string) config('services.fifa_connect.xsd_path'),
            DIRECTORY_SEPARATOR
        );

        $target = rtrim(
            (string) config(
                'services.fifa_connect.xsd_validation_path'
            ),
            DIRECTORY_SEPARATOR
        );

        if (!is_dir($source)) {
            $this->error(
                "Authoritative FIFA XSD source bundle is missing: {$source}"
            );

            return self::FAILURE;
        }

        File::deleteDirectory($target);
        File::copyDirectory($source, $target);

        $manifest = [
            'standard_version' => config(
                'services.fifa_connect.data_standard_version'
            ),
            'source_path' => $source,
            'generated_at' => now()->toISOString(),
            'patches' => [],
            'files' => [],
        ];

        foreach ($this->sourceFiles($source) as $relative) {
            $manifest['files'][$relative] = [
                'source_sha256' => hash_file(
                    'sha256',
                    $source . DIRECTORY_SEPARATOR . $relative
                ),
            ];
        }

        $generic = $target . DIRECTORY_SEPARATOR . 'generic.xsd';
        $genericSource = file_get_contents($generic);
        $genericPatched = str_replace(
            'schemaLocation="http://www.w3.org/2005/05/xmlmime"',
            'schemaLocation="xmlmime.xsd"',
            $genericSource,
            $count
        );

        if ($count !== 1) {
            throw new RuntimeException(
                'Expected xmlmime schemaLocation was not found exactly once.'
            );
        }

        file_put_contents($generic, $genericPatched);

        $manifest['patches'][] = [
            'file' => 'generic.xsd',
            'reason' =>
                'Resolve the official W3C xmlmime schema through the local '
                . 'official copy so validation can run with network access disabled.',
            'semantic_change' => false,
        ];

        $registration =
            $target . DIRECTORY_SEPARATOR . 'registration.xsd';

        $registrationSource = file_get_contents($registration);

        $from =
            'value="^[0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ]{6}'
            . '[0123456789ABCDEFGHIJKLMNPQRSTU]$"';

        $to =
            'value="[0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ]{6}'
            . '[0123456789ABCDEFGHIJKLMNPQRSTU]"';

        $registrationPatched = str_replace(
            $from,
            $to,
            $registrationSource,
            $count
        );

        if ($count !== 1) {
            throw new RuntimeException(
                'Expected FIFAIdentifier pattern was not found exactly once.'
            );
        }

        file_put_contents(
            $registration,
            $registrationPatched
        );

        $manifest['patches'][] = [
            'file' => 'registration.xsd',
            'type' => 'FIFAIdentifier',
            'reason' =>
                'XML Schema regexes are implicitly anchored and do not use '
                . 'PCRE ^/$ anchors. Removing those two characters preserves '
                . 'the intended seven-character FIFAIdentifier constraint.',
            'semantic_change' => false,
        ];

        foreach ($this->sourceFiles($target) as $relative) {
            if (!isset($manifest['files'][$relative])) {
                $manifest['files'][$relative] = [];
            }

            $manifest['files'][$relative]['validation_sha256'] =
                hash_file(
                    'sha256',
                    $target . DIRECTORY_SEPARATOR . $relative
                );
        }

        file_put_contents(
            $target . DIRECTORY_SEPARATOR . 'VALIDATION_PATCH.json',
            json_encode(
                $manifest,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        $this->info(
            'FIFA Connect Data validation bundle prepared.'
        );
        $this->line("Source: {$source}");
        $this->line("Validation: {$target}");
        $this->line('Compatibility patches: 2');

        return self::SUCCESS;
    }

    private function sourceFiles(string $base): array
    {
        $files = [];

        foreach (File::allFiles($base) as $file) {
            $relative = str_replace(
                DIRECTORY_SEPARATOR,
                '/',
                $file->getRelativePathname()
            );

            if ($relative === 'VALIDATION_PATCH.json') {
                continue;
            }

            $files[] = $relative;
        }

        sort($files);

        return $files;
    }
}
