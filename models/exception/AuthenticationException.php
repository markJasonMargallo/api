<?php

class AuthenticationException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Authorization required.')
    {
        $this->message = $message;
        $this->statusCode = 401;
    }
}
