<?php

namespace Tests\Concerns;

use App\Models\Player;
use App\Models\PlayerConsent;
use App\Models\PrivacyPolicy;

/** Consentement actif « permit » au partage hors du club (IHE PCF), pour les tests des échanges externes. */
trait GrantsSharingConsent
{
    protected function grantSharingConsent(Player $player): PlayerConsent
    {
        $association = (int) ($player->association_id ?: $player->club?->association_id ?: 0);
        $policy = PrivacyPolicy::query()->firstOrCreate(['association_id' => $association, 'version' => 1],
            ['title' => 'Politique de test', 'body' => 'Partage à des fins de soins.', 'body_sha256' => hash('sha256', 'Partage à des fins de soins.'), 'published_at' => now()]);

        return PlayerConsent::query()->create(['player_id' => $player->id, 'privacy_policy_id' => $policy->id, 'decision' => 'permit', 'status' => 'active',
            'performer_type' => 'player', 'performer_name' => 'Joueur de test', 'signed_at' => now()]);
    }
}
