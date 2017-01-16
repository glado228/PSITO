<?php

namespace Psito\Extension\Addons\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Psito\Extension\Console\MakeCommandTrait;
use Psito\Extension\Addons\AddonDirectory;
use Psito\Extension\Addons\AddonGenerator;
use InvalidArgumentException;
use Exception;

/**
 * Modules console commands.
 * @author Fumio Furukawa <fumio.furukawa@gmail.com>
 */
class AddonMakeCommand extends Command
{
    use AddonCommandTrait;
    use MakeCommandTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:addon
        {name : Name of addon.}
        {skeleton? : Skeleton of addon.}
        {--namespace= : PHP namespace of addon. Slash OK.}
        {--no-namespace : No PHP namespace.}
        {--language= : Languages, comma separated.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[+] Make addon';

    /**
     * @var array
     */
    protected $skeletons = [
        1 => 'minimum',
        2 => 'simple',
        3 => 'library',
        4 => 'api',
        5 => 'ui',
        6 => 'debug',
        7 => 'laravel5',
        8 => 'sample:ui',
        9 => 'sample:auth',
    ];

    /**
     * @var string
     */
    protected $default_skeleton = 'sample:ui';

    /**
     * Execute the console command.
     *
     * @param \Psito\Extension\Addons\AddonGenerator $generator
     * @return mixed
     */
    public function handle(Filesystem $filesystem, AddonGenerator $generator)
    {
        $addon_name = preg_replace('#(/+)#', '-', $this->argument('name'));

        $output_path = AddonDirectory::path($addon_name);

        if ($filesystem->exists($output_path)) {
            throw new InvalidArgumentException("addon directory '{$addon_name}' is already exists.");
        }

        $skeleton = $this->chooseSkeleton($this->argument('skeleton'));
        if ($this->option('no-namespace')) {
            $namespace = '';
        } else {
            if ($this->option('namespace')) {
                $namespace = str_replace('/', '\\', $this->option('namespace'));
                $namespace = studly_case(preg_replace('/[^\w_\\\\]/', '', $namespace));
            } else {
                $namespace = studly_case(preg_replace('/[^\w_]/', '', $addon_name));
            }

            $namespace = preg_replace('/^(\d)/', '_$1', $namespace);
        }
        $languages = $this->option('language') ? explode($this->option('language')) : [];

        $properties = [
            'addon_name' => preg_replace('/[^\w_]/', '', $addon_name),
            'addon_class' => preg_replace(
                ['/[^\w_]/', '/^(\d)/'],
                ['', '_$1'],
                studly_case($addon_name)
            ),
            'namespace' => $this->option('no-namespace') ? '' : $namespace,
            'languages' => array_unique(array_merge(['en', config('app.locale')], $languages)),
        ];

        try {
            $generator->generateAddon($output_path, str_replace(':', '-', $skeleton), $properties);
            $this->info('Addon Generated.');
        } catch (Exception $ex) {
            $filesystem->deleteDirectory($output_path);

            throw $ex;
        }
    }
}
