<?php
require_once('./models/middleware/Middleware.php');
require_once('./models/compiler/ExecutionService.php');
require_once('./models/compiler/CodeTestingService.php');

class CompileRoutes
{

    private CodeTestingService $code_testing_service;
    private Request $request_data;
    private Middleware $middleware;
    private $url;
    private $method;
    private $parent_route;

    public function __construct(Request $request_data)
    {
        $this->request_data = $request_data;

        if ($this->request_data->is_header_provided()) {
            $this->middleware = new Middleware($this->request_data->get_header());
        } else {
            throw new AuthenticationException();
        }

        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('run/', '', $this->request_data->get_request_url());

        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
    }

    public function handle_url()
    {
        $parent_route = $this->parent_route;
        $request_body = json_decode(file_get_contents('php://input'));
        $url = $this->url;
        $count = count($url);
        $current_route = $url[1];

        $method = $this->request_data->get_request_method();
        $request_body = $this->request_data->get_request_body();

        switch ($this->method) {

            case 'POST':
                if ($current_route == "execute") {
                    $execution = $this->execution_service = new ExecutionService($method, $request_body);
                    $result = $execution->run_code();
                    echo $result;
                }
                if ($current_route == "test") {
                    $this->code_testing_service = new CodeTestingService($method, $request_body);
                    $this->code_testing_service->run_tests();
                }
                if ($current_route == "submit") {
                    $this->code_testing_service = new CodeTestingService($method, $request_body);
                    $this->code_testing_service->run_tests();
                }
                break;
            default:
                throw new NotFoundException();
        }
    }
}
