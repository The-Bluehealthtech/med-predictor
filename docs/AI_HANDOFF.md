# AI HANDOFF — med-predictor

_Last updated: 2026-09-25_
_Project path: `/Users/izharmahjoub/med-predictor`_
_Branch at handoff: `deploy/render`_

## 1. Purpose of this file

This file is the canonical handoff for continuing the automated audit and remediation of the Laravel application `med-predictor` in a new ChatGPT conversation.

The next assistant must **read this file, inspect the actual Git state, and verify the code before making new changes**. This file is a snapshot, not a substitute for the repository.

The user explicitly does **not** want an iterative workflow where commands are copied manually into a terminal. Use the connected Remote Desktop Commander and work directly in the repository.

---

## 2. Non-negotiable requirements

### Application/data rules

- No fabricated, random, demo, mock or synthetic player/medical/FIT/FIFA facts may be displayed as real data.
- Missing data must remain `null`, unavailable or clearly unconfigured.
- Never invent business/player/medical values to make a page look complete.
- RBAC, ownership and tenant isolation must be enforced on all sensitive surfaces.
- Player portal data must be traceable to persisted/canonical sources.
- Portal UI may remain visible with missing values; missing data must not be replaced by fake values.
- Do not create fake production players or measurements merely for testing.
- Live FIFA/FIFA Connect/TMS API validation is deferred because the user does not yet have the required API keys.

### Git safety

Never use:

- `git add .`
- `git commit -a`
- `git restore .`

Before editing:

- run `git status --short`
- inspect the relevant diff
- do not discard pre-existing user changes

Do not commit until relevant tests are green. If a commit is later requested, stage exact paths only.

### Secrets

Do not print or expose:

- `.env`
- `DATABASE_URL`
- API keys/tokens
- passwords or secrets

When checking external integration configuration, report only whether a key/URL is configured, never its value.

---

## 3. Current Git snapshot

At handoff:

- Branch: `deploy/render`
- HEAD:
  - `80679b8 test: secure player portal access and authorization`
  - `e8e36e1 fix: display partial FIT axis scores`
  - `93125ec fix: generate FIT snapshot after metric verification`
  - `e1c722d fix: send FIT measurement date as UTC`
  - `7b47a2c feat: add FIT metric verification action`
  - `5b3102d feat: add canonical FIT metric recording form`
  - `ac78c20 feat: add canonical FIT metric management screen`
  - `99c096b feat: add canonical FIT metric recording workflow`

There is a **large uncommitted working tree** spanning routes, controllers, models, views, migrations and tests. Do not assume all of these changes were created in the latest session and do not revert them wholesale.

Always rerun:

```bash
git status --short
git diff --stat
```

before continuing.

---

## 4. Main product architecture

The application must implement a player portal whose data comes from canonical persisted sources fed by:

- players
- clubs
- associations/leagues/federations
- medical/performance modules
- business/functional modules
- external integrations only when genuinely configured

Expected flow:

```text
navigation/module
  -> authorized business operation
  -> canonical persisted data
  -> provenance/transformation
  -> player portal display
```

Audit matrix to maintain:

```text
UI field/card
-> route
-> middleware/permission
-> controller
-> service
-> model/table
-> write/source workflow
-> transformation
-> fallback semantics
-> tenant isolation
-> portal propagation
-> tests
```

---

## 5. /modules catalogue status

A previous automated audit found 32 cards, corresponding to 31 unique destinations because two licence cards share a route.

Last full smoke test before the later FIFA canonical-model edits:

- 31/31 unique destinations returned HTTP 200
- 0 PHP warnings
- no missing card route
- no card still pointed to a test route

Important corrections already made:

- `/modules` now requires authentication.
- FIT Metrics was added under **Analytics & Performance**.
- FIT card uses the canonical permission `record-performance-metrics`.
- Referee Portal now points to `referee-portal.index`, not a test route.
- previously public module destinations were aligned to authenticated access.
- Content Management duplicate route was fixed so the real controller supplies its data.

Rerun the 31-destination smoke test after major routing/view changes before claiming this is still true.

---

## 6. Player portal security

Current route:

- `GET /test-portail-joueur-simple`
- route name `test.portail.joueur.simple`

