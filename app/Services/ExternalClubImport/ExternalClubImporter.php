<?php

namespace App\Services\ExternalClubImport;

use App\Models\Club;
use App\Models\Player;
use App\Services\ExternalClubImport\Providers\FootMercatoProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExternalClubImporter
{
    public function __construct(private FootMercatoProvider $footMercato)
    {
    }

    public function preview(string $url): array
    {
        $data = $this->footMercato->fetchClub($url);
        $club = $this->matchClub($data['club']);
        $matches = collect($data['players'])->map(function (array $player) use ($club) {
            return $this->previewPlayerMatch($player, $club);
        })->all();

        return [
            'data' => $data,
            'club_match' => $club ? ['id' => $club->id, 'name' => $club->name] : null,
            'player_matches' => $matches,
            'summary' => [
                'players_found' => count($data['players']),
                'players_matched' => collect($matches)->where('local_id', '!=', null)->count(),
                'players_new' => collect($matches)->whereNull('local_id')->count(),
                'photos_found' => collect($data['players'])->whereNotNull('photo_url')->count(),
                'dob_found' => collect($data['players'])->whereNotNull('date_of_birth')->count(),
                'nationalities_found' => collect($data['players'])->whereNotNull('nationality')->count(),
                'height_found' => collect($data['players'])->whereNotNull('height')->count(),
                'weight_found' => collect($data['players'])->whereNotNull('weight')->count(),
            ],
        ];
    }
    public function import(string $url, bool $downloadMedia = true): array
    {
        $preview = $this->preview($url);
        $data = $preview['data'];

        return DB::transaction(function () use ($data, $downloadMedia) {
            $batchId = $this->startImportBatch($data, $downloadMedia);
            $club = $this->upsertClub($data['club'], $data, $downloadMedia);
            $created = 0;
            $updated = 0;
            $externalLinks = [];

            foreach ($data['players'] as $externalPlayer) {
                $player = $this->matchPlayer($externalPlayer, $club);
                $isNew = ! $player;
                $player ??= new Player();

                $this->fillPlayer($player, $externalPlayer, $club, $downloadMedia, $isNew ? $batchId : null);
                $player->save();

                $externalLinks[$externalPlayer['external_id']] = $player->id;
                $isNew ? $created++ : $updated++;
            }

            $result = [
                'club_id' => $club->id,
                'club_name' => $club->name,
                'players_created' => $created,
                'players_updated' => $updated,
                'players_total' => count($data['players']),
                'import_batch_id' => $batchId,
                'external_links' => $externalLinks,
            ];

            $this->finishImportBatch($batchId, $result, $data);

            return $result;
        });
    }
    private function matchClub(array $external): ?Club
    {
        $name = trim((string) ($external['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $exact = Club::query()
            ->where('name', $name)
            ->orWhere('short_name', $external['short_name'] ?? $name)
            ->first();

        if ($exact) {
            return $exact;
        }

        $needle = $this->normalizeClubName($name);
        return Club::query()->get()->first(function (Club $club) use ($needle) {
            $candidate = $this->normalizeClubName($club->name);
            return $candidate !== ''
                && ($candidate === $needle
                    || str_starts_with($needle, $candidate.' ')
                    || str_starts_with($candidate, $needle.' '));
        });
    }

    private function previewPlayerMatch(array $external, ?Club $club): array
    {
        $player = $club ? $this->matchPlayer($external, $club) : null;

        return [
            'external_id' => $external['external_id'],
            'name' => $external['name'],
            'local_id' => $player?->id,
            'local_name' => $player?->name,
            'photo_url' => $external['photo_url'] ?? null,
            'position' => $external['position'] ?? null,
            'jersey_number' => $external['jersey_number'] ?? null,
        ];
    }

    private function matchPlayer(array $external, Club $club): ?Player
    {
        $historical = $this->matchPlayerFromImportHistory($external['external_id'], $club);
        if ($historical) {
            return $historical;
        }

        $query = Player::query()->where('club_id', $club->id);

        if (! empty($external['photo_url'])) {
            $byPhoto = (clone $query)->where('player_face_url', $external['photo_url'])->first();
            if ($byPhoto) {
                return $byPhoto;
            }
        }

        return $query
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($external['name'])])
            ->first();
    }
    private function matchPlayerFromImportHistory(string $externalId, Club $club): ?Player
    {
        if (! Schema::hasTable('import_batches') || ! Schema::hasColumn('import_batches', 'report')) {
            return null;
        }

        $reports = DB::table('import_batches')
            ->where('source_label', FootMercatoProvider::SOURCE)
            ->where('status', 'completed')
            ->whereNotNull('report')
            ->orderByDesc('id')
            ->limit(50)
            ->pluck('report');

        foreach ($reports as $rawReport) {
            $report = is_array($rawReport) ? $rawReport : json_decode((string) $rawReport, true);
            $localId = $report['result']['external_links'][$externalId] ?? null;
            if (! $localId) {
                continue;
            }

            $player = Player::query()
                ->where('id', $localId)
                ->where('club_id', $club->id)
                ->first();

            if ($player) {
                return $player;
            }
        }

        return null;
    }

    private function upsertClub(array $external, array $data, bool $downloadMedia): Club
    {
        $club = $this->matchClub($external) ?? new Club();
        $club->name = $external['name'] ?: ($club->name ?? 'Club externe');
        $club->short_name = $external['short_name'] ?: $club->short_name;
        $club->status = $club->status ?: 'active';
        $club->league = $club->league ?: 'Saudi Pro League';

        if ($external['logo_url'] ?? null) {
            $club->logo_url = $external['logo_url'];
            if ($downloadMedia) {
                $path = $this->downloadMedia($external['logo_url'], 'clubs/'.Str::slug($club->name));
                if ($path) {
                    $club->logo_path = $path;
                    if (Schema::hasColumn('clubs', 'logo_image')) {
                        $club->logo_image = $path;
                    }
                }
            }
        }

        $club->save();

        return $club;
    }

    private function fillPlayer(
        Player $player,
        array $external,
        Club $club,
        bool $downloadMedia,
        ?int $importBatchId = null
    ): void
    {
        $player->name = $external['name'];
        $player->club_id = $club->id;
        $player->association_id = $player->association_id ?: $club->association_id;
        $player->position = $external['position'] ?? $player->position;
        $player->age = $player->age ?? ($external['age'] ?? null);
        $player->jersey_number = $external['jersey_number'] ?? $player->jersey_number;

        $player->first_name = $player->first_name ?: ($external['first_name'] ?? null);
        $player->last_name = $player->last_name ?: ($external['last_name'] ?? null);
        $player->nationality = $player->nationality ?: ($external['nationality'] ?? null);
        $player->height = $player->height ?: ($external['height'] ?? null);
        $player->weight = $player->weight ?: ($external['weight'] ?? null);

        if (! $player->date_of_birth && ! empty($external['date_of_birth'])) {
            $player->date_of_birth = $external['date_of_birth'];
        }
        if ($external['photo_url'] ?? null) {
            $player->player_face_url = $external['photo_url'];
            if ($downloadMedia) {
                $path = $this->downloadMedia($external['photo_url'], 'players/'.$external['external_id']);
                if ($path) {
                    if (Schema::hasColumn('players', 'player_picture')) {
                        $player->player_picture = $path;
                    }
                    if (Schema::hasColumn('players', 'profile_image')) {
                        $player->profile_image = $path;
                    }
                }
            }
        }

        if (Schema::hasColumn('players', 'club_logo_url') && $club->logo_url) {
            $player->club_logo_url = $club->logo_url;
        }

        if ($importBatchId && Schema::hasColumn('players', 'import_batch_id')) {
            $player->import_batch_id = $importBatchId;
        }
    }

    private function downloadMedia(string $url, string $basePath): ?string
    {
        try {
            $response = Http::timeout(20)->retry(2, 250)->get($url);
            if (! $response->successful()) {
                return null;
            }

            $contentType = strtolower($response->header('Content-Type', ''));
            $extension = str_contains($contentType, 'png') ? 'png'
                : (str_contains($contentType, 'webp') ? 'webp' : 'jpg');

            $path = 'external/footmercato/'.$basePath.'.'.$extension;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
    private function normalizeClubName(?string $name): string
    {
        return (string) Str::of((string) $name)
            ->ascii()
            ->lower()
            ->replace(['-', '_'], ' ')
            ->squish();
    }

    private function startImportBatch(array $data, bool $downloadMedia): ?int
    {
        if (! Schema::hasTable('import_batches')) {
            return null;
        }

        return DB::table('import_batches')->insertGetId([
            'batch_type' => 'import',
            'source_label' => FootMercatoProvider::SOURCE,
            'status' => 'running',
            'params' => json_encode([
                'domain' => 'club_squad',
                'club_external_id' => $data['club']['external_id'] ?? null,
                'club_name' => $data['club']['name'] ?? null,
                'source_url' => $data['source_url'] ?? null,
                'season' => $data['season'] ?? null,
                'download_media' => $downloadMedia,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function finishImportBatch(?int $batchId, array $result, array $data): void
    {
        if (! $batchId || ! Schema::hasTable('import_batches')) {
            return;
        }

        $updates = [
            'status' => 'completed',
            'finished_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('import_batches', 'rows_read')) {
            $updates['rows_read'] = count($data['players']);
            $updates['rows_imported'] = count($data['players']);
            $updates['rows_rejected'] = 0;
            $updates['report'] = json_encode([
                'result' => $result,
                'external_players' => collect($data['players'])->map(fn (array $player) => [
                    'external_id' => $player['external_id'],
                    'profile_url' => $player['profile_url'],
                    'name' => $player['name'],
                ])->values()->all(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        DB::table('import_batches')->where('id', $batchId)->update($updates);
    }
}
