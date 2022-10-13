<?php

class AuthRoutes
{
    private $url;
    private string $method;

    public function __construct(string $url, string $method)
    {

        $this->url = explode('/', $url);
        $this->method = $method;
        echo 'authRoutes Class';
    }

    public function handle_url()
    {

        $request_body = json_decode(file_get_contents('php://input'));

        switch ($this->method) {

            case 'POST':
                if ($this->url == 'student-login') {
                    $username = $request_body->username;
                    echo $username;
                }

                break;
        }
    }
}
