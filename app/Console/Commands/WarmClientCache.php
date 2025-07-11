<?php

namespace App\Console\Commands;

use App\Services\ClientCacheService;
use Illuminate\Console\Command;

class WarmClientCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up the client cache for select dropdowns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Warming up client cache...');

        $clients = ClientCacheService::refreshCache();
        $count = count($clients);

        $this->info("✅ Client cache warmed successfully with {$count} clients.");

        return Command::SUCCESS;
    }
}
