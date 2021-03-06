<?php

namespace Psito\Foundation;


class Application extends \Illuminate\Foundation\Application {
    /**
     * The array of Beverage config items.
     *
     * @var array
     */
    protected $structure;

    /**
     * Create a new Caffeinated Beverage application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath = null, $configPath = null)
    {
        if ($basePath) $this->setBasePath($basePath);

        $this->structure = $this->loadConfig($configPath);

        parent::__construct($basePath);
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath.'/'.$this->structure['app_path'];
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath.'/'.$this->structure['config_path'];
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->basePath.'/'.$this->structure['database_path'];
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function langPath()
    {
        return $this->basePath.'/'.$this->structure['lang_path'];
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.'/'.$this->structure['public_path'];
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {

        return $this->basePath.'/'.$this->structure['storage_path'];
    }

    /**
     * Manually load our structure config file. We need to do this since this
     * file is loaded before the config service provider is kicked in.
     *
     * @return array
     */
    protected function loadConfig($customConfigPath = null)
    {
        if (is_null($customConfigPath)) {
            $customConfigPath = $this->basePath.'/config';
        }

        $structureConfigPath = realpath(__DIR__.'/../config');
        $structureConfigFile = $structureConfigPath.'/structure.php';
        $customConfigFile   = $customConfigPath.'/structure.php';

        $customConfig   = [];
        $structureConfig = include($structureConfigFile);

        if (file_exists($customConfigFile)) {
            $customConfig = include($customConfigFile);
        }

        $config = array_replace_recursive($structureConfig, $customConfig);

        if ($customConfigPath) {
            $config['config'] = str_replace($this->basePath.'/', '', $customConfigPath);
        } else {
            $config['config'] = 'config';
        }

        return $config;
    }
} 