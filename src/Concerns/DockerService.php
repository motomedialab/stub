<?php

namespace Motomedialab\Stub\Concerns;

abstract class DockerService
{
    public int $order = 0;
    public string $name;
    public string $description;
    public ?string $composeStub = null;
    public array $variables = [];
    public ?string $dockerStubDir = null;
    public ?string $config = null;

    public ?array $requires = [];

    public function __construct(array $variables = [])
    {
        $this->variables = [
            ...$this->variables,
            ...$variables
        ];
    }

    public function getVariables(): array
    {
        return [
            'NAME' => $this->name,
            'DESCRIPTION' => $this->description,
            ...$this->variables
        ];
    }

    public function getVariable(string $variable, mixed $default = null)
    {
        return $this->getVariables()[$variable] ?? $default;
    }

    public function setVariable(string $variable, $value): static
    {
        $this->variables[$variable] = $value;

        return $this;
    }

    public function files(): array
    {
        return [];
    }

    public function evaluate(string $contents): string
    {
        foreach ($this->getVariables() as $variable => $value) {
            $contents = str_replace('{{' . $variable . '}}', $value, $contents);
        }

        return $contents;
    }

    public function build(string $dockerComposeFile): void
    {
        if ($this->dockerStubDir) {
            $this->cloneDockerFiles();
        }

        if ($this->composeStub) {
            file_put_contents($dockerComposeFile, $this->evaluate($this->composeStub) . "\n\n", FILE_APPEND);
        }

        // create generated files
        if (($files = $this->files()) && count($files) !== 0) {
            foreach ($files as $path => $file) {

                $path = base_path($path);

                if (!file_exists(dirname($path))) {
                    mkdir($path, recursive: true);
                }

                file_put_contents($path, $this->evaluate($file));
            }
        }
    }

    public function setupDependencies(): void
    {
        foreach ($this->requires as $require) {
            exec('composer require ' . $require);
        }
    }

    public function getEnvVariables(): ?string
    {
        return $this->config ? $this->evaluate($this->config) : null;
    }


    protected function cloneDockerFiles(): void
    {
        $basePath = base_path('docker/' . $this->dockerStubDir);

        if (!file_exists($basePath)) {
            mkdir($basePath, recursive: true);
        }
        $this->copyDirectory(__DIR__ . '/../../stubs/docker/' . $this->dockerStubDir, $basePath);
    }

    protected function copyDirectory($src, $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }

}
