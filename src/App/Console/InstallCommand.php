<?php

namespace InWeb\Admin\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use ResolvesStubPath;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Admin resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Admin Assets / Resources...');
        $this->callSilent('admin:publish');

        $this->comment('Publishing Admin Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'admin-provider']);

        $this->comment('Publishing Translatable Config...');
        $this->callSilent('vendor:publish', ['--tag' => 'translatable']);

        $this->createAdminDirectory();

        $this->comment('Registering Admin Service Provider...');
        $this->registerAdminServiceProvider();

        $this->comment('Registering Admin Auth Settings...');
        $this->registerAuthSettings();

        $this->call('config:clear');

        if ($this->confirm('Seed admin with data? (user, permissions)', true)) {
            $this->comment('Seeding Admin Data...');
            $this->callSilent('admin:seed');
        }

        $this->setAppNamespace();

        $this->info('Admin scaffolding installed successfully.');
    }

    /**
     * Register the Admin service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerAdminServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class," . PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class," . PHP_EOL . "        {$namespace}\Providers\AdminServiceProvider::class," . PHP_EOL,
            file_get_contents(config_path('app.php'))
        ));
    }

    protected function registerAuthSettings()
    {
        $content = file_get_contents(config_path('auth.php'));

        $content = str_replace(
            "'guards' => [" . PHP_EOL,
            "'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin',
        ]," . PHP_EOL . PHP_EOL,
            $content
        );

        $content = str_replace(
            "'providers' => [" . PHP_EOL,
            "'providers' => [
        'admin' => [
             'driver' => 'eloquent',
             'model' => \InWeb\Admin\App\Models\AdminUser::class,
         ]," . PHP_EOL . PHP_EOL,
            $content
        );

        file_put_contents(config_path('auth.php'), $content);
    }

    /**
     * Set the proper application namespace on the installed files.
     *
     * @return void
     */
    protected function setAppNamespace()
    {
        $namespace = $this->laravel->getNamespace();

        $this->setAppNamespaceOn(app_path('Providers/AdminServiceProvider.php'), $namespace);
    }

    /**
     * Set the namespace on the given file.
     *
     * @param string $file
     * @param string $namespace
     * @return void
     */
    protected function setAppNamespaceOn($file, $namespace)
    {
        file_put_contents($file, str_replace(
            'App\\',
            $namespace,
            file_get_contents($file)
        ));
    }

    private function createAdminDirectory()
    {
        @\File::makeDirectory(app_path('Admin'));
        @\File::makeDirectory(app_path('Admin/Resources'));
        @\File::makeDirectory(app_path('Admin/Filters'));
        @\File::makeDirectory(app_path('Admin/Actions'));
    }
}
