<?php

namespace Motomedialab\Stub\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Motomedialab\Stub\Actions\CopyStubs;
use Motomedialab\Stub\Concerns\DockerService;
use Motomedialab\Stub\Services\Mailpit;
use Motomedialab\Stub\Services\Mariadb;
use Motomedialab\Stub\Services\Nginx;
use Motomedialab\Stub\Services\Pest;
use Motomedialab\Stub\Services\PhpFpm;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

class StubProjectCommand extends Command
{
    protected $name = 'motomedialab:stub';

    protected $description = 'Stub a project according to MotoMediaLab development standards';

    private array $defaults = [
        Nginx::class,
        PhpFpm::class,
        Mariadb::class,
        Mailpit::class,
        Pest::class,
    ];


    private ?string $domain = null;

    private ?string $envFileData = null;

    public function handle(): void
    {
        // set our envFile contents
        $this->envFileData = file_get_contents(__DIR__ . './../../stubs/.env');

        $this->setAppName();

        $this->setAppDomain();

        $this->publishStubs();

        $chosenServices = $this->chooseServices();

        $this->configureServices($chosenServices);

        $this->moveComposeFile();

        $this->buildEnvironmentFile($chosenServices);

        $this->finishUp();
    }

    private function setAppName(): void
    {
        $this->setEnvVariable(
            'APP_NAME',
            text('Give this project a name', 'My project name', required: true)
        );
    }

    private function setAppDomain(): void
    {
        $domain = text('Choose a name for your local domain', 'example.test', required: true);

        $this->domain = Str::of($domain)->replaceLast('.test', '')->slug()->append('.test');
        $this->setEnvVariable('APP_URL', 'https://' . $this->domain);

        \Laravel\Prompts\info('Will use domain ' . $this->domain);
    }

    private function publishStubs(): void
    {
        $this->info('Publishing stub files');

        App::call(CopyStubs::class);
    }


    /**
     * @return Collection<DockerService>
     */
    private function chooseServices(): Collection
    {
        $services = [];
        $iterator = new \DirectoryIterator(__DIR__ . '/../Services');

        /** @var \DirectoryIterator $file */
        foreach ($iterator as $file) {
            if ($iterator->isDir() || $iterator->isDot()) {
                continue;
            }

            $services[] = new (
                'Motomedialab\Stub\Services\\'
                . substr($file->getBasename(), 0, strrpos($file->getBasename(), '.'))
            );
        }

        $options = collect($services)
            ->sortBy('order', descending: false)
            ->mapWithKeys(fn ($o) => [get_class($o) => $o->name . ' (' . $o->description . ')']);

        $services = multiselect(
            label: 'What services do you require?',
            options: $options,
            default: $this->defaults,
            scroll: 10,
            required: true
        );

        return collect($services)
            ->map(fn ($service) => (new $service())
                ->setVariable('DOMAIN', $this->domain));
    }

    /**
     * @param Collection<DockerService> $services
     */
    private function configureServices(Collection $services): void
    {
        // setup our docker-compose file
        $this->setupComposeFile();

        $services->each(function (DockerService $service) {
            $this->line('Configuring ' . $service->name . '...');

            // build our services with docker-compose.
            $service->build($this->tmpComposeFile());

            // setup any dependencies
            $service->setupDependencies();
        });
    }

    /**
     * @param Collection<DockerService> $services
     */
    private function buildEnvironmentFile(Collection $services): void
    {
        $this->info('Configuring environment variables...');

        $services->each(fn (DockerService $service) => $this->appendToEnv($service->getEnvVariables()));

        file_put_contents(base_path('.env'), $this->envFileData);
    }

    private function finishUp()
    {
        Artisan::call('key:generate');

        $this->info('All done!');
    }

    private function setEnvVariable(string $variable, string $value): void
    {
        $lines = explode("\n", $this->envFileData);

        $replacement = array_map(
            fn ($line) => str_starts_with($line, $variable)
            ? sprintf('%s="%s"', $variable, $value)
            : $line,
            $lines
        );

        $this->envFileData = implode("\n", $replacement);
    }

    private function appendToEnv(?string $value): void
    {
        if ($value) {
            $this->envFileData .= "\n\n" . $value;
        }
    }

    private function setupComposeFile(): void
    {
        $tmpFile = $this->tmpComposeFile();

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        file_put_contents($tmpFile, "version: \"3.8\"\n\nservices:\n\n");
    }

    private function moveComposeFile(): void
    {
        rename($this->tmpComposeFile(), base_path('docker-compose.yaml'));
    }

    private function tmpComposeFile(): string
    {
        return base_path('.docker-compose.yaml.tmp');
    }

}
