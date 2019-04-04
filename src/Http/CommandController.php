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
        $command = new TestCommand(10,'progress',$taskId);
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

    /** Delete task from storage
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {
        $taskId = $request->getParsedBody()['id'] ?? null;
        $storage = new RedisTaskStorage(new \Redis());
        $storage->delete($taskId);
        $body = $response->getBody();
        $body->write(
          json_encode(['status'=>'success','message'=>'task deleted'])
        );
        $response = $response
          ->withStatus(200)
          ->withBody($body);
        return $response;
    }

    /** Get task status
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function info(ServerRequestInterface $request, ResponseInterface $response)
    {
        $taskId = $request->getQueryParams()['id'];
        $storage = new RedisTaskStorage(new \Redis());
        $task = $storage->get($taskId);
        $status = $task->getStatus();
        $body = $response->getBody();
        $body->write(
          json_encode(['status'=>'success','message'=>$status])
        );
        $response = $response
          ->withStatus(200)
          ->withBody($body);
        return $response;
    }
}