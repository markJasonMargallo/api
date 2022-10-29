<?php

class NotFoundException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Resource not found.')
    {
        $this->message = $message;
        $this->statusCode = 404;
    }
}
