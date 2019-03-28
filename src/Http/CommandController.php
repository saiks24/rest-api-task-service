<?php
namespace Saiks24\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Saiks24\Command\TestCommand;
use Saiks24\Queue\AMQPQueue;
use Saiks24\Storage\RedisTaskStorage;
use Slim\Http\Body;

class CommandController
{

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response)
    {
        $taskId = md5(rand(0,PHP_INT_MAX));
        $queue = new AMQPQueue();
        $command = new TestCommand(3,'progress',$taskId);
        $queue->addTaskToQueue($command);
        $storage = new RedisTaskStorage(new \Redis());
        $storage->add($command);
        $body = $response->getBody();
        $body->write(
          json_encode(['status'=>'success','id'=>$command->getId()])
        );
        $response = $response
          ->withStatus(200)
          ->withBody($body);
        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {

    }

    public function info(ServerRequestInterface $request, ResponseInterface $response)
    {

    }
}