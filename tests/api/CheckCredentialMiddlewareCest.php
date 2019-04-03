<?php 

class CheckCredentialMiddlewareCest
{
    public function _before(ApiTester $I)
    {
    }

    public function tryToTestCredentialMiddleware(ApiTester $I)
    {
        $I->wantToTest('Что без токена досутпа сервер отдаст 401');
        $I->sendGET('/api/v1/command/info',['id'=>'c25125bf853ef7cf70ade98c3ed5b4fc']);
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error'
        ]);
        $I->seeResponseContainsJson([
          'message' => 'Token required in Authorization header'
        ]);
    }
}
