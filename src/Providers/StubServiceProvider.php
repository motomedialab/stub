<?php

namespace Motomedialab\Stub\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Motomedialab\Stub\Console\StubProjectCommand;

class StubServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // register our stub command

        $this
            ->registerCommands()
            ->configureProject();

    }

    private function registerCommands(): StubServiceProvider
    {
        $this->commands([StubProjectCommand::class]);

        return $this;
    }

    private function configureProject(): StubServiceProvider
    {
        // enforce model strictness for our project.
        Model::shouldBeStrict(true);

        return $this;
    }
}
