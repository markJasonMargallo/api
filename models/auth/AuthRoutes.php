<?php
require_once('./models/middleware/Middleware.php');
require_once('./models/auth/AuthService.php');


class AuthRoutes
{
    private Request $request_data;
    private AuthService $auth_service;

    public function __construct(Request $request_data)
    {
        $this->request_data = $request_data;
        $this->auth_service = new AuthService();
    }

    public function handle_url()
    {
        $url = str_replace('auth/', '', $this->request_data->get_request_url());
        $method = $this->request_data->get_request_method();
        $body = $this->request_data->get_request_body();

        switch ($method) {

            case 'POST':
                if ($url == 'student-login') {
                    echo json_encode($this->auth_service->login_student($body));
                }else if ($url == 'instructor-login') {
                    echo json_encode($this->auth_service->login_instructor($body));
                }else if ($url == 'register-student') {
                    echo json_encode($this->auth_service->register_student($body));
                }else if ($url == 'register-instructor') {
                    echo json_encode($this->auth_service->register_instructor($body));
                }else if ($url == 'refresh-token') {
                    echo json_encode($this->auth_service->refresh_token());
                }
                break; 
        }
    }
}
