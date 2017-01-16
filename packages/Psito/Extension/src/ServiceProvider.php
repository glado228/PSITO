<?php

namespace Psito\Extension;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Psito\Extension\Addons\Addon;
use Psito\Extension\Addons\AddonDirectory;
use Psito\Extension\Addons\AddonClassLoader;
use Psito\Extension\Addons\AddonGenerator;
use Psito\Extension\Repository;
use Psito\Extension\Templates\BladeExtension;
use Jumilla\Versionia\Laravel\Migrator;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var array
     */
    protected static $commands = [
// app:
        'command+.app.container' => Console\AppContainerCommand::class,
        'command+.app.route' => Console\RouteListCommand::class,
        'command+.app.tail' => Console\TailCommand::class,
// addon:
        'command+.addon.status' => Addons\Console\AddonStatusCommand::class,
        'command+.addon.make' => Addons\Console\AddonMakeCommand::class,
        'command+.addon.name' => Addons\Console\AddonNameCommand::class,
        'command+.addon.remove' => Addons\Console\AddonRemoveCommand::class,
        'command+.addon.check' => Addons\Console\AddonCheckCommand::class,
// database:
        'command+.database.status' => Database\Console\DatabaseStatusCommand::class,
        'command+.database.upgrade' => Database\Console\DatabaseUpgradeCommand::class,
        'command+.database.clean' => Database\Console\DatabaseCleanCommand::class,
        'command+.database.refresh' => Database\Console\DatabaseRefreshCommand::class,
        'command+.database.rollback' => Database\Console\DatabaseRollbackCommand::class,
        'command+.database.again' => Database\Console\DatabaseAgainCommand::class,
        'command+.database.seed' => Database\Console\DatabaseSeedCommand::class,
// hash:
        'command+.hash.make' => Console\HashMakeCommand::class,
        'command+.hash.check' => Console\HashCheckCommand::class,
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var array
     */
    protected $addons;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // register spec path for app
        $app['path.specs'] = $app->basePath().'/app/resources/specs';

        // register spec repository
        $app->singleton('specs', function ($app) {
            $loader = new Repository\FileLoader($app['files'], $app['path.specs']);

            return new Repository\NamespacedRepository($loader);
        });

        // register addon generator
        $app->singleton('addons.generator', function ($app) {
            return new AddonGenerator;
        });
        $app->alias('addons.generator', AddonGenerator::class);

        // register database migrator
        $app->singleton('database.migrator', function ($app) {
            return new Migrator($app['db']);
        });
        $app->alias('database.migrator', Migrator::class);

        $this->registerClassResolvers();

        // register all addons
        $this->registerAddons();
    }

    /**
     * @return void
     */
    protected function registerClassResolvers()
    {
        AddonClassLoader::register(Application::getAddons());

        AliasResolver::register(Application::getAddons(), $this->app['config']->get('app.aliases'));
    }

    /**
     * @return void
     */
    protected function registerAddons()
    {
        foreach (Application::getAddons() as $addon) {
            // register addon
            $addon->register($this->app);
        }
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Add package commands
        $this->setupCommands(static::$commands);

        //
        $this->registerBladeExtensions();

        // setup all addons
        $this->bootAddons();
    }

    /**
     * setup package's commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function setupCommands($commands)
    {
        foreach ($commands as $name => $class) {
            $this->app->singleton($name, function ($app) use ($class) {
                return new $class($app);
            });
        }

        // Now register all the commands
        $this->commands(array_keys(static::$commands));
    }

    /**
     * register blade extensions.
     *
     * @return void
     */
    protected function registerBladeExtensions()
    {
        Blade::extend(BladeExtension::comment());

        Blade::extend(BladeExtension::script());
    }

    /**
     * setup & boot addons.
     *
     * @return void
     */
    protected function bootAddons()
    {
        foreach (Application::getAddons() as $name => $addon) {
            // register package
            $this->registerPackage($name, $addon);

            // boot addon
            $addon->boot($this->app);
        }
    }

    /**
     * Register the package's component namespaces.
     *
     * @param  string  $namespace
     * @param  \Psito\Extension\Addons\Addon  $addon
     * @return void
     */
    protected function registerPackage($namespace, $addon)
    {
        $lang = $addon->path($addon->config('addon.paths.lang', 'lang'));
        if (is_dir($lang)) {
            $this->app['translator']->addNamespace($namespace, $lang);
        }

        $view = $addon->path($addon->config('addon.paths.views', 'views'));
        if (is_dir($view)) {
            $this->app['view']->addNamespace($namespace, $view);
        }

        $spec = $addon->path($addon->config('addon.paths.specs', 'specs'));
        if (is_dir($spec)) {
            $this->app['specs']->addNamespace($namespace, $spec);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys(static::$commands);
    }
}
