<?php
namespace Saiks24\Worker;

use Saiks24\App\Config;
use Saiks24\Command\CommandInterface;
use Saiks24\Storage\RedisTaskStorage;

class SingleThreadWorker implements WorkerInterface
{
    /** @var bool Flag that worker been interrupted*/
    private $isInterrupted;

    /** @var \AMQPQueue Queue in AMQP broker*/
    private $queue;

    /** @var \Saiks24\Storage\StorageInterface  Storage for tasks*/
    private $taskStorage;

    /** Prepare consumer and start working
     * @param string Base application dir
     * @return mixed|void
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \Exception
     */
    public function run(string $baseDir = '')
    {
        echo 'Worker Try start on pid: ' . getmypid().PHP_EOL;
        echo 'Init connect to queue service'.PHP_EOL;
        $config = new Config($baseDir.'/config/config.php');
        $connection = new \AMQPConnection($config->configGetValue('amqp'));
        $connection->pconnect();
        echo 'Init channel and exchange'.PHP_EOL;
        $channel = new \AMQPChannel($connection);
        $this->initExchange($channel);
        $this->queue = $this->initQueue($channel);
        $this->taskStorage = new RedisTaskStorage(new \Redis());
        echo 'Done! Worker waited connections...'.PHP_EOL;
        $this->work();
    }

    /**
     * Start work
     *
     * @return void
     *
     * @throws \Exception
     */
    private function work() : void
    {
        while (true) {
            $messageFromQueue = $this->queue->get();
            if ($messageFromQueue instanceof \AMQPEnvelope) {
                /** @var CommandInterface $command */
                $command = unserialize($messageFromQueue->getBody());
                if ($command instanceof CommandInterface) {
                    $command->execute();
                    $this->queue->ack($messageFromQueue->getDeliveryTag());
                    $this->taskStorage->add($command);
                } else {
                    $this->queue->nack($messageFromQueue->getDeliveryTag());
                }
            }
            if($this->isInterrupted) {
                echo 'Stopped by Interrupt' . PHP_EOL;
                exit();
            }
        }
    }

    /** Create exchange
     * @param \AMQPChannel $channel
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    private function initExchange(\AMQPChannel $channel) : void
    {
        $exchange = new \AMQPExchange($channel);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setName('task_exchange');
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
    }

    /** Create queue
     * @param \AMQPChannel $channel
     *
     * @return \AMQPQueue
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    private function initQueue(\AMQPChannel $channel) : \AMQPQueue
    {
        $queue = new \AMQPQueue($channel);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setName('task_queue');
        $queue->declareQueue();
        $queue->bind('task_exchange','default.queue');
        return $queue;
    }

    /** Stop handler for worker
     * @return mixed|void
     */
    public function stop() : void
    {
        echo 'Worker stopped when finish worked with task...';
        $this->isInterrupted = true;
    }

}
