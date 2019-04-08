<?php

class TaskDeleteCest
{
    public function tryToTestCredentialMiddleware(ApiTester $I)
    {
        $I->wantToTest('Что без токена досутпа сервер отдаст 401');
        $I->sendDELETE('/api/v1/command/delete');
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
        $I->sendDELETE('/api/v1/command/delete');
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
        $I->sendDELETE('/api/v1/command/delete');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error'
        ]);
        $I->seeResponseContainsJson([
          'message' => 'Token required in Authorization header'
        ]);
    }

    public function tryToTestGetInfoWithoutIdInRequest(ApiTester $I)
    {
        $I->wantToTest('Что без параметра - id в запросе сервер отдаст 400 ошибку');
        //TODO убрать хардкод
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendDELETE('/api/v1/command/delete');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error',
          'message' => 'Param: id required in request string'
        ]);
    }

    public function tryToTestSuccessfullDeleteTask(ApiTester $I)
    {
        $I->wantToTest('Успешное удаление задачи');
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendDELETE('/api/v1/command/delete',['id'=>'123456']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'success',
          'message' => 'task deleted'
        ]);
    }
}