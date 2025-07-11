<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CallLog;
use App\Models\Task;
use App\Models\Client;
use App\Models\Employee;
use Carbon\Carbon;

class CallLogTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing clients and employees
        $clients = Client::all();
        $employees = Employee::all();

        if ($clients->isEmpty() || $employees->isEmpty()) {
            $this->command->info('Please create clients and employees first');
            return;
        }

        // Create sample call logs
        $callLogs = [
            [
                'client_id' => $clients->random()->id,
                'employee_id' => $employees->random()->id,
                'caller_name' => 'John Smith',
                'caller_phone' => '+1-555-0123',
                'call_type' => 'incoming',
                'subject' => 'Website update request',
                'description' => 'Client called to request updates to their homepage layout and contact information.',
                'notes' => 'Client mentioned they want to add a new team member section.',
                'priority' => 'medium',
                'status' => CallLog::STATUS_PENDING,
                'call_date' => Carbon::now()->subHours(2),
                'duration_minutes' => 15,
                'follow_up_required' => true,
                'follow_up_date' => Carbon::now()->addDays(3)
            ],
            [
                'client_id' => $clients->random()->id,
                'employee_id' => $employees->random()->id,
                'caller_name' => 'Sarah Johnson',
                'caller_phone' => '+1-555-0456',
                'call_type' => 'incoming',
                'subject' => 'Technical support for email setup',
                'description' => 'Client experiencing issues with email configuration on their new domain.',
                'notes' => 'Helped with DNS records setup.',
                'priority' => 'high',
                'status' => CallLog::STATUS_RESOLVED,
                'call_date' => Carbon::now()->subHours(4),
                'duration_minutes' => 25,
                'follow_up_required' => false,
            ],
            [
                'client_id' => $clients->random()->id,
                'employee_id' => $employees->random()->id,
                'caller_name' => 'Mike Wilson',
                'caller_phone' => '+1-555-0789',
                'call_type' => 'outgoing',
                'subject' => 'Follow-up on project proposal',
                'description' => 'Called to discuss the new e-commerce project proposal and timeline.',
                'notes' => 'Client interested in additional features.',
                'priority' => 'low',
                'status' => CallLog::STATUS_IN_PROGRESS,
                'call_date' => Carbon::now()->subDay(),
                'duration_minutes' => 20,
                'follow_up_required' => true,
                'follow_up_date' => Carbon::now()->addWeek()
            ],
            [
                'client_id' => $clients->random()->id,
                'employee_id' => $employees->random()->id,
                'caller_name' => 'Lisa Brown',
                'caller_phone' => '+1-555-0321',
                'call_type' => 'incoming',
                'subject' => 'Urgent server issue',
                'description' => 'Client reported their website is down and needs immediate attention.',
                'notes' => 'Escalated to technical team.',
                'priority' => 'high',
                'status' => CallLog::STATUS_ESCALATED,
                'call_date' => Carbon::now()->subMinutes(30),
                'duration_minutes' => 8,
                'follow_up_required' => true,
                'follow_up_date' => Carbon::now()->addHours(2)
            ]
        ];

        foreach ($callLogs as $logData) {
            $callLog = CallLog::create($logData);

            // Create a task for each call log
            $task = Task::create([
                'call_log_id' => $callLog->id,
                'client_id' => $callLog->client_id,
                'assigned_to' => $employees->random()->id,
                'created_by' => $callLog->employee_id,
                'title' => 'Task: ' . $callLog->subject,
                'description' => 'Auto-generated task from call log: ' . $callLog->description,
                'priority' => $callLog->priority,
                'status' => $callLog->status,
                'due_date' => $callLog->follow_up_date ?? Carbon::now()->addDays(7),
                'estimated_hours' => rand(1, 8),
                'notes' => 'Auto-created from call log #' . $callLog->id
            ]);

            if ($callLog->status >= CallLog::STATUS_IN_PROGRESS) {
                $task->update(['started_at' => Carbon::now()->subHours(rand(1, 24))]);
            }

            if ($callLog->status >= CallLog::STATUS_RESOLVED) {
                $task->update([
                    'completed_at' => Carbon::now()->subHours(rand(1, 12)),
                    'actual_hours' => rand(1, 6)
                ]);
            }
        }

        // Create some standalone tasks (not from call logs)
        $standaloneTasks = [
            [
                'client_id' => $clients->random()->id,
                'assigned_to' => $employees->random()->id,
                'created_by' => $employees->random()->id,
                'title' => 'Monthly website backup',
                'description' => 'Perform monthly backup of client website and database.',
                'priority' => 'low',
                'status' => Task::STATUS_PENDING,
                'due_date' => Carbon::now()->addDays(5),
                'estimated_hours' => 2,
                'notes' => 'Scheduled maintenance task'
            ],
            [
                'client_id' => $clients->random()->id,
                'assigned_to' => $employees->random()->id,
                'created_by' => $employees->random()->id,
                'title' => 'Security audit',
                'description' => 'Conduct security audit for client\'s web application.',
                'priority' => 'high',
                'status' => Task::STATUS_IN_PROGRESS,
                'due_date' => Carbon::now()->addDays(2),
                'started_at' => Carbon::now()->subDays(1),
                'estimated_hours' => 8,
                'notes' => 'Annual security review'
            ],
            [
                'client_id' => $clients->random()->id,
                'assigned_to' => $employees->random()->id,
                'created_by' => $employees->random()->id,
                'title' => 'Database optimization',
                'description' => 'Optimize database queries and improve performance.',
                'priority' => 'medium',
                'status' => Task::STATUS_COMPLETED,
                'due_date' => Carbon::now()->subDays(1),
                'started_at' => Carbon::now()->subDays(3),
                'completed_at' => Carbon::now()->subHours(6),
                'estimated_hours' => 4,
                'actual_hours' => 5,
                'notes' => 'Performance improvements implemented'
            ]
        ];

        foreach ($standaloneTasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('Sample call logs and tasks created successfully!');
    }
}
