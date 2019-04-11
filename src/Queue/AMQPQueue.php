<?php
namespace Saiks24\Queue;

use Saiks24\Command\CommandInterface;

class AMQPQueue
{
    /** @var \AMQPQueue */
    private $queue;

    /** @var \AMQPExchange */
    private $exchange;

    public function addTaskToQueue(CommandInterface $command) : void
    {
        $connection = $this->connect();
        $channel = new \AMQPChannel($connection);
        $this->exchange = $this->instanceExchange($channel);
        $this->queue = $this->instanceQueue($channel);
        $this->exchange->publish(
          serialize($command),
          'task.queue',
          AMQP_NOPARAM,
          [
            'delivery_mode' => 2
          ]
        );
    }

    private function instanceQueue(\AMQPChannel $channel) : \AMQPQueue
    {
        $queue = new \AMQPQueue($channel);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setName('task_queue');
        $queue->declareQueue();
        $queue->bind('task_exchange','task.queue');
        return $queue;
    }

    private function instanceExchange(\AMQPChannel $channel) : \AMQPExchange
    {
        $exchange = new \AMQPExchange($channel);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->setName('task_exchange');
        $exchange->declareExchange();
        return $exchange;
    }

    private function connect() : \AMQPConnection
    {
        $cnn = new \AMQPConnection([
          'host'  => '0.0.0.0',
          'port'  => 5672,
          'vhost' => '/',
          'login' => 'guest',
          'password' => 'guest'
        ]);
        $cnn->pconnect();
        return $cnn;
    }

    /**
     * @return \AMQPQueue
     */
    public function getQueue(): \AMQPQueue
    {
        return $this->queue;
    }

    /**
     * @return \AMQPExchange
     */
    public function getExchange(): \AMQPExchange
    {
        return $this->exchange;
    }
}