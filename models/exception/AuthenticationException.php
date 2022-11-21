<?php

class AuthenticationException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Forbidden.')
    {
        $this->message = $message;
        $this->statusCode = 403;
    }
}
