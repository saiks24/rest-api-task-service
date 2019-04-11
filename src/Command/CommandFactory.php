<?php


namespace Saiks24\Command;


class CommandFactory
{
    public static function createCommand(string $className,$args) : CommandInterface
    {
        return new $className($args);
    }
}