A prior security patch removed the IDOR/default-player behavior.

Current intended behavior:

- guest -> authentication required
- player -> can access only linked own player
- player requesting another player -> 403
- system admin -> may select player
- club roles -> scoped to same club
- association admin -> scoped to same association
- no default `player_id = 4`
- no arbitrary public `?player_id=` data exposure

Relevant committed tests:

- `tests/Feature/PlayerPortalSimpleAccessTest.php`
- `tests/Unit/Components/PlayerPortalSimpleSecurityContractTest.php`

Legacy `PlayerPortalController::showPlayer()` remains historically inconsistent and must not be reused blindly.

---

## 7. Canonical FIT status

The FIT implementation is a canonical, traceable pipeline.

Primary files include:

- `app/Services/Fit/FitScoreService.php`
- `app/Services/Fit/FitSnapshotService.php`
- `app/Http/Controllers/FitMetricManagementController.php`

Rules:

- axes: physical, technical, tactical, mental, social
- overall FIT only when all five axes are available
- missing axes stay null
- no substitution with fake/default values
- only verified `performance_metrics` are used
- historical calculation respects as-of time
- evidence/provenance are stored
- current complete snapshot drives global FIT score
- latest attempt may show partial axis values
- radar requires all five axes
- metric verification creates the FIT snapshot in the same transaction

Production was previously confirmed by the user as displaying the calculated score correctly.

---

## 8. Medical / PCMA hardening completed

Important changes already made:

- medical/Healthcare API groups require `auth:sanctum`
- duplicate PCMA API endpoints require `auth:sanctum`
- `/api/signed-pcmas` requires authenticated web access
- PCMA browser form uses authenticated canonical web routes
- medical records are attributed to the authenticated user rather than hardcoded `user_id = 1`
- target players are resolved through scoped models
- active PCMA AI paths no longer return fake “normal/cleared” results when AI is unavailable
- CT/ultrasound/fitness/AI V1/transcript prefill failures return explicit unavailability (typically 503), not simulated medical findings

Legacy mock helper methods may still physically exist in old controllers, but audited active PCMA paths must not call them.

Relevant tests:

- `tests/Unit/Components/MedicalApiSecurityContractTest.php`
- `tests/Unit/Components/PcmaAiFallbackContractTest.php`

Last targeted medical/PCMA contract run was green before the later FIFA model work.

---

## 9. Licence / transfer status

Canonical licence workflow:

- `PlayerLicenseWorkflowController::store()` writes `player_licenses`
- `PlayerPortalDataService` reads the same `player_licenses` table by `player_id`
- club and association scope is enforced
- active/pending duplicate licence logic exists

FIFA TMS:

- live integration is not configured
- auto-mock-in-local behavior was removed
- missing key should report `unconfigured`
- explicit mock mode must never report a live connection
- licence/history aggregation must not invent a FIFA licence merely because a player has a club/nationality

Relevant tests:

- `tests/Unit/Services/FifaTmsLicenseServiceContractTest.php`
- `tests/Unit/Components/LicensePortalFlowContractTest.php`
- `tests/Unit/Components/PlayerLicenseWorkflowContractTest.php`
- `tests/Unit/Components/TransferManagementContractTest.php`

---

## 10. Performance / analytics status

Canonical controllers were added/used for:

- Performance Analytics
- Analytics Dashboard
- Digital Twin
- DTN
- RPM

Relevant untracked/new files visible in Git status include:

- `app/Http/Controllers/PerformanceAnalyticsController.php`
- `app/Http/Controllers/AnalyticsDashboardController.php`
- `app/Http/Controllers/DigitalTwinController.php`
- `app/Http/Controllers/DtnController.php`
- `app/Http/Controllers/RpmController.php`

Last targeted performance/analytics contract run observed earlier in the audit:

- 15 tests
- 82 assertions
- green

Models such as `PlayerPerformance`, `Player`, `Club`, `Team`, `Competition` were checked for tenant scoping. `PerformanceAlert` does not use the same global scope, so controllers must scope it explicitly.

---

## 11. FIFA Portal / FIFA Connect status

### Live API

The user explicitly decided to postpone live FIFA API validation because API keys are not yet available.

