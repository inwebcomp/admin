<?php

namespace InWeb\Admin\App\Console;

use Illuminate\Console\Command;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Events\ServingAdmin;

class SyncResourcePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:permissions {resource?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync resource permissions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $resource = $this->argument('resource');

        if ($resource)
            $resources = [$resource];
        else
            $resources = Admin::$resources;

        foreach ($resources as $resource) {
            $resource::syncPermissionActions();
        }

        $this->info("Permissions synchronized");
    }
}