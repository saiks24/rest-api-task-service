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

        $verifyMock = self::getMockBuilder(\Saiks24\Verification\FromConfigCredentialValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $verifyMock->method('validate')->willReturn(true);
        $checkCredentialMiddleware = new \Saiks24\Middleware\CheckCredentialMiddleware();
        $checkCredentialMiddleware->setVerify($verifyMock);


        $checkCredentialMiddleware($requestMock,$responseMock,[]);
    }

    public function testThatIfNotValidTokenReturnedErrorResponse()
    {
        $requestMock = self::getMockBuilder(\Slim\Http\Request::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $requestMock->expects(self::once())
            ->method('getHeaderLine')
            ->withConsecutive(['Authorization'])->willReturn('1234');

        $responseMock  = self::getMockBuilder(\Slim\Http\Response::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $responseMock->method('withHeader')->willReturn($responseMock);
        $responseMock->method('getBody')->willReturn('');

        $verifyMock = self::getMockBuilder(\Saiks24\Verification\FromConfigCredentialValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $verifyMock->method('validate')->willReturn(false);
        $checkCredentialMiddleware = new \Saiks24\Middleware\CheckCredentialMiddleware();
        $checkCredentialMiddleware->setVerify($verifyMock);

        /** @var \Slim\Http\Response $response */
        $response = $checkCredentialMiddleware($requestMock,$responseMock,[]);

        self::assertEquals(\Codeception\Util\HttpCode::FORBIDDEN,$response->getStatusCode());
    }
}