<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ClientPhone;
use App\Models\ClientEmail;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@erp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create Sample Employee
        $employeeUser = User::create([
            'name' => 'John Employee',
            'email' => 'employee@erp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_EMPLOYEE,
        ]);

        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_id' => 'EMP001',
            'department' => 'Accounting',
            'position' => 'Senior Accountant',
            'hire_date' => now()->subYears(2),
            'salary' => 60000.00,
            'status' => 'active',
            'permissions' => ['view_accounting', 'edit_payroll', 'view_documents'],
        ]);

        // Create Sample Client
        $clientUser = User::create([
            'name' => 'Jane Client',
            'email' => 'client@erp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        $client = Client::create([
            'user_id' => $clientUser->id,
            'company_name' => 'ABC Corporation',
            'address' => '123 Business St, City, State 12345',
            'tax_id' => 'TAX123456789',
            'business_license' => 'BL987654321',
            'status' => 'active',
            'services' => ['accounting', 'payroll', 'tax_preparation'],
            'notes' => 'Important client with multiple services.',
        ]);

        // Add phone numbers for client
        ClientPhone::create([
            'client_id' => $client->id,
            'phone' => '+1-555-123-4567',
            'type' => 'office',
            'is_primary' => true,
        ]);

        ClientPhone::create([
            'client_id' => $client->id,
            'phone' => '+1-555-987-6543',
            'type' => 'mobile',
            'is_primary' => false,
        ]);

        // Add additional emails for client
        ClientEmail::create([
            'client_id' => $client->id,
            'email' => 'billing@abccorp.com',
            'type' => 'billing',
            'is_primary' => false,
        ]);

        ClientEmail::create([
            'client_id' => $client->id,
            'email' => 'support@abccorp.com',
            'type' => 'support',
            'is_primary' => false,
        ]);

        // Create another sample client
        $clientUser2 = User::create([
            'name' => 'Mike Business',
            'email' => 'mike@xyzltd.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        $client2 = Client::create([
            'user_id' => $clientUser2->id,
            'company_name' => 'XYZ Ltd',
            'address' => '456 Commerce Ave, Business City, BC 54321',
            'tax_id' => 'TAX987654321',
            'business_license' => 'BL123456789',
            'status' => 'active',
            'services' => ['payroll', 'hr_consulting'],
            'notes' => 'Monthly payroll processing client.',
        ]);

        ClientPhone::create([
            'client_id' => $client2->id,
            'phone' => '+1-555-555-1234',
            'type' => 'office',
            'is_primary' => true,
        ]);

        echo "Sample users created:\n";
        echo "Admin: admin@erp.com / password\n";
        echo "Employee: employee@erp.com / password\n";
        echo "Client 1: client@erp.com / password\n";
        echo "Client 2: mike@xyzltd.com / password\n";
    }
}