Therefore:

- do not claim live FIFA connectivity
- missing key -> `unconfigured`
- no automatic local mock pretending to be connected
- explicit mock mode must be labelled simulated and disconnected from live FIFA

### FIFA Portal

The active FIFA Portal was moved away from the legacy simulated view.

The active portal should use:

- real local players
- persisted fields
- `N/A` for missing values
- real connectivity status

The old legacy view `fifa-portal-integrated.blade.php` historically contained `Math.random()`, hardcoded famous players and demo values. It should not be the active canonical surface.

Relevant tests:

- `tests/Unit/Components/FifaPortalContractTest.php`
- `tests/Unit/Services/FifaConnectConnectivityContractTest.php`
- `tests/Unit/Components/FifaConnectDashboardContractTest.php`

---

## 12. FIFA Connect Data Standard 3.3 is an essential requirement

The user explicitly stated that **FIFA Connect Data model compliance is an essential requirement of the application** and asked not to skim the documentation.

Normative references supplied by the user:

- Data Holders:
  - https://support.id.ma.services/support/solutions/articles/7000073835-data-holders-explained
- FIFA Connect Data:
  - https://data.fifaconnect.org/
  - https://data.fifaconnect.org/scenarios/
  - https://data.fifaconnect.org/xml-examples/
  - https://data.fifaconnect.org/registration/
  - https://data.fifaconnect.org/competition/
  - https://data.fifaconnect.org/discipline/
  - https://data.fifaconnect.org/content/documentation/generic.html

The public FIFA Connect Data **3.3 XSDs** were also downloaded and inspected directly.

Important interpretation rule:

- current XSD 3.3 constraints are the technical source of truth
- documentation explains semantics
- older XML examples are structural examples
- if an old example conflicts with current XSD lexical constraints, do not weaken the XSD to match the old example

Do not call the application “FIFA certified” merely because it validates technically. The XSD header indicates implementation is intended for permitted/authorized users; official certification/authorization is separate.

---

## 13. FIFA identity authority rule

A major non-compliance issue was found: the legacy application generated fake values such as:

- `FIFA2026TUN001`
- `COMP_...`
- `FIFA000123`
- role-prefixed FIFA IDs
- `FIFA_<uniqid>`
- `HEALTH_<uniqid>`

This is now explicitly forbidden.

Rule:

> FIFA IDs are authoritative external identifiers and must never be fabricated locally.

Local entities may exist with:

```text
fifa_connect_id = null
```

until a genuine authoritative FIFA identifier is received.

Several legacy generator methods were changed to fail closed with `LogicException`, and player/competition/account workflows were changed not to assign locally synthesized FIFA IDs.

Relevant guardrail test:

- `tests/Unit/Components/FifaIdentifierAuthorityContractTest.php`

Last known guardrail run before later serializer-model issues:

- 20 tests
- 94 assertions
- green

---

## 14. Data Holder semantics

Data Holder is modelled explicitly and must not be inferred from an old registration.

Required logic:

```text
existing FIFA_ID stored locally
    |
    +-- Connect ID mutation was sent
    |      -> covered_by_mutation
    |
    +-- no Connect ID mutation
           -> claim_required
           -> RegisterAsDataHolderOfPerson(...)
           -> registered
           -> verified
```

Do not “replay” an old inactive registration simply to become a Data Holder.

Bulk claim preparation should exclude:

- empty IDs
- malformed IDs
- secondary/merged IDs
- remote-deleted IDs
- duplicates

Key files:

- `app/Services/FifaConnect/DataHolderService.php`
- `app/Models/FifaConnect/DataHolder.php`

---

## 15. Canonical FIFA Connect schema

A separate canonical layer was created rather than overloading the application’s legacy business tables.

Primary migration:

- `database/migrations/2026_09_25_120800_create_fifa_connect_canonical_tables.php`

Additional canonical/round-trip migrations are currently untracked in the working tree:

