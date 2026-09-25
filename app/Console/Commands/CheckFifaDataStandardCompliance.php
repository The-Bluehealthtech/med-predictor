<?php

namespace App\Console\Commands;

use App\Services\FifaConnect\SchemaCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CheckFifaDataStandardCompliance extends Command
{
    protected $signature = 'fifa:data-standard:check
        {--strict : Fail when any mandatory compliance check is unavailable}';

    protected $description =
        'Check FIFA Connect Data Standard 3.3 model and XSD readiness';

    private const REQUIRED_XSDS = [
        'generic.xsd',
        'registration.xsd',
        'competition.xsd',
        'discipline.xsd',
        'scenarios.xsd',
        'includes/iso639-2-language-code.xsd',
        'includes/iso3166-country-code.xsd',
        'includes/iso3166-13-country-code.xsd',
        'includes/iso4217-currency-code.xsd',
    ];

    private const REQUIRED_TABLES = [
        'fifa_connect_persons',
        'fifa_connect_person_local_names',
        'fifa_connect_person_national_identifiers',
        'fifa_connect_organisations',
        'fifa_connect_organisation_local_names',
        'fifa_connect_organisation_national_identifiers',
        'fifa_connect_supported_disciplines',
        'fifa_connect_addresses',
        'fifa_connect_pictures',
        'fifa_connect_mandatory_data',
        'fifa_connect_mandatory_parts',
        'fifa_connect_registrations',
        'fifa_connect_certifications',
        'fifa_connect_facilities',
        'fifa_connect_facility_local_names',
        'fifa_connect_fields',
        'fifa_connect_competitions',
        'fifa_connect_competition_local_names',
        'fifa_connect_competition_teams',
        'fifa_connect_competition_team_persons',
        'fifa_connect_matches',
        'fifa_connect_match_phases',
        'fifa_connect_match_events',
        'fifa_connect_match_teams',
        'fifa_connect_match_players',
        'fifa_connect_match_officials',
        'fifa_connect_team_officials',
        'fifa_connect_discipline_cases',
        'fifa_connect_sanctions',
        'fifa_connect_data_holders',
        'fifa_connect_exchange_messages',
        'fifa_connect_match_competition_contexts',
        'fifa_connect_match_facility_contexts',
        'fifa_connect_case_match_events',
        'fifa_connect_event_messages',
        'fifa_connect_event_message_scores',
    ];

    private const REQUIRED_COMPONENTS = [
        \App\Services\FifaConnect\SchemaCatalog::class,
        \App\Services\FifaConnect\XsdValidator::class,
        \App\Services\FifaConnect\PersonLocalXmlSerializer::class,
        \App\Services\FifaConnect\PersonDataXmlSerializer::class,
        \App\Services\FifaConnect\OrganisationLocalXmlSerializer::class,
        \App\Services\FifaConnect\FacilityLocalXmlSerializer::class,
        \App\Services\FifaConnect\CompetitionInternationalXmlSerializer::class,
        \App\Services\FifaConnect\MatchInternationalXmlSerializer::class,
        \App\Services\FifaConnect\DisciplineCaseXmlSerializer::class,
        \App\Services\FifaConnect\EventMessageXmlSerializer::class,
        \App\Services\FifaConnect\CanonicalXmlImporter::class,
        \App\Services\FifaConnect\CanonicalPersistenceService::class,
        \App\Services\FifaConnect\DataHolderService::class,
    ];

    public function handle(SchemaCatalog $catalog): int
    {
        $failures = 0;

        $this->info(
            'FIFA Connect Data Standard '
            . config('services.fifa_connect.data_standard_version', 'unknown')
        );

        $this->line(
            'Namespace: '
            . config('services.fifa_connect.xml_namespace', 'unknown')
        );

        $xsdBase = rtrim(
            (string) config('services.fifa_connect.xsd_path'),
            DIRECTORY_SEPARATOR
        );

        $this->newLine();
        $this->line('<comment>XSD bundle</comment>');

        foreach (self::REQUIRED_XSDS as $relative) {
            $path = $xsdBase
                . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $relative);

            if (is_file($path)) {
                $this->line("  [OK] {$relative}");
            } else {
                $this->error("  [MISSING] {$relative}");
                $failures++;
            }
        }

        $validationBase = rtrim(
            (string) config(
                'services.fifa_connect.xsd_validation_path'
            ),
            DIRECTORY_SEPARATOR
        );

        $this->newLine();
        $this->line(
            '<comment>Traceable XSD validation bundle</comment>'
        );

        $manifestPath = $validationBase
            . DIRECTORY_SEPARATOR
            . 'VALIDATION_PATCH.json';

        if (!is_file($manifestPath)) {
            $this->error('  [MISSING] VALIDATION_PATCH.json');
            $failures++;
        } else {
            $manifest = json_decode(
                (string) file_get_contents($manifestPath),
                true
            );

            if (!is_array($manifest)) {
                $this->error('  [FAIL] validation manifest is invalid JSON');
                $failures++;
            } else {
                $patches = $manifest['patches'] ?? [];
                $expectedPatchFiles = [
                    'generic.xsd',
                    'registration.xsd',
                ];

                $actualPatchFiles = array_values(array_map(
                    static fn (array $patch) =>
                        $patch['file'] ?? '',
                    is_array($patches) ? $patches : []
                ));

                sort($actualPatchFiles);
                sort($expectedPatchFiles);

                if ($actualPatchFiles === $expectedPatchFiles
                    && count($patches) === 2
                    && collect($patches)->every(
                        fn ($patch) =>
                            ($patch['semantic_change'] ?? true)
                                === false
                    )) {
                    $this->line(
                        '  [OK] exactly two documented '
                        . 'non-semantic compatibility patches'
                    );
                } else {
                    $this->error(
                        '  [FAIL] unexpected validation bundle patches'
                    );
                    $failures++;
                }

                foreach (self::REQUIRED_XSDS as $relative) {
                    $sourcePath = $xsdBase
                        . DIRECTORY_SEPARATOR
                        . str_replace(
                            '/',
                            DIRECTORY_SEPARATOR,
                            $relative
                        );
                    $validationPath = $validationBase
                        . DIRECTORY_SEPARATOR
                        . str_replace(
                            '/',
                            DIRECTORY_SEPARATOR,
                            $relative
                        );
                    $entry =
                        $manifest['files'][$relative] ?? null;

                    $sourceOk = is_file($sourcePath)
                        && is_array($entry)
                        && hash_file('sha256', $sourcePath)
                            === ($entry['source_sha256'] ?? null);

                    $validationOk = is_file($validationPath)
                        && is_array($entry)
                        && hash_file('sha256', $validationPath)
                            === (
                                $entry['validation_sha256']
                                ?? null
                            );

                    if ($sourceOk && $validationOk) {
                        $this->line(
                            "  [OK] {$relative} hashes verified"
                        );
                    } else {
                        $this->error(
                            "  [FAIL] {$relative} hash mismatch"
                        );
                        $failures++;
                    }
                }
            }
        }

        $this->newLine();
        $this->line('<comment>Critical FIFA simple types</comment>');

        foreach ([
            'FIFAIdentifier',
            'GenderType',
            'SimpleStatusType',
            'DisciplineType',
            'RegistrationLevelType',
            'PlayerRegistrationNatureType',
            'OrganisationNatureType',
            'GroundNatureType',
            'MatchStatusType',
            'MatchPhaseNatureType',
            'MatchEventNatureType',
            'MatchTeamNatureType',
            'MessageNatureType',
            'OffenderNatureType',
            'PersonSanctionNatureType',
            'OrganisationSanctionNatureType',
            'SanctionMeasureType',
            'ISO3166CountryCode',
            'ISO3166-13CountryCode',
            'ISO639-2Type',
            'ISO4217CurrencyCode',
        ] as $type) {
            try {
                $values = $catalog->enumValues($type);
                $pattern = $catalog->pattern($type);

                $details = $values
                    ? count($values) . ' enum values'
                    : ($pattern ? 'pattern loaded' : 'loaded');

                $this->line("  [OK] {$type}: {$details}");
            } catch (Throwable $e) {
                $this->error(
                    "  [FAIL] {$type}: {$e->getMessage()}"
                );
                $failures++;
            }
        }

        $this->newLine();
        $this->line('<comment>Canonical database model</comment>');

        foreach (self::REQUIRED_TABLES as $table) {
            try {
                if (Schema::hasTable($table)) {
                    $this->line("  [OK] {$table}");
                } else {
                    $this->warn("  [PENDING MIGRATION] {$table}");
                    $failures++;
                }
            } catch (Throwable $e) {
                $this->warn(
                    "  [DB UNAVAILABLE] {$table}: {$e->getMessage()}"
                );
                $failures++;
                break;
            }
        }

        $this->newLine();
        $this->line(
            '<comment>Canonical import/export components</comment>'
        );

        foreach (self::REQUIRED_COMPONENTS as $component) {
            if (class_exists($component)) {
                $this->line("  [OK] {$component}");
            } else {
                $this->error("  [MISSING] {$component}");
                $failures++;
            }
        }

        $this->newLine();

        if ($failures === 0) {
            $this->info(
                'Technical readiness checks passed. '
                . 'This does not constitute FIFA certification.'
            );

            return self::SUCCESS;
        }

        $this->warn(
            "{$failures} FIFA Data Standard readiness check(s) are not satisfied."
        );

        return $this->option('strict')
            ? self::FAILURE
            : self::SUCCESS;
    }
}
