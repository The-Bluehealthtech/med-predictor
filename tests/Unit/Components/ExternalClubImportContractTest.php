<?php

namespace Tests\Unit\Components;

use App\Services\ExternalClubImport\Providers\FootMercatoProvider;
use Tests\TestCase;

class ExternalClubImportContractTest extends TestCase
{
    public function test_footmercato_parser_extracts_club_and_squad_fields(): void
    {
        $html = <<<'HTML'
<html><body>
<script type="application/ld+json">
[
  {"@context":"https://schema.org","@type":"SportsTeam","name":"Al Hazm Rass",
   "alternateName":"Hazem","image":"https://cdn.example/al-hazm.png",
   "mainEntityOfPage":"https://www.footmercato.net/club/al-hazm/",
   "coach":{"@type":"Person","name":"Jalel Kadri"}}
]
</script>
<div>2026/2027</div>
<table class="complexTable">
<thead><tr><th>#</th><th>Gardiens</th></tr></thead>
<tbody>
<tr data-foreigner="1" data-contract_ending="1">
<td>14</td>
<td>
<a class="personCardCell" href="https://www.footmercato.net/joueur/bruno-varela/">
<span class="personCardCell__infos">
<span class="personCardCell__name">Bruno Varela</span>
<span class="personCardCell__description">31 ans</span>
</span>
<img data-src="https://cdn.example/portrait/bruno-varela.png" alt="Bruno Varela">
<span class="personCardCell__nationalities"><img alt="CPV"></span>
</a>
</td>
</tr>
</tbody>
</table>
</body></html>
HTML;

        $data = app(FootMercatoProvider::class)->parseClubHtml(
            $html,
            'https://www.footmercato.net/club/al-hazm/effectif/'
        );

        $this->assertSame('Al Hazm Rass', $data['club']['name']);
        $this->assertSame('al-hazm', $data['club']['external_id']);
        $this->assertSame('2026/2027', $data['season']);
        $this->assertCount(1, $data['players']);
        $player = $data['players'][0];
        $this->assertSame('bruno-varela', $player['external_id']);
        $this->assertSame('GK', $player['position']);
        $this->assertSame(14, $player['jersey_number']);
        $this->assertSame(31, $player['age']);
        $this->assertSame(['CPV'], $player['nationality_codes']);
        $this->assertTrue($player['is_foreigner']);
        $this->assertTrue($player['contract_ending']);
        $this->assertSame('https://cdn.example/portrait/bruno-varela.png', $player['photo_url']);
    }
}
