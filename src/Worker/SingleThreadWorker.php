<?php
namespace Saiks24\Worker;


use Saiks24\Command\CommandInterface;
use Saiks24\Storage\RedisTaskStorage;

class SingleThreadWorker implements WorkerInterface
{

    /**
     * @return mixed|void
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function run()
    {
        echo 'Worker Try start on pid: ' . getmypid().PHP_EOL;
        echo 'Init connect to queue service'.PHP_EOL;
        $connection = new \AMQPConnection([
          'host' => '0.0.0.0',
          'port' => 5672,
          'vhost' => '/',
          'login' => 'guest',
          'password' => 'guest'
        ]);
        $connection->pconnect();
        echo 'Init channel and exchange'.PHP_EOL;
        $channel = new \AMQPChannel($connection);
        $exchange = new \AMQPExchange($channel);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setName('task_exchange');
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
        echo 'Init queue'.PHP_EOL;
        $queue = new \AMQPQueue($channel);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setName('task_queue');
        $queue->declareQueue();
        $queue->bind('task_exchange','default.queue');
        $taskStorage = new RedisTaskStorage(new \Redis());
        echo 'Done! Worker waited connections...'.PHP_EOL;
        $queue->consume(function (\AMQPEnvelope $envelope, \AMQPQueue $queue) use($taskStorage) {
            try {
                /** @var \Saiks24\Command\CommandInterface $command */
                $command = unserialize($envelope->getBody());
                if(! ($command instanceof CommandInterface)) {
                    throw new \Exception('Invalid task object');
                }
                $command->execute();
                $taskStorage->add($command);
                $queue->ack($envelope->getDeliveryTag());
            } catch (\Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                $queue->nack($envelope->getDeliveryTag());
            }
        });
    }

    public function stop()
    {
        exit();
    }

    public function getStatus(): array
    {
        return [];
    }

}
