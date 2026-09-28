<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaudiHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $confederationId = DB::table('confederations')->updateOrInsert(
            ['short_name' => 'AFC'],
            [
                'name' => 'Confédération asiatique de football',
                'country' => 'Asie',
                'status' => 'active',
                'fifa_sync_status' => 'pending',
                'founded_year' => 1954,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
        $confederation = DB::table('confederations')->where('short_name', 'AFC')->first();
        $association = DB::table('associations')
            ->where('short_name', 'SAFF')
            ->orWhere('name', 'Fédération saoudienne de football')
            ->first();

        $associationData = [
            'name' => 'Fédération saoudienne de football',
            'short_name' => 'SAFF',
            'country' => 'Arabie saoudite',
            'confederation_id' => $confederation->id,
            'confederation' => 'AFC',
            'status' => 'active',
            'fifa_id' => 'SAFF',
            'updated_at' => $now,
        ];

        if ($association) {
            DB::table('associations')->where('id', $association->id)->update($associationData);
            $associationId = $association->id;
        } else {
            $associationData['created_at'] = $now;
            $associationId = DB::table('associations')->insertGetId($associationData);
        }

        DB::table('clubs')->updateOrInsert(
            ['name' => 'Al-Hazem SC'],
            [
                'short_name' => 'Al-Hazem',
                'country' => 'Arabie saoudite',
                'city' => 'Ar Rass',
                'stadium' => 'Al-Hazem Club Stadium',
                'stadium_name' => 'Al-Hazem Club Stadium',
                'association_id' => $associationId,
                'league' => 'Saudi Pro League',
                'status' => 'active',
                'founded_year' => 1957,
                'website' => 'https://alhazem.sa',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $this->command?->info('Hiérarchie AFC → SAFF → Al-Hazem SC créée.');
    }
}
