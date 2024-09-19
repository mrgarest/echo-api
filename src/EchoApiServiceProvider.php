<?php

namespace MrGarest\EchoApi;

use Illuminate\Support\ServiceProvider;

class EchoApiServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__  . '/../config/echo-api.php', 'echo-api');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
    }
    
    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    private function configurePublishing()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/echo-api.php' => config_path('echo-api.php'),
        ], 'echo-api-config');
    }
}
