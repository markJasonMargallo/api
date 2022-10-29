<?php

class AuthorizationException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Invalid Token')
    {
        $this->message = $message;
        $this->statusCode = 403;
    }
}
