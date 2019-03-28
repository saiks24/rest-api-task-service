<?php
namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CheckCredentialMiddleware
{
    public function __invoke(ServerRequestInterface $request,ResponseInterface $response, $next)
    {
        $token = $request->getHeaderLine('Authorization');
        $body = $response->getBody();
        $body->write($token);
        $response = $response->withBody($body);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request,$response);

        return $response;
    }

    private function validateToken(string $token)
    {

    }
}