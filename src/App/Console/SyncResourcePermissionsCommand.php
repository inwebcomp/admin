<?php

namespace InWeb\Admin\App\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Events\ServingAdmin;
use InWeb\Admin\App\HasPermissions;

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

        if ($resource) {
            $resources = [$resource];
        } else {
            event(new ServingAdmin(new Request()));

            $resources = array_merge(
                Admin::$resources,
                Admin::$tools
            );
        }

        $resources = array_filter($resources, function ($section) {
            return in_array(HasPermissions::class, class_uses_recursive($section));
        });

        foreach ($resources as $resource) {
            $resource::syncPermissionActions();
        }

        $this->info("Permissions synchronized");
    }
}
