<?php
class Request{

    private $auth_header;
    private $header_provided = false;
    private $request_url;
    private $request_method;
    private $request_body;

    public function __construct()
    {
        $req = $_REQUEST['request'];

        if (isset(getallheaders()["Authorization"])) {
            $this->header_provided = true;
            $this->auth_header = getallheaders()["Authorization"];
        }

        $this->request_url = str_replace('api/', '', $req);
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_body = json_decode(file_get_contents('php://input'));
    }

    public function get_header(){
        return $this->auth_header;
    }

    public function set_url(string $new_url){
        $this->request_url = $new_url;
    }

    public function is_header_provided(){
        return $this->header_provided;
    }

    public function get_request_url(){
        return $this->request_url;
    }

    public function get_request_method(){
        return $this->request_method;
    }

    public function get_request_body(){
        return $this->request_body;
    }
}