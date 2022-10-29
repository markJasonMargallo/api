<?php
require_once('./models/room/RoomService.php');
require_once('./models/exception/AuthorizationException.php');
require_once('./models/exception//AuthenticationException.php');


class RoomRoutes extends Middleware
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

        // $this->url = str_replace('room/', '', $this->request_data->get_request_url());
        $this->url = explode('/', $this->request_data->get_request_url());
        $this->method = $this->request_data->get_request_method();
        $this->room_service = new RoomService();
    }

    public function handle_url()
    {
        $request_body = json_decode(file_get_contents('php://input'));

        switch ($this->method) {
            case 'POST':
                if (count($this->url) == 1) {
                    if ($this->url[0] == 'room') {
                        echo json_encode($this->room_service->add_room($request_body, $this->middleware->get_owner_email()));
                    }
                }
                break;
            case 'GET':
                if (count($this->url) == 2) {
                    if ($this->url[0] == 'room') {
                        echo json_encode($this->room_service->get_room($this->url[1]));
                    }
                } else if (count($this->url) == 1) {
                    if ($this->url[0] == 'rooms') {
                        echo json_encode($this->room_service->get_rooms_by_instructor($this->middleware->get_owner_id()));
                    }
                }
                break;
        }
    }
}
