<?php
require_once('./models/exception/NotFoundException.php');
require_once('./models/instructor/room/RoomService.php');
require_once('./models/instructor/room/RoomRoutes.php');
require_once('./models/exception/AuthorizationException.php');
require_once('./models/exception/AuthenticationException.php');


class InstructorRoutes
{
    private RoomService $room_service;
    private Request $request_data;
    private Middleware $middleware;

    public function __construct(Request $request_data)
    {
        $this->request_data = $request_data;

        if ($this->request_data->is_header_provided()) { 

            $this->middleware = new Middleware($this->request_data->get_header());

            if (!$this->middleware->is_instructor()) {
                throw new AuthorizationException();
            }
        } else {
            throw new AuthenticationException();
        }


        $trimmed_url= str_replace('instructor/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);
        $this->url = explode('/', $trimmed_url);
        $this->room_service = new RoomService();
    }

    public function handle_url()
    {
        $url = $this->url;
        $current_route = $url[0];
        $count = count($url);
        $next_route = null;
        if ($count > 1) {
            $next_route = $url[1];
        }

        switch ($current_route) { 
            case ('room' || 'rooms'):
                if($next_route == 'activity'){
                    echo "activity routes";
                }else{
                    $room_routes = new RoomRoutes($this->request_data, $this->middleware);
                    $room_routes->handle_url();
                }
                
                break;
            default:
                throw new NotFoundException();
        }
    }
}
