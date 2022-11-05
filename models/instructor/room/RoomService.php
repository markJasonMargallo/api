<?php

require_once('./models/instructor/room/RoomRepository.php');
require_once('./models/instructor/room/RoomTemplate.php');
require_once('./models/auth/AuthService.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class RoomService implements RoomTemplate
{
    private RoomRepository $room_repository;
    private AuthService $auth_service;
    private Validation $validator;

    public function __construct()
    {

        $this->auth_repository = new AuthRepository();
        $this->validator = new Validation();
        $this->auth_service = new AuthService();
        $this->room_repository = new RoomRepository();
    }

    public function add_room($room_data, $user_email)
    {
        $instructor_id = $this->auth_service->get_instructor_id($user_email);

        if ($this->room_repository->add_room($room_data, $instructor_id) > 0) {
            return response(['message' => 'Room created successfully'], 200);
        }

        // if ($this->validator->is_body_valid($room_data, './schemas/room_schema.json')) {

           
        // } else {
        //     return $this->validator->invalid_body();
        // }
    }

    public function get_room($id)
    {
        return response($this->room_repository->get_room($id), 200);
    }

    public function search_rooms($search_string)
    {
        return response($this->room_repository->search_rooms($search_string), 200);
    }

    public function get_rooms($instructor_id)
    {
        return response($this->room_repository->get_rooms($instructor_id), 200);
    }

    public function update_room($room_data)
    {
        if($this->room_repository->update_room($room_data)){
            return response(["message" => "Room updated."], 200);
        }else{
            return response(["message" => "Nothing to update."], 400);
        }
        
    }

    public function delete_room($room_id)
    {
        if ($this->room_repository->delete_room($room_id)) {
            return response(["message" => "Room deleted."], 200);
        } else {
            return response(["message" => "Resource not found."], 404);
        }
    }
}
