<?php

class AuthorizationException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Unauthorized')
    {
        $this->message = $message;
        $this->statusCode = 403;
    }
}
