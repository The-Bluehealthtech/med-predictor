<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\ClubOfficial;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncSaudiClubLeaders extends Command
{
    protected $signature = 'clubs:sync-saudi-leaders {--commit : Write to canonical database}';
    protected $description = 'Synchronise the principal executive/board leader of Saudi Pro League clubs';

    private array $leaders = [
        ['club'=>'Abha','name'=>'Ahmed Al-Hodithy','role'=>'President','description'=>'President','source'=>'lequipe','url'=>'https://www.lequipe.fr/Football/FootballFicheClub10827.html'],
        ['club'=>'Al Ahli','name'=>'Fabrice Bocquet','role'=>'Other','description'=>'Chief Executive Officer','source'=>'alahlifc','url'=>'https://en.alahlifc.sa/news/al-ahli-appoints-fabrice-bocquet-as-chief-executive-officer'],
        ['club'=>'Al Fateh','name'=>'Mansour Ibrahim Al-Afaliq','role'=>'President','description'=>'Chairman of the Board','source'=>'fatehclub','url'=>'https://www.fatehclub.com/en/strategy'],
        ['club'=>'Al Fayha','name'=>'Tawfiq Al-Modaiheem','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Al-Fayha_FC_season'],
        ['club'=>'Al Faisaly','name'=>'Abdulmajeed Al-Omaim','role'=>'President','description'=>'Chairman','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/Al_Faisaly_FC'],
        ['club'=>'Al Hazm','name'=>'Salman Al-Malik','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Alhazem_season'],
        ['club'=>'Al Hilal','name'=>'Nawaf bin Saad','role'=>'President','description'=>'Chairman of the Board','source'=>'alhilal','url'=>'https://alhilal.com/en/the-club/board-of-directors'],
        ['club'=>'Al Ettifaq','name'=>'Samer Al-Misehal','role'=>'President','description'=>'President','source'=>'ettifaq','url'=>'https://ettifaq.com/club-vision/'],
        ['club'=>'Al Ittihad','name'=>'Domingos Oliveira','role'=>'Other','description'=>'Chief Executive Officer','source'=>'ittihadclub','url'=>'https://www.ittihadclub.sa/en/news/al-ittihad-extends-contract-of-ceo-domingos-olivera'],
        ['club'=>'Al Khaleej','name'=>'Ahmed Khuraidah','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Al-Khaleej_FC_season'],
        ['club'=>'Al Kholood','name'=>'Ben Harburg','role'=>'President','description'=>'Chairman / Owner representative','source'=>'spl','url'=>'https://www.spl.com.sa/en/news/ben-harburg-pioneering-owner-plotting-new-era-at-al-kholood'],
        ['club'=>'Al Nassr','name'=>'Abdullah Al-Majid','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Al-Nassr_FC_season'],
        ['club'=>'Al Qadsiah','name'=>'Rami Al-Turki','role'=>'President','description'=>'Acting Chairman','source'=>'okaz','url'=>'https://www.okaz.com.sa/sport/na/2256653'],
        ['club'=>'Al Riyadh','name'=>'Bandar Al-Muqail','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Al-Riyadh_SC_season'],
        ['club'=>'Al Shabab','name'=>'Abdulaziz bin Fahd Al-Malik','role'=>'President','description'=>'President','source'=>'alshabab','url'=>'https://www.alshabab-sc.sa/blogs/802'],
        ['club'=>'Al Taawoun','name'=>'Badr Al-Ghannam','role'=>'President','description'=>'President','source'=>'public_directory','url'=>'https://en.wikipedia.org/wiki/2025%E2%80%9326_Al-Taawoun_FC_season'],
        ['club'=>'Diriyah','name'=>'Khalid bin Mohammed bin Saud','role'=>'President','description'=>'Chairman of the Board','source'=>'diriyahcompany','url'=>'https://www.diriyahcompany.sa/en/news/diriyah-club'],
        ['club'=>'NEOM','name'=>'Meshari Al-Motairi','role'=>'President','description'=>'Chairman','source'=>'neom','url'=>'https://www.neom.com/en-us/newsroom/neom-introduces-neom-sports-club'],
    ];

    public function handle(): int
    {
        $commit = (bool) $this->option('commit');
        $rows = [];
        foreach ($this->leaders as $item) {
            $club = $this->findClub($item['club']);
            $rows[] = [$item['club'], $club?->name ?? 'NOT FOUND', $item['name'], $item['description']];
            if (! $commit || ! $club) continue;
            $this->upsertLeader($club, $item);
        }
        $this->table(['Source club','FIT club','Dirigeant','Rôle'], $rows);
        $this->info($commit ? 'Dirigeants synchronisés dans club_officials.' : 'Prévisualisation uniquement. Utilisez --commit pour écrire.');
        return self::SUCCESS;
    }

    private function findClub(string $needle): ?Club
    {
        $n = Str::of($needle)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim()->toString();
        return Club::query()->get()->first(function (Club $club) use ($n) {
            $c = Str::of($club->name)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim()->toString();
            return $c === $n || str_starts_with($c, $n.' ') || str_starts_with($n, $c.' ')
                || ($n === 'al nassr' && str_contains($c, 'al nasr'))
                || ($n === 'al qadsiah' && str_contains($c, 'al quadisiya'))
                || ($n === 'al ettifaq' && str_contains($c, 'al ittifaq'))
                || ($n === 'al hazm' && str_contains($c, 'al hazem'))
                || ($n === 'al faisaly' && str_contains($c, 'al faysaly'));
        });
    }

    private function upsertLeader(Club $club, array $item): void
    {
        [$first, $last] = $this->splitName($item['name']);
        $official = ClubOfficial::query()->where('club_id', $club->id)
            ->where('registration_type', ClubOfficial::ORGANISATION_OFFICIAL)
            ->whereRaw("LOWER(TRIM(international_first_name || ' ' || international_last_name)) = ?", [mb_strtolower($item['name'])])
            ->first() ?? new ClubOfficial();
        $official->club_id = $club->id;
        $official->international_first_name = $first;
        $official->international_last_name = $last;
        $official->gender = $official->gender ?: 'male';
        $official->registration_type = ClubOfficial::ORGANISATION_OFFICIAL;
        $official->team_official_role = null;
        $official->organisation_official_role = $item['role'];
        $official->role_description = $item['description'];
        $official->is_head_coach = false;
        $official->status = 'active';
        $official->discipline = 'Football';
        $official->registration_valid_from = $official->registration_valid_from ?: now()->startOfYear()->toDateString();
        $official->source = $item['source'];
        $official->source_url = $item['url'];
        $official->retrieved_at = now();
        $official->save();
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [];
        $first = array_shift($parts) ?: $name;
        return [$first, implode(' ', $parts) ?: $first];
    }
}
