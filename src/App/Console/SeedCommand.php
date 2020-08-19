<?php

namespace InWeb\Admin\App\Console;

use Illuminate\Console\Command;
use InWeb\Admin\Database\Seeds\DatabaseSeeder;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed all of the Admin resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        (new DatabaseSeeder())->setCommand($this)->run();
    }
}