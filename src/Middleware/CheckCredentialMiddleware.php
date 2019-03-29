<?php
namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Http\StatusCode;

class CheckCredentialMiddleware
{
    public function __invoke(ServerRequestInterface $request,ResponseInterface $response, $next)
    {
        try {
            $token = $request->getHeaderLine('Authorization');
            if(empty($token)) {
                throw new \InvalidArgumentException('Token required in Authorization header');
            }
            /** @var \Psr\Http\Message\ResponseInterface $response */
            $response = $next($request,$response);

            return $response;
        } catch (\InvalidArgumentException $e) {
            $response = $response
              ->withHeader('Content-Type','application/json')
              ->withHeader('Cache-Control','private, no-cache, max-age=0, must-revalidate')
              ->withStatus(403);
            $body = $response->getBody();
            $body->write(\json_encode(['status'=>'error','message'=>$e->getMessage()]));
            $response->withBody($body);
            return $response;
        }

    }

    private function validateToken(string $token)
    {

    }
}