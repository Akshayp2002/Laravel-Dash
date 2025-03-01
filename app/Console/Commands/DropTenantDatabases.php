<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DropTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:drop-databases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops all tenant databases before migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::all();

        if ($tenants->isNotEmpty()) {
            foreach ($tenants as $tenant) {
                $db_name = config('tenancy.database.prefix') . $tenant->id;
                DB::statement("DROP DATABASE IF EXISTS `$db_name`");
                $this->info("Dropped database: $db_name");
            }
        } else {
            $this->info("No tenant databases found.");
        }
    }
}
