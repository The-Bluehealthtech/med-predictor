<?php

namespace Tests\Unit\RoleEvaluationImport;

use App\Services\RoleEvaluationImport\RowValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires purs (pas de base de données) du validateur du Livrable 2 :
 * minutes [0,130], taux [0,1], comptages entiers >= 0, booléens, codes de
 * poste, et la règle réussites <= tentatives.
 *
 * Non exécuté par moi (aucun PHP dans l'environnement de rédaction) — voir
 * docs/role-evaluation/03-implementation-livrable-2.md.
 */
class RowValidatorTest extends TestCase
{
    private function validator(): RowValidator
    {
        return new RowValidator(['GK', 'LCB', 'RCB', 'LB', 'RB', 'CDM', 'LCM', 'CAM', 'RCAM', 'LAM', 'RAM', 'CF']);
    }

    public function test_minutes_within_range_are_accepted(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_MINUTES, '45');
        $this->assertSame(45, $value);
        $this->assertNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_MINUTES, '0');
        $this->assertSame(0, $value);
        $this->assertNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_MINUTES, '130');
        $this->assertSame(130, $value);
        $this->assertNull($err);
    }

    public function test_minutes_out_of_range_are_rejected(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_MINUTES, '131');
        $this->assertNull($value);
        $this->assertNotNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_MINUTES, '-1');
        $this->assertNull($value);
        $this->assertNotNull($err);
    }

    public function test_rate_within_zero_and_one_is_accepted(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_RATE, '0.87');
        $this->assertSame(0.87, $value);
        $this->assertNull($err);
    }

    public function test_rate_above_one_is_rejected(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_RATE, '1.5');
        $this->assertNull($value);
        $this->assertNotNull($err);
    }

    public function test_count_must_be_a_non_negative_integer(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_COUNT, '12');
        $this->assertSame(12, $value);
        $this->assertNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_COUNT, '-3');
        $this->assertNull($value);
        $this->assertNotNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_COUNT, '3.5');
        $this->assertNull($value);
        $this->assertNotNull($err);
    }

    public function test_boolean_recognizes_french_and_english_tokens(): void
    {
        foreach (['oui', 'Oui', 'true', '1', 'titulaire'] as $token) {
            [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_BOOLEAN, $token);
            $this->assertTrue($value, "token '{$token}' devrait donner true");
            $this->assertNull($err);
        }

        foreach (['non', 'false', '0', 'remplaçant'] as $token) {
            [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_BOOLEAN, $token);
            $this->assertFalse($value, "token '{$token}' devrait donner false");
            $this->assertNull($err);
        }
    }

    public function test_position_code_must_exist_in_the_provided_catalog(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_POSITION_CODE, 'cdm');
        $this->assertSame('CDM', $value); // normalisé en majuscules
        $this->assertNull($err);

        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_POSITION_CODE, 'ZZZ');
        $this->assertNull($value);
        $this->assertNotNull($err);
    }

    public function test_legacy_position_code_uses_the_restricted_pre_existing_vocabulary(): void
    {
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_LEGACY_POSITION_CODE, 'dm');
        $this->assertSame('DM', $value);
        $this->assertNull($err);

        // Un code du NOUVEAU référentiel (12 codes) n'est pas valide dans
        // l'ANCIEN référentiel : les deux vocabulaires sont volontairement distincts.
        [$value, $err] = $this->validator()->convertAndValidate(RowValidator::KIND_LEGACY_POSITION_CODE, 'CDM');
        $this->assertNull($value);
        $this->assertNotNull($err);
    }

    public function test_success_greater_than_attempt_is_rejected(): void
    {
        $err = $this->validator()->checkAttemptSuccessPair('passes_tentees', 10, 'passes_reussies', 12);
        $this->assertNotNull($err);
        $this->assertStringContainsString('passes_reussies', $err);
    }

    public function test_success_less_or_equal_to_attempt_is_accepted(): void
    {
        $err = $this->validator()->checkAttemptSuccessPair('passes_tentees', 10, 'passes_reussies', 10);
        $this->assertNull($err);
    }

    public function test_pair_with_a_null_side_is_not_compared(): void
    {
        // Une donnée absente/NA ne doit jamais provoquer un faux rejet.
        $err = $this->validator()->checkAttemptSuccessPair('passes_tentees', null, 'passes_reussies', 5);
        $this->assertNull($err);
    }
}
