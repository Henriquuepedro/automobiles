<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;

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

if (! function_exists('getStoreDomain')) {
    /**
     * Recupera o domínio da loja e se é compartilhado ou próprio.
     *
     * @return StdClass
     */
    function getStoreDomain(): StdClass
    {
        $host           = str_replace('www.', '', Request::getHttpHost());
        $expHost        = explode('.', $host);

        $object = new StdClass();

        $object->hostShared     = false;
        $object->nameHostStore  = $host;

        $expHostValidate = $expHost;
        unset($expHostValidate[0]);
        $expHostValidate = array_reverse(array_reverse($expHostValidate));

        // Host compartilhado.
        if (implode('.', $expHostValidate) === env('SHARED_DOMAIN_PUBLIC')) {
            $object->hostShared = true;
            $object->nameHostStore = $expHost[0];
        }

        return $object;
    }
}
