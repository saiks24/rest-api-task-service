<?php

namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class RateLimiter
{
    /** @var \Redis */
    private $redis;

    /** int */
    private $requestLimit;

    public function __construct(\Redis $redis,int $requestLimit)
    {
        $this->redis = $redis;
        $this->requestLimit = $requestLimit;
    }


    /** Check that client don't send requests many than limit
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Slim\Http\Response                      $response
     * @param                                          $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, Response $response,$next)
    {
        $this->redis->connect('0.0.0.0');
        $responseId = $this->generateIdByRequest($request);

        if($this->isOverRate($responseId)) {
            return $this->createBadRequest($response);
        }

        $this->incrementRateById($responseId);
        $response = $next($request,$response);
        return $response;
    }

    /** Check that limit was reached
     * @param string $responseId
     *
     * @return bool
     */
    private function isOverRate(string $responseId) : bool
    {
        $key = 'limiter:'.$responseId;
        if($this->redis->setnx($responseId,$key)) {
            $this->redis->expire($responseId,10);
        }
        $limit = $this->redis->get($key);
        return (int)$limit > $this->requestLimit;
    }

    /** Increment count of requests
     * @param string $responseId
     */
    private function incrementRateById(string $responseId)
    {
        $key = 'limiter:'.$responseId;
        $this->redis->incr($key);
    }

    /** Generate id for request
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return string
     */
    private function generateIdByRequest(ServerRequestInterface $request) : string
    {
        $token = $request->getHeaderLine('Authorization');
        $id = $request->getUri() . '::' . $token . '::' . time();
        return $id;
    }

    /** Create 400 Response
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function createBadRequest(ResponseInterface $response) : ResponseInterface
    {
        $badRequestResponse = $response->withStatus(400);
        $body = $badRequestResponse->getBody();
        $body->write(\json_encode(['status'=>'error','message'=>'too many requests']));
        $badRequestResponse->withBody($body);
        return $badRequestResponse;
    }
}