<?php
require_once __DIR__.'/../../vendor/autoload.php';

class CheckCredentialMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    public function testThatRequestArgCalledMethodGetHeaderLine()
    {
        $requestMock = self::getMockBuilder(\Slim\Http\Request::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $requestMock->expects(self::once())
            ->method('getHeaderLine')
            ->withConsecutive(['Authorization'])->willReturn('kjnxy1fjj1o231t05tes');

        $responseMock  = self::getMockBuilder(\Slim\Http\Response::class)
        ->disableOriginalConstructor()
        ->disableOriginalClone()
        ->getMock();
        $checkCredentialMiddleware = new \Saiks24\Middleware\CheckCredentialMiddleware();


        $checkCredentialMiddleware($requestMock,$responseMock,[]);
    }
}