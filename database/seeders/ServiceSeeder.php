<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Accounting',
                'detail' => 'Complete accounting services including bookkeeping, financial statements, and financial reporting',
                'type' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Payroll',
                'detail' => 'Payroll processing, tax calculations, and employee payment management',
                'type' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Tax Preparation',
                'detail' => 'Individual and business tax preparation and filing services',
                'type' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Bookkeeping',
                'detail' => 'Daily transaction recording, account reconciliation, and financial record maintenance',
                'type' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'HR Consulting',
                'detail' => 'Human resources consulting, policy development, and compliance guidance',
                'type' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Financial Planning',
                'detail' => 'Strategic financial planning, budgeting, and investment advisory services',
                'type' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
