<?php

namespace Motomedialab\Stub\Actions;

class GetDockerStubPath
{
    public function __invoke(string $directory): string
    {
        return __DIR__ . '/../../stubs/docker' . $directory;
    }
}
