<?php
require_once('./models/instructor/student/StudentService.php');
require_once('./models/exception/NotFoundException.php');
require_once('./models/instructor/submission/SubmissionRoutes.php');

class StudentRoutes
{
    private StudentService $student_service;
    private Request $request_data;
    private Middleware $middleware;

    public function __construct(Request $request_data, Middleware $middleware)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;

        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('item/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->student_service = new StudentService();
    }

    public function handle_url()
    {
        $request_body = json_decode(file_get_contents('php://input'));

        $url = $this->url;
        $count = count($url);
        $current_route = $url[0];
        $next_route = null;
        
        if ($count > 1) {
            $next_route = $url[1];
        }

        switch ($this->method) {
            case 'POST':
                if ($current_route == 'students' && $count == 1) {
                    echo json_encode($this->student_service->get_students($request_body));
                }
                break;
            case 'GET':
                if ($current_route == 'student' && $count == 2) {
                    if (intval($next_route) > 0) {
                        echo json_encode($this->student_service->get_student($next_route));
                    } else {
                        throw new NotFoundException();
                    }
                } 
                break;
            default:
                throw new NotFoundException();
                break;
        }
        
    }
}
