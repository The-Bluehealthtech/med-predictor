<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AfricanConfederationTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $id = DB::table('confederations')->where('short_name', 'CAF')->value('id');
        if (!$id) {
            $id = DB::table('confederations')->insertGetId([
                'name' => 'Confédération Africaine de Football',
                'short_name' => 'CAF', 'country' => 'Afrique', 'status' => 'active',
                'founded_year' => 1957, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        DB::table('associations')->where('name', 'Fédération Tunisienne de Football')->update([
            'confederation' => 'CAF', 'updated_at' => $now,
        ]);
        $this->command?->info("Confédération africaine disponible : CAF (#{$id}).");
    }
}
