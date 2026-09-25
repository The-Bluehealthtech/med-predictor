<?php

namespace App\Rules;

use App\Services\FifaConnect\SchemaCatalog;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

class FifaIdentifier implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if ($value === null || $value === '') {
            return;
        }

        try {
            app(SchemaCatalog::class)->assertFifaIdentifier(
                (string) $value
            );
        } catch (Throwable) {
            $fail(
                'The :attribute field must be a valid FIFA Connect Data 3.3 FIFAIdentifier.'
            );
        }
    }
}
