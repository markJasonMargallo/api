<?php
require_once('./models/compiler/Compile.php');
require_once('./models/middleware/Middleware.php');

class CompileRoutes
{

    private Request $request_data;
    private Middleware $middleware;

    public function __construct(Request $request_data)
    {
        $this->request_data = $request_data;

        if ($this->request_data->is_header_provided()) {

            $this->middleware = new Middleware($this->request_data->get_header());

        } else {
            throw new Exception("Authentication Required");
        }

        // $this->url = explode('/', $request_data->get_request_url());
        // $this->method = $request_data->get_request_method();
    }

    public function handle_url()
    {

        $method = $this->request_data->get_request_method();
        $request_body = $this->request_data->get_request_body();

        switch ($method) {

            case 'POST':

                $language = $request_body->language;
                $code = $request_body->code;

                $compile = new Compile($language, $code);
                $compile->execute();

                break;
        }
    }
}
