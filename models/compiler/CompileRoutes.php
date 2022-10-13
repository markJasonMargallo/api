<?php
require_once('./models/compiler/Compile.php');
class CompileRoutes
{

    private $url;
    private string $method;

    public function __construct(string $url, string $method)
    {

        $this->url = explode('/', $url);
        $this->method = $method;
    }

    public function handle_url()
    {

        switch ($this->method) {
            case 'POST':
                $request_body = json_decode(file_get_contents('php://input'));
                $language = $request_body->language;
                $code = $request_body->code;

                $compile = new Compile($language, $code);
                $compile->execute();
                break;
        }
    }
}
