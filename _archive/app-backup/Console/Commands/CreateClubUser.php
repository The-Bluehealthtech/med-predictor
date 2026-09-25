<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateClubUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-club 
                            {name : The name of the user}
                            {email : The email address}
                            {club : The club name}
                            {role : The user role (club_admin, club_manager, club_medical)}
                            {--password=password123 : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user for a specific club';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $clubName = $this->argument('club');
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
            'role' => 'required|in:club_admin,club_manager,club_medical',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Find the club
        $club = Club::where('name', 'like', "%{$clubName}%")->first();
        
        if (!$club) {
            $this->error("Club '{$clubName}' not found. Available clubs:");
            Club::all()->each(function ($club) {
                $this->line("- {$club->name}");
            });
            return 1;
        }

        // Find the association
        $association = $club->association;
        
        if (!$association) {
            $this->error("No association found for club '{$club->name}'");
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
                'club_id' => $club->id,
                'association_id' => $association->id,
                'fifa_connect_id' => $this->generateFifaConnectId($club, $role),
                'permissions' => $permissions,
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => "{$name} - {$club->name} {$role}"
            ]);

            $this->info("User created successfully!");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Club: {$club->name}");
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
            'club_admin' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'fifa_connect_access'
            ],
            'club_manager' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access'
            ],
            'club_medical' => [
                'healthcare_access'
            ],
            default => []
        };
    }

    private function generateFifaConnectId(Club $club, string $role): string
    {
        $clubShort = strtoupper(preg_replace('/[^A-Z]/', '', $club->name));
        $roleShort = strtoupper(substr($role, 5, 3)); // club_admin -> ADMIN
        $random = strtoupper(substr(md5(uniqid()), 0, 3));
        
        return "{$clubShort}_{$roleShort}_{$random}";
    }
} 