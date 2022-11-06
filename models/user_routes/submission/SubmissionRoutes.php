<?php
require_once('./models/user_routes/submission/SubmissionService.php');
require_once('./models/exception/NotFoundException.php');

class SubmissionRoutes
{
    private SubmissionService $submission_service;
    private Request $request_data;
    private Middleware $middleware;
    private $url;
    private $method;
    private $parent_route;

    public function __construct(Request $request_data, Middleware $middleware, $parent_route)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;
        $this->parent_route = $parent_route;

        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('item/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->submission_service = new SubmissionService();
    }

    public function handle_url()
    {
        $parent_route = $this->parent_route;
        $request_body = json_decode(file_get_contents('php://input'));
        $url = $this->url;
        $count = count($url);
        $current_route = $url[0];
        $next_route = null;
        
        if ($count > 1) {
            $next_route = $url[1];
        }

        if ($parent_route == 'instructor') {
            switch ($this->method) {
                case 'GET':
                    if ($current_route == 'submission' && $count == 2) {
                        if (intval($next_route) > 0) {
                            echo json_encode($this->submission_service->get_submission($next_route));
                        } else {
                            throw new NotFoundException();
                        }
                    } else if ($current_route == 'submissions' && $count == 2) {
                        echo json_encode($this->submission_service->get_submissions($next_route));
                    }
                    break;
                default:
                    throw new NotFoundException();
            }
        }else if ($parent_route == 'student') {
            switch ($this->method) {
                case 'POST':
                    if ($current_route == 'submission') {
                        echo json_encode($this->submission_service->add_submission($request_body, $this->middleware->get_owner_id()));
                    }
                    break;
                case 'GET':
                    if ($current_route == 'submission' && $count == 2) {
                        if (intval($next_route) > 0) {
                            echo json_encode($this->submission_service->get_submission($next_route));
                        } else {
                            throw new NotFoundException();
                        }
                    } else if ($current_route == 'submissions' && $count == 2) {
                        echo 'here';
                        echo json_encode($this->submission_service->get_submissions($next_route));
                    }
                    break;
                case 'PUT':
                    if ($current_route == 'submission') {
                        echo json_encode($this->submission_service->update_student_submission($request_body, $this->middleware->get_owner_id()));
                    }
                    break;
                default:
                    throw new NotFoundException();
            }
        }



        
    }
}
