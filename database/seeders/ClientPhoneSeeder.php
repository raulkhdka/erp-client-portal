<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\ClientPhone;

class ClientPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please create clients first.');
            return;
        }

        foreach ($clients as $client) {
            // Add primary phone number
            ClientPhone::create([
                'client_id' => $client->id,
                'phone' => '+1-555-' . rand(1000, 9999),
                'type' => 'office',
                'is_primary' => true
            ]);

            // Add secondary phone numbers for some clients
            if (rand(0, 1)) {
                ClientPhone::create([
                    'client_id' => $client->id,
                    'phone' => '+1-555-' . rand(1000, 9999),
                    'type' => 'mobile',
                    'is_primary' => false
                ]);
            }

            if (rand(0, 2) === 0) {
                ClientPhone::create([
                    'client_id' => $client->id,
                    'phone' => '+1-555-' . rand(1000, 9999),
                    'type' => 'fax',
                    'is_primary' => false
                ]);
            }
        }

        $this->command->info('Client phone numbers created successfully!');
    }
}
