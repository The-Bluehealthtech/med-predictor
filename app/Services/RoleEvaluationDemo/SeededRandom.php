<?php

namespace App\Services\RoleEvaluationDemo;

/**
 * Générateur pseudo-aléatoire déterministe pour le Livrable 3 ("reproductible,
 * graine fixe" — mandat, LIVRABLE 3).
 *
 * Implémentation auto-contenue (xorshift32), PAS un wrapper autour de
 * mt_srand()/mt_rand() : un premier essai basé sur mt_srand() s'est révélé
 * FAUX à l'exécution réelle (voir rapport Livrable 3, validation) — mt_rand()
 * s'appuie sur un état GLOBAL au processus PHP, donc deux instances
 * SeededRandom construites dans le même processus se marchent dessus dès que
 * leurs appels s'entrelacent (mt_srand() du second constructeur réinitialise
 * l'état global utilisé par le premier objet). L'implémentation ci-dessous
 * porte tout son état dans l'objet lui-même : deux instances, même
 * entrelacées, restent totalement indépendantes et chacune reproductible
 * pour sa propre graine.
 *
 * xorshift32 n'est pas cryptographique (inutile ici : ce n'est que de la
 * donnée de démonstration) et reste déterministe sur une installation PHP
 * 64 bits comme celle-ci (toutes les opérations sont masquées sur 32 bits) —
 * voir "Ce que je n'ai pas pu vérifier" du rapport pour la portabilité 32
 * bits, non testée.
 */
class SeededRandom
{
    private int $seed;

    private int $state;

    public function __construct(int $seed)
    {
        $this->seed = $seed;
        // xorshift ne doit jamais démarrer sur un état à zéro (point fixe).
        $this->state = ($seed & 0xFFFFFFFF) ?: 0x9E3779B9;
    }

    public function seed(): int
    {
        return $this->seed;
    }

    private function nextUint32(): int
    {
        $x = $this->state;
        $x ^= ($x << 13) & 0xFFFFFFFF;
        $x ^= $x >> 17;
        $x ^= ($x << 5) & 0xFFFFFFFF;
        $x &= 0xFFFFFFFF;
        $this->state = $x;

        return $x;
    }

    public function nextInt(int $min, int $max): int
    {
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }
        $range = $max - $min + 1;

        return $min + ($this->nextUint32() % $range);
    }

    public function nextFloat(float $min = 0.0, float $max = 1.0): float
    {
        $f = $this->nextUint32() / 4294967295.0; // [0,1]

        return $min + $f * ($max - $min);
    }

    public function chance(float $probability): bool
    {
        return $this->nextFloat(0.0, 1.0) < $probability;
    }

    /**
     * Bruit gaussien (Box-Muller), centré sur 0, d'écart-type $stddev.
     * Utilisé pour donner à chaque match un aléa propre autour du niveau
     * moyen d'un joueur (mandat : "bruit").
     */
    public function gaussianNoise(float $stddev): float
    {
        $u1 = max($this->nextFloat(0.0, 1.0), 1e-9);
        $u2 = $this->nextFloat(0.0, 1.0);
        $z = sqrt(-2.0 * log($u1)) * cos(2.0 * M_PI * $u2);

        return $z * $stddev;
    }

    /**
     * @param array<int, mixed> $items
     */
    public function pick(array $items): mixed
    {
        $items = array_values($items);

        return $items[$this->nextInt(0, count($items) - 1)];
    }

    /**
     * Fisher-Yates déterministe.
     *
     * @param array<int, mixed> $items
     * @return array<int, mixed>
     */
    public function shuffleArray(array $items): array
    {
        $items = array_values($items);
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = $this->nextInt(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        return $items;
    }

    /**
     * Entier clampé dans [$min, $max] autour d'une moyenne avec bruit gaussien.
     */
    public function noisyCount(float $mean, float $stddev, int $min = 0, ?int $max = null): int
    {
        $value = (int) round($mean + $this->gaussianNoise($stddev));
        $value = max($min, $value);
        if ($max !== null) {
            $value = min($max, $value);
        }

        return $value;
    }
}
