<?php

namespace Motomedialab\Stub\Actions;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CopyStubs
{
    public function __invoke()
    {
        $stubPath = __DIR__ . '/../../stubs';

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stubPath), RecursiveIteratorIterator::CHILD_FIRST);

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && pathinfo($file->getPathName(), PATHINFO_EXTENSION) === 'stub') {
                $path = str_replace($stubPath, '', $file->getPathname());

                copy($file->getPathname(), base_path(str_replace('.stub', '', $path)));
            }
        }
    }
}
