<?php

class TaskInfoCest
{
    public function _before(ApiTester $I)
    {
    }

    public function tryToTestCredentialMiddleware(ApiTester $I)
    {
        $I->wantToTest('Что без токена досутпа сервер отдаст 401');
        $I->sendGET('/api/v1/command/info');
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
        $I->sendGET('/api/v1/command/info');
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
        $I->sendGET('/api/v1/command/info');
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
        $I->sendGET('/api/v1/command/info');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
          'status' => 'error',
          'message' => 'Param: id required in request string'
        ]);
    }

    public function tryToTestSuccessGetInfoOfUndefinedTask(ApiTester $I)
    {
        $I->wantToTest('Что сервер корректно вернет ответ о том что заявка с идентификатором не существует');
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendGET('/api/v1/command/info',['id'=>'123456']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson([
          'status' => 'success',
          'message' => 'undefined'
        ]);
    }

    public function tryToTestSuccessGetInfoAboutTask(ApiTester $I)
    {
        $I->wantToTest('Что сервер корректно отдаст информацию по задаче');
        // Создадим задачу
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendPOST('/api/v1/command/create');
        $task = $I->grabResponse();
        $id = \json_decode($task,true)['id'];

        // Получим информацию по ней
        $I->haveHttpHeader('Authorization','kjnxy1fjj1o231t05tes');
        $I->sendGET('/api/v1/command/info',['id'=>$id]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson([
          'status' => 'success',
          'message' => 'progress'
        ]);
    }
}