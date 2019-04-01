<?php

namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class ReitLimiter
{
    /** @var \Redis */
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function __invoke(ServerRequestInterface $request, Response $response,$next)
    {
        $this->redis->connect('0.0.0.0');
        $responseId = $this->generateIdByRequest($request);

        if($this->isOverReit($responseId)) {
            return $this->createBadRequest($response);
        }

        $this->incrementReitById($responseId);
        $response = $next($request,$response);
        return $response;
    }

    private function isOverReit(string $responseId) : bool
    {

    }

    private function incrementReitById(string $responseId)
    {

    }

    private function generateIdByRequest(ServerRequestInterface $response) : string
    {

    }

    private function createBadRequest(ResponseInterface $response)
    {
        $badRequestResponse = $response->withStatus(400);
        $body = $badRequestResponse->getBody();
        $body->write(\json_encode(['status'=>'error','message'=>'too many requests']));
        $badRequestResponse->withBody($body);
        return $badRequestResponse;
    }
}