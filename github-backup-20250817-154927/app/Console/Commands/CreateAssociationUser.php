<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Association;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAssociationUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-association 
                            {name : The name of the user}
                            {email : The email address}
                            {association : The association name}
                            {role : The user role (association_admin, association_registrar, association_medical)}
                            {--password=password123 : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user for a specific association';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $associationName = $this->argument('association');
        $role = $this->argument('role');
        $password = $this->option('password');

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:association_admin,association_registrar,association_medical',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Find the association
        $association = Association::where('name', 'like', "%{$associationName}%")->first();
        
        if (!$association) {
            $this->error("Association '{$associationName}' not found. Available associations:");
            Association::all()->each(function ($association) {
                $this->line("- {$association->name} ({$association->country})");
            });
            return 1;
        }

        // Set permissions based on role
        $permissions = $this->getPermissionsForRole($role);

        // Create the user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'club_id' => null,
                'association_id' => $association->id,
                'fifa_connect_id' => $this->generateFifaConnectId($association, $role),
                'permissions' => $permissions,
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => "{$name} - {$association->name} {$role}"
            ]);

            $this->info("User created successfully!");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Association: {$association->name}");
            $this->info("Role: {$user->role}");
            $this->info("Password: {$password}");
            $this->info("FIFA Connect ID: {$user->fifa_connect_id}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create user: " . $e->getMessage());
            return 1;
        }
    }

    private function getPermissionsForRole(string $role): array
    {
        return match($role) {
            'association_admin' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'fifa_connect_access'
            ],
            'association_registrar' => [
                'player_registration_access',
                'competition_management_access'
            ],
            'association_medical' => [
                'healthcare_access',
                'player_registration_access'
            ],
            default => []
        };
    }

    private function generateFifaConnectId(Association $association, string $role): string
    {
        $assocShort = strtoupper(preg_replace('/[^A-Z]/', '', $association->short_name));
        $roleShort = strtoupper(substr($role, 12, 3)); // association_admin -> ADMIN
        $random = strtoupper(substr(md5(uniqid()), 0, 3));
        
        return "{$assocShort}_{$roleShort}_{$random}";
    }
} 