<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $icons = [
            'fas fa-folder',
            'fas fa-file-alt',
            'fas fa-file-invoice',
            'fas fa-chart-line',
            'fas fa-receipt',
            'fas fa-gavel',
            'fas fa-envelope',
            'fas fa-credit-card',
            'fas fa-piggy-bank',
            'fas fa-university',
            'fas fa-handshake',
            'fas fa-users'
        ];

        for ($i = 11; $i <= 20; $i++) {
            $name = 'Category ' . $i;
            DB::table('document_categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name), // Added this line to generate a slug
                'description' => 'This is a description for document category ' . $i . '.',
                'icon' => $icons[array_rand($icons)],
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                'is_active' => true,
                'sort_order' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}