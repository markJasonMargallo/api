<?php
require_once('./models/user_routes/solution/SolutionService.php');
require_once('./models/exception/NotFoundException.php');

class SolutionRoutes
{
    private SolutionService $solution_service;
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

        $trimmed_url = str_replace('solution/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->solution_service = new SolutionService();
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
                case 'POST':
                    if ($current_route == 'solution') {
                        echo json_encode($this->solution_service->add_code($request_body));
                    }
                    break;
                case 'GET':
                    if ($current_route == 'solution' && $count == 2) {
                        if (intval($next_route) > 0) {
                            echo json_encode($this->solution_service->get_code($next_route));
                        } else {
                            throw new NotFoundException();
                        }
                    }
                    break;
                case 'PUT':
                    if ($current_route == 'solution' && $count == 1) {
                        echo json_encode($this->solution_service->update_code($request_body));
                    }else if($current_route == 'solution' && $next_route = 'test'){
                        echo json_encode($this->solution_service->add_test($request_body));
                    }
                    break;
                default:
                    throw new NotFoundException();
            }
        }
    }
}
