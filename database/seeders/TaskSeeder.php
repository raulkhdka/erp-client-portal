<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Client;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Get all employees
        $employees = Employee::all();
        $clients = Client::all();
        $adminUser = User::where('role', 'admin')->first();

        if ($employees->isEmpty() || $clients->isEmpty() || !$adminUser) {
            $this->command->error('Please run other seeders first (Employee, Client, User)');
            return;
        }

        foreach ($employees as $employee) {
            // Create 5 tasks for each employee
            for ($i = 0; $i < 10; $i++) {
                Task::create([
                    'client_id' => $clients->random()->id,
                    'assigned_to' => $employee->id,
                    'created_by' => $adminUser->id,
                    'title' => "Test Task #{$i} for " . $employee->name,
                    'description' => "This is a test task for employee: " . $employee->name,
                    'priority' => ['low', 'medium', 'high', 'urgent'][array_rand(['low', 'medium', 'high', 'urgent'])],
                    'status' => rand(1, 8),
                    'due_date' => now()->addDays(rand(1, 30)),
                    'notes' => 'Test task notes',
                ]);
            }
        }
    }
}