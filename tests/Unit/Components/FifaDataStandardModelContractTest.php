<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FifaDataStandardModelContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_canonical_migration_covers_core_xsd_domains(): void
    {
        $migration = file_get_contents(
            $this->projectPath(
                'database/migrations/2026_09_25_120800_create_fifa_connect_canonical_tables.php'
            )
        );

        foreach ([
            'fifa_connect_persons',
            'fifa_connect_registrations',
            'fifa_connect_organisations',
            'fifa_connect_facilities',
            'fifa_connect_fields',
            'fifa_connect_competitions',
            'fifa_connect_matches',
            'fifa_connect_match_events',
            'fifa_connect_discipline_cases',
            'fifa_connect_sanctions',
            'fifa_connect_data_holders',
            'fifa_connect_exchange_messages',
        ] as $table) {
            $this->assertStringContainsString(
                "Schema::create('{$table}'",
                $migration,
                $table
            );
        }
    }

    public function test_person_model_preserves_xsd_identity_fields(): void
    {
        $migration = file_get_contents(
            $this->projectPath(
                'database/migrations/2026_09_25_120800_create_fifa_connect_canonical_tables.php'
            )
        );

        foreach ([
            'person_fifa_id',
            'international_first_name',
            'international_last_name',
            'popular_name',
            'gender',
            'nationality',
            'second_nationality',
            'date_of_birth',
            'country_of_birth',
            'region_of_birth',
            'place_of_birth',
            'local_system_ma_id',
            'local_language',
            'local_country',
        ] as $column) {
            $this->assertStringContainsString(
                "'{$column}'",
                $migration,
                $column
            );
        }
    }

    public function test_registration_model_keeps_fifa_registration_separate_from_license(): void
    {
        $migration = file_get_contents(
            $this->projectPath(
                'database/migrations/2026_09_25_120800_create_fifa_connect_canonical_tables.php'
            )
        );

        foreach ([
            'registration_type',
            'registration_valid_from',
            'registration_valid_to',
            'level',
            'discipline',
            'registration_nature',
            'match_official_role',
            'team_official_role',
            'organisation_official_role',
        ] as $column) {
            $this->assertStringContainsString(
                "'{$column}'",
                $migration,
                $column
            );
        }

        $this->assertStringContainsString(
            "player_license_id",
            $migration
        );
    }

    public function test_data_holder_is_explicit_relation_not_inferred_registration(): void
    {
        $service = file_get_contents(
            $this->projectPath(
                'app/Services/FifaConnect/DataHolderService.php'
            )
        );

        $this->assertStringContainsString(
            "'claim_status' => \$connectMutationSent",
            $service
        );
        $this->assertStringContainsString(
            "'claim_required'",
            $service
        );
        $this->assertStringContainsString(
            "'covered_by_mutation'",
            $service
        );
        $this->assertStringNotContainsString(
            'PlayerLicense::create',
            $service
        );
    }

    public function test_xsd_runtime_configuration_is_versioned(): void
    {
        $config = file_get_contents(
            $this->projectPath('config/services.php')
        );

        $this->assertStringContainsString(
            "FIFA_CONNECT_DATA_STANDARD_VERSION",
            $config
        );
        $this->assertStringContainsString(
            "'3.3'",
            $config
        );
        $this->assertStringContainsString(
            "'http://fifa.com/fc'",
            $config
        );
    }
}
