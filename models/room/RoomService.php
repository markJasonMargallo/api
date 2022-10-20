<?php

require_once('./modules/Auth.php');
require_once('./models/room/RoomRepository.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class RoomService
{
    private AuthModule $auth_module;
    private RoomRepository $room_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->auth_module = new AuthModule();
        $this->auth_repository = new AuthRepository();
        $this->validator = new Validation();
    }

    public function create_room($room_data)
    {
        if($this->validator->is_body_valid($room_data, './schemas/room_schema.json')){

            if($this->room_repository->add_room($room_data->room_name, $this->auth_module->get_account_id()) > 0){
                return response(['message' => 'Room created successfully'], 200);
            }
        }else{
            return $this->validator->invalid_body();
        }
    }


}