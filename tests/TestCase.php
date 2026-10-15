<?php

namespace Aura\Blocks\Tests;

use Aura\Base\AuraServiceProvider;
use Aura\Base\Providers\AuthServiceProvider;
use Aura\Blocks\AuraBlocksServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Lab404\Impersonate\ImpersonateServiceProvider;
use Laravel\Fortify\FortifyServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function defineEnvironment($app): void
    {
        $this->useIsolatedFilesystemPaths($app);
        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('b', 32)));
        $app['config']->set('aura.teams', false);
        $app['config']->set('database.default', 'testing');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $this->useIsolatedFilesystemPaths($app);
        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('b', 32)));
        $app['config']->set('aura.teams', false);
        $app['config']->set('database.default', 'testing');

        $migration = require __DIR__.'/../vendor/eminiarts/aura-cms/database/migrations/create_aura_tables.php.stub';
        $migration->up();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FortifyServiceProvider::class,
            AuthServiceProvider::class,
            AuraServiceProvider::class,
            AuraBlocksServiceProvider::class,
            ImpersonateServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function useIsolatedFilesystemPaths($app): void
    {
        $basePath = sys_get_temp_dir().'/aura-blocks-testbench-'.getmypid();

        foreach ([
            'app/Aura/Resources',
            'bootstrap/cache',
            'config',
            'database/migrations',
            'public',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ] as $path) {
            if (! is_dir($basePath.'/'.$path)) {
                @mkdir($basePath.'/'.$path, 0755, true);
            }
        }

        $app->useAppPath($basePath.'/app');
        (function (): void {
            $this->namespace = 'App\\';
        })->call($app);
        $app->useBootstrapPath($basePath.'/bootstrap');
        $app->useConfigPath($basePath.'/config');
        $app->useDatabasePath($basePath.'/database');
        $app->usePublicPath($basePath.'/public');
        $app->useStoragePath($basePath.'/storage');
    }
}
