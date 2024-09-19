<?php

namespace MrGarest\EchoApi\Exceptions;

final class HttpStatusCodeExistException extends \Exception
{
    protected $message = 'HTTP status codes do not exist';
}
