<?php


namespace Saiks24\Http;


use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body;
use Slim\Http\Response;
use Slim\Http\Stream;

trait ResponseCreatorTrait
{
    /**
     * @param string $message
     * @param int $responseCode
     *
     * @return ResponseInterface
     */
    public function createErrorResponse(string $message,int $responseCode)
    {
        $body = new Stream(fopen('php://temp','a+'));
        $body->write($message);

        $errorResponse = new Response($responseCode, null,$body);

        $errorResponse->withAddedHeader('Content-Type','application/json')
            ->withAddedHeader('Cache-Control','private, no-cache, max-age=0, must-revalidate');

        return $errorResponse;
    }
}