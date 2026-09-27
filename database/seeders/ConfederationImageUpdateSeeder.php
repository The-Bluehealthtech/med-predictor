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
            'confederation_logo_url' => 'https://www.cafonline.com/media/e5joy0ul/cafonline.jpg',
            'updated_at' => $now,
        ]);
        DB::table('associations')->where('name', 'Fédération Tunisienne de Football')->update([
            'association_logo_url' => 'https://www.ftf.org.tn/fr/wp-content/uploads/2014/12/LOGO-FTF_df84263d3942d7a318831d94f0873c4e.png',
            'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/c/ce/Flag_of_Tunisia.svg',
            'updated_at' => $now,
        ]);
        $this->command?->info('Logo CAF et drapeau tunisien mis à jour.');
    }
}
