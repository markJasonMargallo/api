<?php
require_once('./config/Config.php');
require_once('./models/compiler/CompileRoutes.php');
require_once('./models/auth/AuthRepository.php');

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
            $compile_routes = new CompileRoutes($request, $request_method);
            $compile_routes->handle_url();
            
        }else if(str_starts_with($request, 'auth')){
            $auth_routes = new AuthRoutes($request, $request_method);
            $auth_routes->handle_url();
        }

        
    }
}

$route = new Route();
$route->clientRequest();