- `2026_09_25_121000_extend_fifa_connect_canonical_round_trip_model.php`
- `2026_09_25_121100_allow_simple_fifa_match_team_context.php`
- `2026_09_25_121100_allow_team_only_identity_in_fifa_simple_matches.php`
- `2026_09_25_121200_add_depth_to_fifa_match_competition_contexts.php`
- `2026_09_25_121300_disambiguate_legacy_fifa_connect_record_foreign_keys.php`
- `2026_09_25_121400_rename_role_fifa_prefix_to_account_reference_prefix.php`

Canonical domains/tables include:

- Person
- local names
- national identifiers
- Organisation
- supported disciplines
- Address
- Picture
- MandatoryData / MandatoryPart
- Registration
- Certification
- Facility / Field
- Competition / elements / teams / roles/ranking
- Match / phases / events / teams / players / officials
- Discipline Case / Sanction
- Data Holder
- exchange/scenario messages

The main canonical migration was applied to the local DB during the audit.

---

## 16. XSD bundle and compliance tooling

Local public FIFA Connect Data 3.3 XSD bundle was installed under:

`storage/app/fifa-connect/xsd`

including:

- `generic.xsd`
- `registration.xsd`
- `competition.xsd`
- `discipline.xsd`
- `scenarios.xsd`
- ISO language/country/currency include XSDs

These files are local validation infrastructure and may be ignored/untracked. Do not assume they are committed.

Config was extended with:

- data standard version `3.3`
- XML namespace `http://fifa.com/fc`
- XSD path

Key services/commands visible in the working tree include:

- `app/Services/FifaConnect/SchemaCatalog.php`
- `app/Services/FifaConnect/XsdValidator.php`
- `app/Services/FifaConnect/PersonLocalXmlSerializer.php`
- `app/Services/FifaConnect/OrganisationLocalXmlSerializer.php`
- `app/Services/FifaConnect/FacilityLocalXmlSerializer.php`
- `app/Services/FifaConnect/CompetitionInternationalXmlSerializer.php`
- `app/Services/FifaConnect/MatchInternationalXmlSerializer.php`
- `app/Services/FifaConnect/DisciplineCaseXmlSerializer.php`
- `app/Services/FifaConnect/EventMessageXmlSerializer.php`
- `app/Services/FifaConnect/CanonicalXmlImporter.php`
- `app/Services/FifaConnect/CanonicalPersistenceService.php`
- `app/Console/Commands/CheckFifaDataStandardCompliance.php`
- `app/Console/Commands/PrepareFifaDataStandardValidationBundle.php`

Read these files before recreating anything; much of the later FIFA work already exists.

Command:

```bash
php artisan fifa:data-standard:check --strict
```

Previously returned strict readiness success after the XSD bundle was installed and canonical tables were migrated.

Important: this readiness command does **not** replace serializer/importer unit tests.

---

## 17. Last known FIFA serializer test state

A later run of the whole `tests/Unit/Services` directory exposed real FIFA-model integration errors plus unrelated historical SQLite migration failures.

Do **not** assume the whole services test suite is green.

The real FIFA issues identified at handoff are below.

### OPEN ISSUE A — Competition picture relation

Current `app/Models/FifaConnect/Competition.php` imports:

```php
use App\Models\FifaConnect\Concerns\HasFifaPicture;
```

but the current file does **not** actually use the trait or define the expected picture owner constant.

Meanwhile `CompetitionInternationalXmlSerializer` expects:

```text
picture
teams.picture
teams.persons.picture
```

This produced a real test error.

First correction should restore the canonical picture relation on `Competition` consistently with the other FIFA canonical models.

### OPEN ISSUE B — CompetitionTeam relation is inconsistent

Current `app/Models/FifaConnect/CompetitionTeam.php` contains:

```php
public function roles(): HasMany
{
    return $this->hasMany(CompetitionRole::class, 'competition_team_id');
}
```

but:

- `CompetitionRole.php` does not currently exist
- `CompetitionInternationalXmlSerializer` expects `$team->persons`
- `CompetitionTeamPerson.php` does exist

This is a concrete broken state caused during an interrupted refactor. Fix this before trusting Competition serializer tests.

Do not invent a new domain name casually: align the final class/relation with the XSD semantics and the existing persistence service.

### OPEN ISSUE C — MatchRecord is only partially restored

Current `app/Models/FifaConnect/MatchRecord.php` has:

