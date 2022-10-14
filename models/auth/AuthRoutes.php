<?php
require_once('./models/middleware/Middleware.php');
require_once('./models/auth/AuthService.php');


class AuthRoutes extends Middleware
{
    private $url;
    private string $method;
    private AuthService $auth_service;

    public function __construct(string $url, string $method)
    {
        // echo 'auth/';
        $this->url = str_replace('auth/', '', $url);
        $this->method = $method;
        $this->auth_service = new AuthService();
    }

    public function handle_url()
    {
        $request_body = json_decode(file_get_contents('php://input'));

        switch ($this->method) {

            case 'POST':
                if ($this->url == 'student-login') {
                    echo json_encode($this->auth_service->login_student($request_body));
                }else if ($this->url == 'instructor-login') {
                    echo json_encode($this->auth_service->login_instructor($request_body));
                }else if ($this->url == 'refresh-token') {
                    echo json_encode($this->auth_service->refresh_token());
                }
                break;
        }
    }
}
