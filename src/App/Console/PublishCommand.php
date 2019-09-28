<?php

namespace InWeb\Admin\App\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:publish {--force : Overwrite any existing files} {--views : Publish views files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the Admin resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'admin-config',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'admin-assets',
            '--force' => true,
        ]);

        if ($this->option('views')) {
            $this->call('vendor:publish', [
                '--tag'   => 'admin-views',
                '--force' => $this->option('force'),
            ]);
        }

        $this->call('view:clear');
    }
}