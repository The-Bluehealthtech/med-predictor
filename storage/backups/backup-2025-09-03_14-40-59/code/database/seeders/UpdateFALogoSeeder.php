<?php

namespace Database\Seeders;

use App\Models\Association;
use Illuminate\Database\Seeder;

class UpdateFALogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fa = Association::where('name', 'The Football Association')->first();
        
        if ($fa) {
            // Update with the official FA logo URL from their CDN
            $fa->update([
                'association_logo_url' => 'https://cdn.thefa.com/thefawebsite/assets/images/the-fa-logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'
            ]);
            
            $this->command->info('FA logo updated successfully!');
            $this->command->info('New logo URL: ' . $fa->association_logo_url);
        } else {
            $this->command->error('The Football Association not found in database.');
        }
    }
} 