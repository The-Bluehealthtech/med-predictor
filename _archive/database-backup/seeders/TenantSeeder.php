<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system tenant
        $systemTenant = Tenant::create([
            'name' => 'FIT System',
            'slug' => 'fit-system',
            'display_name' => 'FIT Platform System',
            'description' => 'System tenant for FIT platform administration',
            'type' => 'system',
            'status' => 'active',
            'timezone' => 'UTC',
            'language' => 'en',
            'settings' => [
                'fifa_sync_enabled' => true,
                'audit_logging_enabled' => true,
                'multi_tenant_enabled' => true,
            ],
            'metadata' => [
                'version' => '1.0.0',
                'environment' => config('app.env'),
            ],
        ]);

        // Create default federation tenant
        $federationTenant = Tenant::create([
            'name' => 'Fédération Tunisienne de Football',
            'slug' => 'ftf',
            'display_name' => 'FTF - Fédération Tunisienne de Football',
            'description' => 'Fédération Tunisienne de Football - FIFA Member',
            'type' => 'federation',
            'status' => 'active',
            'fifa_connect_id' => 'TUN_FED_001',
            'fifa_code' => 'TUN',
            'country' => 'TN',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'website' => 'https://www.ftf.org.tn',
            'email' => 'contact@ftf.org.tn',
            'phone' => '+216 71 234 567',
            'address' => 'Stade Olympique de Radès, 2040 Radès, Tunisie',
            'settings' => [
                'fifa_sync_enabled' => true,
                'local_competitions_enabled' => true,
                'player_registration_enabled' => true,
            ],
            'metadata' => [
                'fifa_member_since' => '1960',
                'confederation' => 'CAF',
                'region' => 'North Africa',
            ],
            'parent_tenant_id' => $systemTenant->id,
        ]);

        // Create association tenant
        $associationTenant = Tenant::create([
            'name' => 'Ligue Tunisienne de Football',
            'slug' => 'ltf',
            'display_name' => 'LTF - Ligue Tunisienne de Football',
            'description' => 'Ligue Tunisienne de Football - Association de Football',
            'type' => 'association',
            'status' => 'active',
            'fifa_connect_id' => 'TUN_ASSOC_001',
            'fifa_code' => 'TUN',
            'country' => 'TN',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'website' => 'https://www.ltf.org.tn',
            'email' => 'contact@ltf.org.tn',
            'phone' => '+216 71 345 678',
            'address' => 'Complexe Sportif de Tunis, 1000 Tunis, Tunisie',
            'settings' => [
                'competition_management_enabled' => true,
                'referee_management_enabled' => true,
                'club_registration_enabled' => true,
            ],
            'metadata' => [
                'league_level' => 'professional',
                'season_format' => 'annual',
                'promotion_relegation' => true,
            ],
            'parent_tenant_id' => $federationTenant->id,
        ]);

        // Create club tenant
        $clubTenant = Tenant::create([
            'name' => 'Espérance Sportive de Tunis',
            'slug' => 'est',
            'display_name' => 'EST - Espérance Sportive de Tunis',
            'description' => 'Espérance Sportive de Tunis - Club de Football',
            'type' => 'club',
            'status' => 'active',
            'fifa_connect_id' => 'TUN_CLUB_001',
            'fifa_code' => 'TUN',
            'country' => 'TN',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'website' => 'https://www.est.org.tn',
            'email' => 'contact@est.org.tn',
            'phone' => '+216 71 456 789',
            'address' => 'Stade Olympique de Radès, 2040 Radès, Tunisie',
            'settings' => [
                'player_management_enabled' => true,
                'team_management_enabled' => true,
                'medical_records_enabled' => true,
            ],
            'metadata' => [
                'founded_year' => '1919',
                'club_level' => 'professional',
                'home_stadium' => 'Stade Olympique de Radès',
                'capacity' => 60000,
            ],
            'parent_tenant_id' => $associationTenant->id,
        ]);

        // Create system admin user
        $systemAdmin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@fit.tbhc.uk',
            'password' => bcrypt('password'),
            'role' => 'system_admin',
            'tenant_id' => $systemTenant->id,
            'status' => 'active',
            'timezone' => 'UTC',
            'language' => 'en',
            'notifications_email' => true,
            'notifications_sms' => false,
        ]);

        // Create federation admin user
        $federationAdmin = User::create([
            'name' => 'FTF Administrator',
            'email' => 'admin@ftf.org.tn',
            'password' => bcrypt('password'),
            'role' => 'association_admin',
            'tenant_id' => $federationTenant->id,
            'status' => 'active',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_sms' => false,
        ]);

        // Create association admin user
        $associationAdmin = User::create([
            'name' => 'LTF Administrator',
            'email' => 'admin@ltf.org.tn',
            'password' => bcrypt('password'),
            'role' => 'association_admin',
            'tenant_id' => $associationTenant->id,
            'status' => 'active',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_sms' => false,
        ]);

        // Create club admin user
        $clubAdmin = User::create([
            'name' => 'EST Administrator',
            'email' => 'admin@est.org.tn',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'tenant_id' => $clubTenant->id,
            'status' => 'active',
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_sms' => false,
        ]);

        // Update created_by and updated_by for tenants
        $systemTenant->update(['created_by' => $systemAdmin->id, 'updated_by' => $systemAdmin->id]);
        $federationTenant->update(['created_by' => $systemAdmin->id, 'updated_by' => $systemAdmin->id]);
        $associationTenant->update(['created_by' => $federationAdmin->id, 'updated_by' => $federationAdmin->id]);
        $clubTenant->update(['created_by' => $associationAdmin->id, 'updated_by' => $associationAdmin->id]);

        $this->command->info('Tenant data seeded successfully!');
        $this->command->info('Created tenants:');
        $this->command->info('- System Tenant: FIT System');
        $this->command->info('- Federation Tenant: FTF');
        $this->command->info('- Association Tenant: LTF');
        $this->command->info('- Club Tenant: EST');
        $this->command->info('');
        $this->command->info('Created users:');
        $this->command->info('- System Admin: admin@fit.tbhc.uk');
        $this->command->info('- Federation Admin: admin@ftf.org.tn');
        $this->command->info('- Association Admin: admin@ltf.org.tn');
        $this->command->info('- Club Admin: admin@est.org.tn');
        $this->command->info('');
        $this->command->info('Default password for all users: password');
    }
}






