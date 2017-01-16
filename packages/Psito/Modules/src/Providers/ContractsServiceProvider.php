<?php

namespace Psito\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(
            'Psito\Modules\Contracts\RepositoryInterface',
            'Psito\Modules\Repository'
        );
    }
}
