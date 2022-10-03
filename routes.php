<?php
require_once('./config/Config.php');
require_once('./models/compiler/CompileRoutes.php');

// error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED);


class Route {
    protected Connection $db;
    // protected Auth $auth;
    protected PDO $pdo;
    

    public function __construct(){
        // $this->db = new Connection();
        // $this->pdo = $this->db->connect();
        // $this->auth =  new Auth($this->pdo);
    }

    public function clientRequest(): void{
        
        $request = $_REQUEST['request'];
        $request = str_replace('api/', '', $request);
        $request_method = $_SERVER['REQUEST_METHOD'];

        if($request_method == 'OPTIONS'){
            http_response_code(200);
            return;
        }

        if(str_starts_with($request, 'compile')){
            $compileRoutes = new CompileRoutes($request, $request_method);
            $compileRoutes->handle_url();
        }
    }
}

$route = new Route();
$route->clientRequest();