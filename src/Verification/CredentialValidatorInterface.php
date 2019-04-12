<?php

namespace Saiks24\Verification;

interface CredentialValidatorInterface
{
    public function validate(String $token) : bool;
}