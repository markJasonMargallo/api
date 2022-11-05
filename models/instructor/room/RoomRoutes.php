<?php
require_once('./models/instructor/room/RoomService.php');
require_once('./models/exception/NotFoundException.php');
require_once('./models/instructor/activity/ActivityRoutes.php');
require_once('./models/instructor/student/StudentRoutes.php');



class RoomRoutes
{
    private RoomService $room_service;
    private Request $request_data;
    private Middleware $middleware;

    public function __construct(Request $request_data, Middleware $middleware)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;


        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('room/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->room_service = new RoomService();
    }

    public function handle_url()
    {
        $request_body = json_decode(file_get_contents('php://input'));

        $params = $this->request_data->get_request_params();
        $url = $this->url;
        $count = count($url);
        $current_route = $url[0];
        $next_route = null;

        if ($count > 1) {
            $next_route = $url[1];
        }

        if ($next_route == 'activity' || $next_route == 'activities') {

            $activity_routes = new ActivityRoutes($this->request_data, $this->middleware);
            $activity_routes->handle_url();
        } else if ($next_route == 'student' || $next_route == 'students') {

            $student_routes = new StudentRoutes($this->request_data, $this->middleware);
            $student_routes->handle_url();
        } else {

            switch ($this->method) {
                case 'POST':
                    if ($current_route == 'room') {
                        echo json_encode($this->room_service->add_room($request_body, $this->middleware->get_owner_email()));
                    }
                    break;
                case 'GET':
                    echo $current_route;

                    if ($params) {
                        echo json_encode($this->room_service->search_rooms($params['search']));
                    }

                    if ($current_route == 'room') {

                        if (intval($next_route) > 0) {
                            echo json_encode($this->room_service->get_room($next_route));
                        } else {
                            throw new NotFoundException();
                        }
                    } else if ($current_route == 'rooms') {
                        // echo $current_route;
                        echo json_encode($this->room_service->get_rooms($this->middleware->get_owner_id()));
                    }
                    break;
                case 'PUT':
                    if ($current_route == 'room') {
                        echo json_encode($this->room_service->update_room($request_body));
                    }
                    break;
                case 'DELETE':
                    if ($current_route == 'room') {
                        echo json_encode($this->room_service->delete_room($next_route));
                    }
                    break;
                default:
                    throw new NotFoundException();
                    break;
            }
        }
    }
}
