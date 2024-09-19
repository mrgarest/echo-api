<?php

namespace MrGarest\EchoApi\Exceptions;

final class ErrorCodeNotFoundException extends \Exception
{
    protected $message = 'The error code was not found in `config/echo-api.php`.';
}
