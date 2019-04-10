<?php

namespace Saiks24\Storage;

use Saiks24\App\Config;

class StorageFactory
{
    public static function getStorage(Config $config) : StorageInterface
    {
        $currentTaskStorageClass = $config->configGetValue('taskStorage');
        $storage = new $currentTaskStorageClass['class']($currentTaskStorageClass['args']);
        return $storage;
    }
}