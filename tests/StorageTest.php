<?php


class StorageTest extends \PHPUnit\Framework\TestCase
{
    public function testThatStorageSuccessFullCreated()
    {
        $storage = new \Saiks24\Storage\RedisTaskStorage(new Redis());
        self::assertEqulas();
    }
}