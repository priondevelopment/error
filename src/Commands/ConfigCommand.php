<?php

namespace Error\Commands;

/**
 * This file is part of Seetting,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @company Prion Development
 * @package Error
 */

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class ConfigCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'error:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the configuration files for error.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->laravel->view->addNamespace('error', substr(__DIR__, 0, -8).'views');
        $this->line('');
        $this->info("Prion Error Config Creation.");
        $this->line('');
        $this->comment($this->generateConfigMessage());
        $defaultAnswer = true;

        foreach ($this->configFiles() as $file) {
            $existingConfigs = $this->alreadyExistingConfig($file);

            if ($existingConfigs) {
                $this->line('');

                $this->warn($this->getExistingConfigsWarning($existingConfigs));

                $defaultAnswer = false;
            }
        }

        $this->line('');

        if (! $this->confirm("Proceed with the config creation?", $defaultAnswer)) {
            return;
        }

        $this->line('');

        $this->line("Creating config file/s");

        if ($this->createConfig()) {
            $this->info("Config created successfully.");
        } else {
            $this->error(
                "Couldn't create config.\n".
                "Check the write permissions within the database/configs directory."
            );
        }

        $this->line('');
    }

    /**
     * Create the config.
     *
     * @return bool
     */
    protected function createConfig()
    {
        $files = $this->configFiles();
        foreach ($files as $file) {
            $file = str_replace(".php", "", $file);
            $configPath = $this->getConfigPath($file);

            $output = $this->laravel->view
                ->make('error::' . $file)
                ->render();

            if (!file_exists($configPath) && $fs = fopen($configPath, 'x')) {
                fwrite($fs, $output);
                fclose($fs);
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the message to display when running the
     * console command showing what tables are going
     * to be created.
     *
     * @return string
     */
    protected function generateConfigMessage()
    {
        $configs = $this->configFiles();
        return "A command that creates ". implode(', ', $configs) ." "
            . "config file/s in config directory";
    }

    /**
     * Build a warning regarding possible duplication
     * due to already existing configs.
     *
     * @param  array  $existingConfigs
     * @return string
     */
    protected function getExistingConfigsWarning(array $existingConfig)
    {
        $base = "Prion Error config already exist.\nFollowing file was found: ";

        if (count($existingConfig) > 1) {
            $base = str_replace('file was', 'files were', $base);
        }

        return $base . array_reduce($existingConfig, function ($carry, $fileName) {
                return $carry . "\n - " . $fileName;
            });
    }

    /**
     * Check if there is another config
     * with the same suffix.
     *
     * @return array
     */
    protected function alreadyExistingConfig($file)
    {
        $file = str_replace(".php", "", $file);
        $matchingFiles = glob($this->getConfigPath($file));

        return array_map(function ($path) {
            return basename($path);
        }, $matchingFiles);
    }

    /**
     * Get the config path.
     *
     * The date parameter is optional for ability
     * to provide a custom value or a wildcard.
     *
     * @param  string|null  $date
     * @return string
     */
    protected function getConfigPath($file)
    {
        return app()->basePath("config/${file}.php");
    }


    /**
     * Pull the File Names from the Config Directory
     *
     * @return array
     */
    private function configFiles()
    {
        $configs = array_filter(scandir(__DIR__.'/../config'), function($item) {
            return !is_dir(__DIR__.'/../config/' . $item);
        });

        return $configs;
    }
}
