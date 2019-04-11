<?php


class QueueTest extends \PHPUnit\Framework\TestCase
{
    public function testThatQueueInstanceSuccess()
    {
        $commandMock = self::getMockBuilder(\Saiks24\Command\TestCommand::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queue = new \Saiks24\Queue\AMQPQueue();
        $queue->addTaskToQueue($commandMock);

        self::assertNotEmpty($queue->getQueue());
    }

    public function testThatExchangeInstanceSuccess()
    {
        $commandMock = self::getMockBuilder(\Saiks24\Command\TestCommand::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queue = new \Saiks24\Queue\AMQPQueue();
        $queue->addTaskToQueue($commandMock);

        self::assertNotEmpty($queue->getExchange());
    }
}