- `competition()`
- `phases()`
- `teams()`
- `facility()`
- `events()`

but `MatchInternationalXmlSerializer` also expects:

- `competitionContext`
- `facilityContext`
- `officials`

Those relations are still missing at handoff.

Relevant canonical models already exist:

- `MatchCompetitionContext.php`
- `MatchFacilityContext.php`
- `MatchOfficial.php`
- `MatchPhase.php`
- `MatchEvent.php`
- `MatchTeam.php`
- `MatchPlayer.php`
- `TeamOfficial.php`

Restore the aggregate model relations rather than weakening serializer constraints.

### OPEN ISSUE D — unit tests and DB eager loading

Some serializers call `loadMissing()` on in-memory, non-persisted Eloquent models used by unit tests.

This can trigger SQLite/database access even when the test has already injected the relation with `setRelation()`.

Preferred fix:

- keep production eager loading
- avoid unnecessary DB queries for already-loaded relations / non-persisted test models
- do not remove XSD/cardinality checks to make tests pass

### OPEN ISSUE E — unrelated SQLite migration harness failures

The broad service test run also hit historical SQLite migration failures before reaching business logic, involving:

- `database/migrations/2026_09_23_092028_canonicalize_player_portal_relations.php`

This is a separate test-harness robustness issue. Do not misclassify those failures as FIFA serializer failures.

---

## 18. Known serializer coverage before the open issues

Earlier targeted tests were green for:

- `SchemaCatalog`
- FIFAIdentifier validation
- `PersonLocalXmlSerializer`
- `PlayerRegistration` serialization/validation
- `OrganisationLocalXmlSerializer`
- `FacilityLocalXmlSerializer`
- required-field refusal
- enum refusal
- Organisation exactly-one-address constraint
- Facility at-least-one-field constraint

Last known result for those three canonical scenario groups:

- 15 tests
- 47 assertions
- green

Related regression set at that point:

- 24 tests
- 80 assertions
- green

After later Competition/Match work, rerun these before asserting current green status.

---

## 19. Competition / Match / Discipline / EventMessage implementation

These implementations already exist and must be audited, not rewritten blindly.

### CompetitionInternationalXmlSerializer

Observed behavior includes:

- competition FIFA ID validation
- organisation FIFA ID validation
- status/system/team-character/discipline/age-category enums
- CompetitionNature
- System
- Simple Match children
- CompetitionElement hierarchy
- CompetitionTeam
- team ranking
- team persons/roles
- Home/Away match team checks

### MatchInternationalXmlSerializer

Observed behavior includes:

- Match FIFA ID
- MatchStatus
- date/time/matchday/attendance
- phases
- events
- competition context
- facility context
- match officials
- exactly two teams
- distinct Home/Away natures
- players and team officials
- FIFA ID and enum validation

### DisciplineCaseXmlSerializer

Observed behavior includes:

- case FIFA ID
- organisation FIFA ID
- offender person/organisation choice
- status
- competition/match context
- one-or-more sanctions
- person vs organisation sanction nature validation
- currency / measure / dates

### EventMessageXmlSerializer

Observed behavior includes:

- `Insert | Update | Delete`
- message nature
- event ID
- FIFA IDs
- competition element ID
- one or two score items
- XSD 3.3 quirk: `ExportDateTime` is handled according to the actual XSD type rather than its misleading name

### Import / persistence

Existing:

- `CanonicalXmlImporter.php`
- `CanonicalPersistenceService.php`

Persistence code already writes canonical Competition/Match structures, pictures and roles.

Audit round-trip invariants:

```text
XML
-> validate XSD
-> import canonical array/object
-> persist canonical tables
-> reload aggregate
-> serialize XML
-> validate XSD
```

No silent loss of cardinality, IDs, optional fields or ordering semantics is acceptable.

---

## 20. Immediate next steps for the new chat

Do these first, in this order.

### Step 1 — verify repository state

```bash
cd /Users/izharmahjoub/med-predictor
git branch --show-current
git status --short
git diff --stat
```

Read this file again from disk.

### Step 2 — repair the interrupted FIFA aggregate-model state

Inspect and fix:

1. `app/Models/FifaConnect/Competition.php`
   - restore canonical picture relation/trait expected by serializer

