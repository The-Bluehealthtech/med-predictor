<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credentials:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show available login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔑 Available Login Credentials');
        $this->info('==============================');
        
        $users = User::all(['name', 'email']);
        
        if ($users->isEmpty()) {
            $this->error('No users found in database!');
            $this->info('Run: php artisan db:seed --class=UserSeeder');
            return 1;
        }
        
        $this->table(
            ['Name', 'Email', 'Password'],
            $users->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    'password123'
                ];
            })->toArray()
        );
        
        $this->info('');
        $this->info('💡 Default password for all users: password123');
        $this->info('🌐 Login URL: http://localhost:8000/login');
        
        return 0;
    }
}
