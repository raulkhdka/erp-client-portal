<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Contracts',
                'slug' => 'contracts',
                'description' => 'Legal contracts and agreements',
                'icon' => 'fas fa-file-contract',
                'color' => '#28a745',
                'sort_order' => 1,
            ],
            [
                'name' => 'Invoices',
                'slug' => 'invoices',
                'description' => 'Invoice documents and billing records',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => '#ffc107',
                'sort_order' => 2,
            ],
            [
                'name' => 'Tax Documents',
                'slug' => 'tax-documents',
                'description' => 'Tax forms and related documents',
                'icon' => 'fas fa-calculator',
                'color' => '#dc3545',
                'sort_order' => 3,
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'description' => 'Business reports and analytics',
                'icon' => 'fas fa-chart-bar',
                'color' => '#007bff',
                'sort_order' => 4,
            ],
            [
                'name' => 'Presentations',
                'slug' => 'presentations',
                'description' => 'Meeting presentations and slides',
                'icon' => 'fas fa-presentation',
                'color' => '#6f42c1',
                'sort_order' => 5,
            ],
            [
                'name' => 'Proposals',
                'slug' => 'proposals',
                'description' => 'Project proposals and quotes',
                'icon' => 'fas fa-handshake',
                'color' => '#20c997',
                'sort_order' => 6,
            ],
            [
                'name' => 'Legal Documents',
                'slug' => 'legal-documents',
                'description' => 'Legal papers and court documents',
                'icon' => 'fas fa-gavel',
                'color' => '#fd7e14',
                'sort_order' => 7,
            ],
            [
                'name' => 'Employee Records',
                'slug' => 'employee-records',
                'description' => 'HR documents and employee files',
                'icon' => 'fas fa-user-tie',
                'color' => '#6c757d',
                'sort_order' => 8,
            ],
            [
                'name' => 'Client Files',
                'slug' => 'client-files',
                'description' => 'Client-specific documents and files',
                'icon' => 'fas fa-users',
                'color' => '#17a2b8',
                'sort_order' => 9,
            ],
            [
                'name' => 'Marketing Materials',
                'slug' => 'marketing-materials',
                'description' => 'Brochures, flyers, and marketing content',
                'icon' => 'fas fa-bullhorn',
                'color' => '#e83e8c',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            DocumentCategory::create($category);
        }
    }
}
