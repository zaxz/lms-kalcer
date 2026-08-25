<?php

namespace App\Exceptions;

class LmsSessionExpiredException extends \Exception
{
    protected $code = 401;
}
