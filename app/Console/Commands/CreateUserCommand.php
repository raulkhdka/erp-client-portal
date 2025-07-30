<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user
                            {--name= : Name of the user}
                            {--email= : Email address}
                            {--password= : Password for the user}
                            {--role=admin : Role (admin, client, employee)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user (admin, client, or employee)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?? $this->ask('Name');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password') ?? $this->ask('Password'); //$password = $this->option('password') ?? $this->secret('Password');
        $role = $this->option('role') ?? $this->choice('Role', ['admin', 'client', 'employee'], 0);

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('A user with that email already exists.');
            return 1;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => $role,
        ]);

        $this->info("✅ User created successfully with ID: {$user->id}");

        return 0;

    }
}
