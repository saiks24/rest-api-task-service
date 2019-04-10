<?php
use \PHPUnit\Framework\TestCase;

class RedisTaskStorageTest extends TestCase
{
    public function testThatTryToConnectRedis()
    {
        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->expects(self::once())->method('pconnect')->willReturn(true);

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
    }

    public function testAddMethodCallRedisHmSetMethod()
    {
        $commandMock = self::getMockBuilder(\Saiks24\Command\TestCommand::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->method('getId')->willReturn('1');
        $commandMock->method('getStatus')->willReturn('success');

        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $redisMock->expects(self::once())->method('hMset')->willReturn(true);

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);

        $taskStorage->add($commandMock);
    }

    public function testAddMethodCallCommandGetIdMethod()
    {
        $commandMock = self::getMockBuilder(\Saiks24\Command\TestCommand::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->method('getId')->willReturn('1');
        $commandMock->method('getStatus')->willReturn('success');

        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $commandMock->expects(self::once())->method('getId');

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
        $taskStorage->add($commandMock);
    }

    public function testAddMethodCallCommandGetStatusMethod()
    {
        $commandMock = self::getMockBuilder(\Saiks24\Command\TestCommand::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->method('getId')->willReturn('1');
        $commandMock->method('getStatus')->willReturn('success');

        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $commandMock->expects(self::once())->method('getStatus');

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
        $taskStorage->add($commandMock);
    }

    public function testGetMethodCallHgetMethodOnReddisConnect()
    {
        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $redisMock->expects(self::once())->method('hGet')->withConsecutive(['tasks:1','command']);

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
        $taskStorage->get('1');
    }

    public function testGetMethodReturnDefaultTaskIfTaskWithThisIdIsUndefined()
    {
        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $serializeTestObject = serialize(new stdClass());
        $redisMock->method('hGet')->willReturn($serializeTestObject);

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
        $defaultTask = $taskStorage->get('1');

        self::assertEquals('undefined',$defaultTask->getStatus());
    }

    public function testDeleteMethodCallDeleteOnRedisConnect()
    {
        $testId = '1234';
        $redisMock = self::getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redisMock->method('pconnect')->willReturn(true);
        $redisMock->expects(self::once())->method('delete')->withConsecutive(['tasks:'.$testId]);

        $taskStorage = new \Saiks24\Storage\RedisTaskStorage($redisMock);
        $taskStorage->delete($testId);
    }
}