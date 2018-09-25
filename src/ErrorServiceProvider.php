<?php

namespace Error;

/**
 * This file is part of Error,
 * an API error management solution for Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Setting
 */

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ErrorServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * The middlewares to be registered.
     *
     * @var array
     */
    protected $middlewares = [];

    public function __construct($app)
    {
        $this->app = $app;
    }


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register published configuration.
        $app_path = app()->basePath('config/error.php');
        $this->publishes([
            __DIR__.'/config/error.php' => $app_path,
        ], 'error');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }


    /**
     * Merges Config Settings
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/error.php',
            'error'
        );
    }

}