<?php

namespace Error;

/**
 * This file is part of Error,
 * an API error management solution for Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Error
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
        'Config' => 'command.error.config',
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
        $app_path = app()->basePath('config/prionerror.php');
        $this->publishes([
            __DIR__.'/config/prionerror.php' => $app_path,
        ], 'prionerror');

        // Register Translations
        $trans_path = __DIR__.'/resources/lang';
        $this->loadTranslationsFrom($trans_path, 'error');
        $this->publishes([
            $trans_path => resource_path('lang/vendor/error'),
        ]);
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->mergeConfig();
    }


    /**
     * Merges Config
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->app->configure('prionerror');
        $this->mergeConfigFrom(
            __DIR__ . '/config/prionerror.php',
            'prionerror'
        );
    }


    /**
     * Register the given commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }


    /**
     * Bind the Error Config Setup Command
     *
     */
    protected function registerConfigCommand()
    {
        $command = $this->commands['Config'];
        $this->app->singleton($command, function () {
            return new \Error\Commands\ConfigCommand;
        });
    }


}