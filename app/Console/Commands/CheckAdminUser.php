<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckAdminUser extends Command
{
    protected $signature = 'check:admin';
    protected $description = 'Check admin user in database';

    public function handle()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        if (!$admin) {
            $this->error('Admin user not found!');
            return;
        }

        $this->info('Admin user found:');
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Is Approved'],
            [[$admin->id, $admin->name, $admin->email, $admin->role, $admin->is_approved ? 'Yes' : 'No']]
        );
    }
} 