<?php 

class CreateTaskCest
{
    public function _before(ApiTester $I)
    {
    }

    public function tryToTestCredentialMiddleware(ApiTester $I)
    {
        $I->wantToTest('Что без токена досутпа сервер отдаст 401');
        $I->sendPOST('/api/v1/command/create');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error'
        ]);
        $I->seeResponseContainsJson([
          'message' => 'Token required in Authorization header'
        ]);
    }

    public function tryToTestWrongCredential(ApiTester $I)
    {
        $I->wantToTest('Что с неверным токеном сервер вернет 401');
        $I->haveHttpHeader('Authorization','wrongToken');
        $I->sendPOST('/api/v1/command/create');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error'
        ]);
        $I->seeResponseContainsJson([
          'message' => 'Bad token'
        ]);
    }

    public function tryToTestWrongCredentialFieldInRequest(ApiTester $I)
    {
        $I->wantToTest('Что с токеном отправленным в неверном месте клиент получит ошибку');
        $I->haveHttpHeader('Wrong-Auth-Header','wrongToken');
        $I->sendPOST('/api/v1/command/create');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error'
        ]);
        $I->seeResponseContainsJson([
          'message' => 'Token required in Authorization header'
        ]);
    }

    public function tryToTestCreateTask(ApiTester $I)
    {
        $I->wantToTest('Создать задачу, и получить ее идентификатор и статус в ответе');
        //TODO Вынести хардкод
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendPOST('/api/v1/command/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'accept',
        ]);
        $I->seeResponseMatchesJsonType([
          'status' => 'string',
          'id' => 'string'
        ]);
    }
}