2. `app/Models/FifaConnect/CompetitionTeam.php`
   - resolve `roles()` vs serializer `persons`
   - reconcile with existing `CompetitionTeamPerson.php`
   - inspect `CanonicalPersistenceService` before choosing final relation/class

3. `app/Models/FifaConnect/MatchRecord.php`
   - restore `competitionContext()`
   - restore `facilityContext()`
   - restore `officials()`
   - retain existing competition/phases/teams/facility/events relations

Do not weaken the serializer just because the model relation is missing.

### Step 3 — lint immediately

```bash
php -l app/Models/FifaConnect/Competition.php
php -l app/Models/FifaConnect/CompetitionTeam.php
php -l app/Models/FifaConnect/MatchRecord.php
php -l app/Services/FifaConnect/CompetitionInternationalXmlSerializer.php
php -l app/Services/FifaConnect/MatchInternationalXmlSerializer.php
```

### Step 4 — run the targeted FIFA tests

Use the actual files present under `tests/Unit/Services`.

Known relevant test files include:

- `FifaConnectSchemaCatalogTest.php`
- `PersonLocalXmlSerializerTest.php`
- `OrganisationFacilityXmlSerializerTest.php`
- `CompetitionMatchXmlSerializerTest.php`
- `DisciplineEventMessageXmlSerializerTest.php`

Also inspect tests for canonical importer/persistence under `tests/integration/FifaConnect` and elsewhere before assuming names.

### Step 5 — separate FIFA failures from SQLite harness failures

If a test fails before executing its FIFA assertions due to SQLite migration incompatibility, report that separately.

Do not “fix” FIFA code to compensate for an unrelated migration harness problem.

### Step 6 — rerun compliance readiness

```bash
php artisan fifa:data-standard:check --strict
```

Then continue round-trip and cardinality work.

---

## 21. Longer-term FIFA completion checklist

Do not claim full technical compliance until all of these are demonstrated:

- XSD 3.3 bundle installed/validated
- canonical schema migrated
- PersonLocal export/import
- OrganisationLocal export/import
- FacilityLocal/Field export/import
- Registration variants
- CompetitionInternational export/import
- MatchInternational export/import
- EventMessage Insert/Update/Delete
- Case/Sanction export/import
- MandatoryData/MandatoryPart support
- Picture embedded/link support
- ISO language/country/currency validation
- FIFAIdentifier validation
- no locally fabricated FIFA IDs
- Data Holder workflow
- canonical DB persistence
- round-trip XML -> DB -> XML
- XSD validation after export
- no cardinality loss
- no enum widening
- no silent defaulting of mandatory FIFA fields
- mapping from legacy business tables into canonical FIFA tables is explicit and auditable
- legacy `fifa_connect_id` values are qualified/validated before promotion to canonical data
- production live integration is tested only when real authorized keys exist

Official FIFA authorization/certification remains separate from technical schema compliance.

---

## 22. Testing philosophy

Prefer:

- contract tests for invariants
- feature tests for authorization
- unit tests for serializers/normalizers
- XSD validation for exchange payloads
- HTTP smoke tests for card destinations
- deterministic fixtures
- no network dependency for ordinary tests
- `Http::fake()` for external-client behavior

Never make a test pass by generating plausible-but-fake medical/player/FIFA data.

---

## 23. Important current caveat

The working tree is **not ready to commit at this handoff** because the later FIFA Competition/Match aggregate refactor is in an interrupted state described in section 17.

The first task in the next conversation is to repair that state and rerun the relevant FIFA tests.

Do not create a commit merely because `php artisan fifa:data-standard:check --strict` passes; serializer/importer/model tests must also pass.

---

## 24. Suggested first message in a new chat

Use:

> Connecte-toi à mon Mac via Remote Desktop Commander et ouvre `/Users/izharmahjoub/med-predictor/docs/AI_HANDOFF.md`. Vérifie ensuite `git status --short` et l’état réel des fichiers avant toute modification. Reprends exactement aux « Immediate next steps » du handoff. Ne me demande pas d’exécuter des commandes manuellement et ne commit rien tant que les tests pertinents ne sont pas verts.
