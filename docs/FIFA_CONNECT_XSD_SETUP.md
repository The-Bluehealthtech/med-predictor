# FIFA Connect Data 3.3 XSD setup

The FIFA XSD files are intentionally excluded from Git. The source files on the current Mac include a FIFA notice restricting redistribution. Obtain the official bundle through an authorized channel for each environment; do not copy it into a public repository.

## Required local layout

Place the authorized, unmodified files under `storage/app/fifa-connect/xsd` (or set `FIFA_CONNECT_XSD_PATH` to their directory):

- `generic.xsd`, `registration.xsd`, `competition.xsd`, `discipline.xsd`, `scenarios.xsd`, `xmlmime.xsd`
- `includes/iso639-2-language-code.xsd`
- `includes/iso3166-country-code.xsd`
- `includes/iso3166-13-country-code.xsd`
- `includes/iso4217-currency-code.xsd`

The expected SHA-256 digests for the exact reviewed 3.3 bundle are tracked in `config/fifa_connect_xsd_hashes.php`. A different revision must be reviewed before those digests change.

## Prepare and verify

Run `php artisan fifa:data-standard:prepare-validation` after installing the authorized source bundle. The command checks all source digests **before** replacing the local validation copy, then makes two documented libxml compatibility patches. Run `php artisan fifa:data-standard:check --strict` after migrating the canonical database schema.

The source and validation directories stay ignored by Git. The command fails closed if the source is missing or differs from the reviewed bundle. Passing the readiness check establishes local technical prerequisites, not FIFA authorization or certification.
