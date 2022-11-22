<?php
require_once('./models/middleware/Middleware.php');
require_once('./models/compiler/ExecutionService.php');

class CompileRoutes
{

    private Request $request_data;
    private Middleware $middleware;
    private ExecutionService $execution_service;

    public function __construct(Request $request_data)
    {
        $this->request_data = $request_data;

        if ($this->request_data->is_header_provided()) {

            $this->middleware = new Middleware($this->request_data->get_header());

        } else {
            throw new AuthenticationException();
        }

    }

    public function handle_url()
    {

        $method = $this->request_data->get_request_method();
        $request_body = $this->request_data->get_request_body();

        switch ($method) {

            case 'POST':
                $execution = $this->execution_service = new ExecutionService($method, $request_body);
                $result = $execution->http_request();
                echo $result;
                break;
        }

    }
}
