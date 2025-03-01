<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Run this command before fresh the DB -> php artisan tenants:drop-databases

        // Run Transaction seeder for test data -> php artisan tenants:seed --class=TransactionSeeder


        // Passport Key Generation related code
        $client = new ClientRepository();
        $client->createPasswordGrantClient(null, 'Default password grant client', 'https://google.com');
        $client->createPersonalAccessClient(null, 'Default personal access client', 'https://google.com');


        Tenant::create(['id' => '_live']);
        Tenant::create(['id' => '_test']);
    }
}
