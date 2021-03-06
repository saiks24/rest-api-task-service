<?php
namespace Saiks24\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Saiks24\App\App;
use Saiks24\Http\ResponseCreatorTrait;
use Saiks24\Verification\CredentialValidatorInterface;
use Slim\Http\Response;
use Slim\Http\StatusCode;

class CheckCredentialMiddleware
{
    use ResponseCreatorTrait;

    /** @var CredentialValidatorInterface */
    private $credentialValidator;

    public function __invoke(ServerRequestInterface $request,ResponseInterface $response, $next)
    {
        try {
            $token = $request->getHeaderLine('Authorization');
            if(empty($token)) {
                throw new \InvalidArgumentException('Token required in Authorization header');
            }
            if(!$this->validateToken(App::make(),$token)) {
                throw new \InvalidArgumentException('Bad token');
            }
            if(!empty($next)) {
                /** @var \Psr\Http\Message\ResponseInterface $response */
                $response = $next($request,$response);
            }
            return $response;

        } catch (\InvalidArgumentException $e) {
            $message = \json_encode(['status'=>'error','message'=>$e->getMessage()]);
            return $this->createErrorResponse($message,403);
        }

    }

    public function setVerify(CredentialValidatorInterface $credentialValidator)
    {
        $this->credentialValidator = $credentialValidator;
        return $this;
    }

    /** Validate request auth token
     * @param \Saiks24\App\App $app
     * @param string           $token
     *
     * @return bool
     */
    private function validateToken(App $app, string $token) : bool
    {
        return $this->credentialValidator->validate($token);
    }
}