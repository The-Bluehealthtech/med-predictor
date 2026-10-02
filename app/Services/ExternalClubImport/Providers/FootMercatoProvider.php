<?php

namespace App\Services\ExternalClubImport\Providers;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FootMercatoProvider
{
    public const SOURCE = 'footmercato';

    public function fetchClub(string $url): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 FIT-ExternalClubImporter/1.0',
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->timeout(20)->retry(2, 250)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Foot Mercato HTTP '.$response->status().' pour '.$url);
        }

        $data = $this->parseClubHtml($response->body(), $url);
        $data['players'] = $this->enrichPlayerDetails($data['players']);
        $data['club']['coach'] = $this->enrichCoachDetails($data['club']['coach'] ?? []);

        return $data;
    }
    public function fetchCompetitionClubs(string $url): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 FIT-ExternalClubImporter/1.0',
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->timeout(20)->retry(2, 250)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Foot Mercato HTTP '.$response->status().' pour '.$url);
        }

        $document = new DOMDocument();
        @$document->loadHTML($response->body());
        $xpath = new DOMXPath($document);
        $clubs = [];

        foreach ($xpath->query('//a[contains(@href, "/club/")]') as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }

            $href = trim($anchor->getAttribute('href'));
            $path = parse_url($href, PHP_URL_PATH) ?: '';
            if (! preg_match('~^/club/([^/]+)/?$~', $path, $match)) {
                continue;
            }

            $name = trim(preg_replace('/\s+/u', ' ', $anchor->textContent));
            if ($name === '') {
                continue;
            }

            $slug = $match[1];
            $clubs[$slug] = [
                'external_id' => $slug,
                'name' => $name,
                'club_url' => 'https://www.footmercato.net/club/'.$slug.'/',
                'squad_url' => 'https://www.footmercato.net/club/'.$slug.'/effectif/',
            ];
        }

        return array_values($clubs);
    }

    public function parseClubHtml(string $html, string $sourceUrl): array
    {
        $document = new DOMDocument();
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);

        $structured = $this->sportsTeamJsonLd($xpath);
        $profiles = $this->playerProfiles($xpath);
        $tables = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " complexTable ")]');

        $players = [];
        foreach ($tables as $table) {
            if (! $table instanceof DOMElement) {
                continue;
            }

            $position = $this->positionFromTable($xpath, $table);
            foreach ($xpath->query('.//tbody/tr[.//a[contains(@href, "/joueur/")]]', $table) as $row) {
                if (! $row instanceof DOMElement) {
                    continue;
                }

                $parsed = $this->parsePlayerRow($xpath, $row, $position);
                if ($parsed !== null) {
                    $profile = $profiles[$parsed['external_id']] ?? [];
                    $players[$parsed['external_id']] = array_replace($parsed, array_filter([
                        'name' => $profile['name'] ?? null,
                        'age' => $profile['age'] ?? null,
                        'photo_url' => $profile['photo_url'] ?? null,
                        'nationality_codes' => $profile['nationality_codes'] ?? null,
                    ], static fn ($value) => $value !== null && $value !== []));
                }
            }
        }
        return [
            'source' => self::SOURCE,
            'source_url' => $sourceUrl,
            'retrieved_at' => now()->toIso8601String(),
            'season' => $this->extractSeason($document->textContent ?? ''),
            'club' => [
                'external_id' => $this->slugFromUrl($structured['mainEntityOfPage'] ?? $sourceUrl, '/club/'),
                'name' => $structured['name'] ?? null,
                'short_name' => $structured['alternateName'] ?? null,
                'logo_url' => $structured['image'] ?? null,
                'gender' => $structured['gender'] ?? null,
                'sport' => $structured['sport'] ?? null,
                'coach' => [
                    'name' => $structured['coach']['name'] ?? null,
                    'profile_url' => $structured['coach']['mainEntityOfPage'] ?? null,
                ],
            ],
            'players' => array_values($players),
        ];
    }

    private function sportsTeamJsonLd(DOMXPath $xpath): array
    {
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $decoded = json_decode(html_entity_decode($script->textContent), true);
            if (! is_array($decoded)) {
                continue;
            }
            $items = array_is_list($decoded) ? $decoded : [$decoded];
            foreach ($items as $item) {
                if (is_array($item) && ($item['@type'] ?? null) === 'SportsTeam') {
                    return $item;
                }
            }
        }

        return [];
    }

    private function positionFromTable(DOMXPath $xpath, DOMElement $table): ?string
    {
        $heading = trim((string) $xpath->evaluate('string(.//thead//th[2])', $table));
        $normalized = mb_strtolower($heading);

        return match (true) {
            str_contains($normalized, 'gardien') => 'GK',
            str_contains($normalized, 'défenseur'), str_contains($normalized, 'defenseur') => 'DEF',
            str_contains($normalized, 'milieu') => 'MID',
            str_contains($normalized, 'attaquant') => 'FWD',
            default => null,
        };
    }
    private function playerProfiles(DOMXPath $xpath): array
    {
        $profiles = [];

        foreach ($xpath->query('//a[contains(@href, "/joueur/")]') as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }

            $profileUrl = trim($anchor->getAttribute('href'));
            $externalId = $this->slugFromUrl($profileUrl, '/joueur/');
            if ($externalId === null) {
                continue;
            }

            $nameNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__name ")]', $anchor)->item(0);
            $rawName = $nameNode
                ? trim($nameNode->childNodes->item(0)?->textContent ?? $nameNode->textContent)
                : trim($anchor->textContent);
            $name = preg_replace('/\s+/u', ' ', trim($rawName));

            $description = trim((string) $xpath->evaluate(
                'string(.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__description ")])',
                $anchor
            ));
            preg_match('/(\d{1,2})\s*ans/u', $description, $ageMatch);

            $portrait = $xpath->query('.//img[contains(@data-src, "/portrait/")]', $anchor)->item(0);
            $nationalities = [];
            foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__nationalities ")]//img', $anchor) as $flag) {
                if ($flag instanceof DOMElement && $flag->getAttribute('alt') !== '') {
                    $nationalities[] = strtoupper(trim($flag->getAttribute('alt')));
                }
            }

            $candidate = [
                'name' => $name !== '' ? $name : null,
                'age' => isset($ageMatch[1]) ? (int) $ageMatch[1] : null,
                'photo_url' => $portrait instanceof DOMElement ? $portrait->getAttribute('data-src') : null,
                'nationality_codes' => array_values(array_unique($nationalities)),
            ];

            if (! isset($profiles[$externalId])) {
                $profiles[$externalId] = $candidate;
                continue;
            }

            foreach ($candidate as $key => $value) {
                $current = $profiles[$externalId][$key] ?? null;
                $currentEmpty = $current === null || $current === '' || $current === [];
                $valuePresent = ! ($value === null || $value === '' || $value === []);

                if ($currentEmpty && $valuePresent) {
                    $profiles[$externalId][$key] = $value;
                }
            }
        }

        return $profiles;
    }

    private function parsePlayerRow(DOMXPath $xpath, DOMElement $row, ?string $position): ?array
    {
        $anchor = $xpath->query('.//a[contains(@href, "/joueur/")]', $row)->item(0);
        if (! $anchor instanceof DOMElement) {
            return null;
        }

        $profileUrl = trim($anchor->getAttribute('href'));
        $externalId = $this->slugFromUrl($profileUrl, '/joueur/');
        if ($externalId === null) {
            return null;
        }

        $nameNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__name ")]', $anchor)->item(0);
        $name = $nameNode ? trim($nameNode->childNodes->item(0)?->textContent ?? $nameNode->textContent) : trim($anchor->textContent);
        $description = trim((string) $xpath->evaluate('string(.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__description ")])', $anchor));

        preg_match('/(\d{1,2})\s*ans/u', $description, $ageMatch);
        $numberText = trim((string) $xpath->evaluate('string(./td[1])', $row));
        $number = ctype_digit($numberText) ? (int) $numberText : null;

        $portrait = $xpath->query('.//img[contains(@data-src, "/portrait/")]', $anchor)->item(0);
        $nationalities = [];
        foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " personCardCell__nationalities ")]//img', $anchor) as $flag) {
            if ($flag instanceof DOMElement && $flag->getAttribute('alt') !== '') {
                $nationalities[] = strtoupper(trim($flag->getAttribute('alt')));
            }
        }

        return [
            'external_id' => $externalId,
            'profile_url' => $profileUrl,
            'name' => preg_replace('/\s+/u', ' ', trim($name)),
            'age' => isset($ageMatch[1]) ? (int) $ageMatch[1] : null,
            'jersey_number' => $number,
            'position' => $position,
            'photo_url' => $portrait instanceof DOMElement ? $portrait->getAttribute('data-src') : null,
            'nationality_codes' => array_values(array_unique($nationalities)),
            'is_foreigner' => $row->getAttribute('data-foreigner') === '1',
            'contract_ending' => $row->getAttribute('data-contract_ending') === '1',
        ];
    }

    private function enrichPlayerDetails(array $players): array
    {
        $indexed = [];
        foreach ($players as $player) {
            $indexed[$player['external_id']] = $player;
        }

        foreach (array_chunk($players, 10) as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk) {
                $requests = [];
                foreach ($chunk as $player) {
                    $requests[] = $pool
                        ->as($player['external_id'])
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 FIT-ExternalClubImporter/1.0',
                            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
                        ])
                        ->timeout(20)
                        ->get($player['profile_url']);
                }

                return $requests;
            });

            foreach ($chunk as $player) {
                $response = $responses[$player['external_id']] ?? null;
                if (! $response || ! $response->successful()) {
                    continue;
                }

                $detail = $this->parsePersonHtml($response->body());
                if ($detail === []) {
                    continue;
                }

                $indexed[$player['external_id']] = array_replace(
                    $indexed[$player['external_id']],
                    array_filter($detail, static fn ($value) => $value !== null && $value !== '')
                );
            }
        }

        return array_values($indexed);
    }

    private function enrichCoachDetails(array $coach): array
    {
        $url = $coach['profile_url'] ?? null;
        if (! $url) {
            return $coach;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 FIT-ExternalClubImporter/1.0',
                'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
            ])->timeout(20)->retry(1, 200)->get($url);

            if ($response->successful()) {
                $detail = $this->parsePersonHtml($response->body());
                return array_replace($coach, array_filter($detail, static fn ($value) => $value !== null && $value !== ''));
            }
        } catch (\Throwable) {
            // Keep the structured club-level coach data when profile enrichment fails.
        }

        return $coach;
    }

    private function parsePersonHtml(string $html): array
    {
        $document = new DOMDocument();
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);

        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $decoded = json_decode(html_entity_decode($script->textContent), true);
            if (! is_array($decoded)) {
                continue;
            }

            $items = array_is_list($decoded) ? $decoded : [$decoded];
            foreach ($items as $item) {
                if (! is_array($item) || ($item['@type'] ?? null) !== 'Person') {
                    continue;
                }

                return [
                    'name' => $item['name'] ?? null,
                    'first_name' => $item['givenName'] ?? null,
                    'last_name' => $item['familyName'] ?? null,
                    'date_of_birth' => isset($item['birthDate']) ? substr((string) $item['birthDate'], 0, 10) : null,
                    'height' => $this->numericMeasure($item['height'] ?? null),
                    'weight' => $this->numericMeasure($item['weight'] ?? null),
                    'nationality' => is_array($item['nationality'] ?? null)
                        ? ($item['nationality']['name'] ?? null)
                        : ($item['nationality'] ?? null),
                    'photo_url' => $item['image'] ?? null,
                ];
            }
        }

        return [];
    }

    private function numericMeasure(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return preg_match('/(\d{2,3})/', (string) $value, $match)
            ? (int) $match[1]
            : null;
    }

    private function extractSeason(string $text): ?string
    {
        return preg_match('/20\d{2}\s*[\/\-]\s*20\d{2}/u', $text, $match)
            ? preg_replace('/\s+/', '', $match[0])
            : null;
    }
    private function slugFromUrl(?string $url, string $segment): ?string
    {
        if (! $url || ! str_contains($url, $segment)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $tail = explode($segment, $path, 2)[1] ?? '';
        $slug = trim($tail, '/');

        return $slug !== '' ? explode('/', $slug)[0] : null;
    }
}
