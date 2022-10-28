<?php
require_once('./models/room/RoomService.php');


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

            if(!$this->middleware->is_instructor()){
                throw new Exception("Unauthorized");
            }

        } else {
            throw new Exception("Authentication Required");
        }

        $this->url = str_replace('room/', '', $this->request_data->get_request_url());
        $this->method = $this->request_data->get_request_method();
        $this->room_service = new RoomService();
    }

    public function handle_url()
    {
        $request_body = json_decode(file_get_contents('php://input'));

        switch ($this->method) {

            case 'POST':
                if ($this->url == 'create') {
                    echo json_encode($this->room_service->add_room($request_body, $this->middleware->get_owner_email()));
                }else if ($this->url == 'instructor-login') {

                }
                break;
        }
    }
}
