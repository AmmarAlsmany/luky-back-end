<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangeAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:change-password {email?} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change admin user password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Admin Password Change Tool');
        $this->newLine();

        // Get email (from argument or ask)
        $email = $this->argument('email') ?? $this->ask('Enter admin email', 'admin@luky.sa');

        // Find user
        $user = User::where('email', $email)
            ->where('user_type', 'admin')
            ->first();

        if (!$user) {
            $this->error("❌ Admin user with email '{$email}' not found!");
            return 1;
        }

        $this->info("Found user: {$user->name} ({$user->email})");
        $this->newLine();

        // Get password (from option or ask)
        $password = $this->option('password');
        
        if (!$password) {
            $password = $this->secret('Enter new password (min 8 characters)');
            $passwordConfirm = $this->secret('Confirm new password');

            if ($password !== $passwordConfirm) {
                $this->error('❌ Passwords do not match!');
                return 1;
            }
        }

        // Validate password
        $validator = Validator::make(['password' => $password], [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Password must be at least 8 characters long!');
            return 1;
        }

        // Confirm change
        if (!$this->confirm("Change password for {$user->name}?", true)) {
            $this->warn('Operation cancelled.');
            return 0;
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        $this->newLine();
        $this->info('✅ Password changed successfully!');
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  New Login Credentials:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("  Email:    {$user->email}");
        $this->line("  Password: (the one you just set)");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        return 0;
    }
}
