<?php
namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CheckCredentialMiddleware
{
    public function __invoke(ServerRequestInterface $request,ResponseInterface $response, $next)
    {
        $token = $request->getHeader('Auth');
        if(empty($token)) {

        }

    }

    private function validateToken(\string $token)
    {

    }
}