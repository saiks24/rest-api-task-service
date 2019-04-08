<?php

namespace Saiks24\Storage;


use Saiks24\App\App;

class StorageFactory
{
    public static function getStorage() : StorageInterface
    {
        $currentTaskStorageClass = App::make()
          ->getConfig()
          ->configGetValue('taskStorage');
        return new $currentTaskStorageClass();
    }
}