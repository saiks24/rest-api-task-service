<?php

namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Saiks24\App\App;
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


    /** Check that client don't reach the limit of response per second
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Slim\Http\Response                      $response
     * @param                                          $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, Response $response,$next)
    {
        $app = App::make();
        $redisConfig = $app->configGetValue('redis');
        $this->redis->connect($redisConfig['host']);
        $responseId = $this->generateIdByRequest($request);

        if($this->isOverRate($responseId)) {
            return $this->createClientErrorResponse($response);
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
        if($this->redis->setnx($key,0)) {
            $this->redis->expire($responseId,1);
        }
        $limit = $this->redis->get($key);
        return (int)($limit) >= $this->requestLimit;
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
        $id = $request->getRequestTarget() . '::' . $token . '::' . time();
        return $id;
    }

    /** Create 400 Response
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function createClientErrorResponse(ResponseInterface $response) : ResponseInterface
    {
        $badRequestResponse = $response->withStatus(400);
        $body = $badRequestResponse->getBody();
        $body->write(\json_encode(['status'=>'error','message'=>'too many requests']));
        $badRequestResponse->withBody($body);
        return $badRequestResponse;
    }
}