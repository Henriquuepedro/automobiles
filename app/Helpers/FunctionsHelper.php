<?php

use Illuminate\Support\Facades\File;

if (! function_exists('makePathDir')) {
    /**
     * Cria as pastas do caminho de um repositório.
     *
     * @param string $paths
     */
    function makePathDir(string $paths)
    {
        $pathCheck = '';
        foreach (explode('/', $paths) as $path) {
            if (!empty($pathCheck)) {
                $pathCheck .= '/';
            }

            $pathCheck .= $path;
            if (!File::exists(public_path($pathCheck))) {
                File::makeDirectory(public_path($pathCheck));
            }
        }
    }
}
