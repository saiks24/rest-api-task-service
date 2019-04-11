<?php
namespace Saiks24\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Saiks24\App\App;
use Saiks24\Command\CommandFactory;
use Saiks24\Command\TestCommand;
use Saiks24\Queue\AMQPQueue;
use Saiks24\Storage\RedisTaskStorage;
use Saiks24\Storage\StorageFactory;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use Slim\Http\Stream;

class CommandController
{

    use ResponseCreatorTrait;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            $taskParams = json_decode($request->getBody()->getContents(),true);
            if(empty($taskParams)) {
                throw new \InvalidArgumentException('Empty task params');
            }
            $validTasks = App::make()->getConfig()->configGetValue('tasks');
            if(!isset($validTasks[$taskParams['type']]) || empty($taskParams['args'])) {
                throw new \InvalidArgumentException('Wrong task params');
            }
            $queue = new AMQPQueue();
            $command = CommandFactory::createCommand($validTasks[$taskParams['type']],$taskParams['args']);
            $queue->addTaskToQueue($command);
            $storage = StorageFactory::getStorage(App::make()->getConfig());
            $command->setStatus('accept');
            $storage->add($command);
            $body = $response->getBody();
            $body->write(
                json_encode(['status'=>'accept','id'=>$command->getId()])
            );
            $response = $response
                ->withStatus(200)
                ->withBody($body);
            return $response;
        } catch (\InvalidArgumentException $e) {
            $message = json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            $badResponse = $this->createErrorResponse($message,400);
            return $badResponse;
        }
    }

    /** Delete task from storage
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            $requestBody = $request->getParsedBody();
            $taskId = $requestBody['id'] ?? null;
            if(empty($taskId)) {
                throw new \InvalidArgumentException('Param: id required in request string');
            }
            $storage = StorageFactory::getStorage(App::make()->getConfig());
            $storage->delete($taskId);
            $body = $response->getBody();
            $body->write(
              json_encode(['status'=>'success','message'=>'task deleted'])
            );
            $response = $response
              ->withStatus(200)
              ->withAddedHeader('Content-Type','application/json')
              ->withBody($body);
            return $response;
        } catch (\InvalidArgumentException $e) {
            $message = json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            return $this->createErrorResponse($message,400);
        }
    }

    /** Get task status
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|static
     */
    public function info(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            $taskId = $request->getQueryParams();
            $taskId = $taskId['id'] ?? null;
            if(empty($taskId)) {
                throw new \InvalidArgumentException('Param: id required in request string');
            }
            $storage = StorageFactory::getStorage(App::make()->getConfig());
            $task = $storage->get($taskId);
            $status = $task->getStatus();
            $body = $response->getBody();
            $body->write(
              json_encode(['status'=>'success','message'=>$status])
            );
            $response = $response
              ->withStatus(200)
              ->withAddedHeader('Content-Type','application/json')
              ->withBody($body);
            return $response;
        } catch (\InvalidArgumentException $e) {
            $message = json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            return $this->createErrorResponse($message,400);
        }
    }
}