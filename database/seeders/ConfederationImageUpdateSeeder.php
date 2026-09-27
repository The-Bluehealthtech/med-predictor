<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfederationImageUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('confederations')->where('short_name', 'CAF')->update([
            'confederation_logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Caf_llogo.svg',
            'updated_at' => $now,
        ]);
        DB::table('associations')->where('name', 'Fédération Tunisienne de Football')->update([
            'association_logo_url' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Tunisian%20Football%20Federation%20logo.svg',
            'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/c/ce/Flag_of_Tunisia.svg',
            'updated_at' => $now,
        ]);
        $this->command?->info('Logo CAF et drapeau tunisien mis à jour.');
    }
}
