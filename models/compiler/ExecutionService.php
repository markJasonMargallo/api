<?php
class ExecutionService
{

    private string $url = "http://localhost:3000/";
    private $options;
    private $context;

    public function __construct(string $method, $request_body)
    {

        $this->options = array(
                'http' => array(
                    'header'  => "Content-type: application/json",
                    'method'  => "$method",
                    'content' => json_encode($request_body)
                )
            );

        $this->context = stream_context_create($this->options);
    }

    public function http_request(){
        return file_get_contents($this->url, false, $this->context);

        // if($result){
        //     return true;
        // }

        // var_dump($result);
    }
}
