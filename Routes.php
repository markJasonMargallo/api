<?php
require_once('./config/Config.php');
require_once('./models/compiler/CompileRoutes.php');
require_once('./models/auth/AuthRoutes.php');
require_once('./models/Request.php');
require_once('./models/instructor/InstructorRoutes.php');


// error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED);

class Route
{
    public function __construct()
    {
    }

    public function client_request(): void
    {
        try{
            $request_data = new Request();

            $request_url = $request_data->get_request_url();
            $request_method = $request_data->get_request_method();

            if ($request_method == 'OPTIONS') {
                http_response_code(200);
                return;
            }

            if (str_starts_with($request_url, 'compile')) {
                $compile_routes = new CompileRoutes($request_data);
                $compile_routes->handle_url();
            } else if (str_starts_with($request_url, 'auth')) {
                $auth_routes = new AuthRoutes($request_data);
                $auth_routes->handle_url();
            } else if (str_starts_with($request_url, 'instructor')) {
                $instructor_routes = new InstructorRoutes($request_data);
                $instructor_routes->handle_url();
            } else {
                http_response_code(404);
            }

        } catch (AuthorizationException $exception) {
            echo json_encode(response(['message' => $exception->getMessage()], $exception->statusCode));
            return;
        } catch (BadRequestException $exception){
            echo json_encode(response(['message' => $exception->getMessage()], $exception->statusCode));
            return;
        } catch (AuthenticationException $exception) {
            echo json_encode(response(['message' => $exception->getMessage()], $exception->statusCode));
            return;
        } catch (NotFoundException $exception) {
            echo json_encode(response(['message' => $exception->getMessage()], $exception->statusCode));
            return;
        }
    }
}

$route = new Route();
$route->client_request();
