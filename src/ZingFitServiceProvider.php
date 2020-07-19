<?php

namespace CapeAndBay\ZingFit;

use Illuminate\Support\ServiceProvider;

class ZingFitServiceProvider extends ServiceProvider
{
    const VERSION = '0.1.0';
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'capeandbay');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'capeandbay');
        $this->publishMigrations();
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Publish the package's migrations.
     *
     * @return void
     */
    protected function publishMigrations()
    {
        if (class_exists('CreateZingFitTokensTable')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());

        $stub = __DIR__.'/../migrations/create_zing_fit_tokens_table.php';

        $target = $this->app->databasePath().'/migrations/'.$timestamp.'_create_zing_fit_tokens_table.php';

        $this->publishes([$stub => $target], 'zingfit.migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zingfit.php', 'zingfit');

        // Register the service the package provides.
        $this->app->singleton('zingfit', function ($app) {
            return new ZingFit;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['zingfit'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/zingfit.php' => config_path('zingfit.php'),
        ], 'config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/capeandbay'),
        ], 'zingfit.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/capeandbay'),
        ], 'zingfit.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/capeandbay'),
        ], 'zingfit.views');*/

        // Registering package commands.
        $this->commands([
            \CapeAndBay\ZingFit\Console\Commands\RefreshZingFitToken::class
        ]);
    }
}
