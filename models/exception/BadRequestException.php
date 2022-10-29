<?php

class BadRequestException extends Exception
{
    public int $statusCode;

    public function __construct(string $message = 'Invalid request body.')
    {
        $this->message = $message;
        $this->statusCode = 400;
    }
}
