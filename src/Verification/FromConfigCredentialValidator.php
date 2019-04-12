<?php

namespace Saiks24\Verification;


use Saiks24\App\App;

class FromConfigCredentialValidator implements CredentialValidatorInterface
{

    /** Validate input credential
     * @param String $token
     * @return bool
     */
    public function validate(String $token): bool
    {
        $app = App::make();
        $appToken = $app->getConfig()->configGetValue('token');
        return $appToken === trim($token);
    }

}