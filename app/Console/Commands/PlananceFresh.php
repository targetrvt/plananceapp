<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PlananceFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planance:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables, re-run all migrations, and seed the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete all data in the database. Are you sure?')) {
            $this->info('Operation cancelled.');
            return Command::FAILURE;
        }

        $this->info('Refreshing database...');

        // Run migrations fresh (drops all tables and re-runs migrations)
        $this->info('Running migrations...');
        Artisan::call('migrate:fresh', [], $this->getOutput());

        // Run seeders
        $this->info('Seeding database...');
        Artisan::call('db:seed', [], $this->getOutput());

        $this->info('Database refreshed successfully!');

        return Command::SUCCESS;
    }
}
