<?php

require_once('./models/room/RoomRepository.php');
require_once('./models/room/RoomTemplate.php');
require_once('./models/student/InstructorService.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class RoomService implements RoomTemplate
{
    private RoomRepository $room_repository;
    private InstructorService $instructor_service;
    private Validation $validator;

    public function __construct()
    {

        $this->auth_repository = new AuthRepository();
        $this->validator = new Validation();
        $this->instructor_service = new InstructorService();
        $this->room_repository = new RoomRepository();
    }

    public function add_room($room_data, $user_email)
    {
        if ($this->validator->is_body_valid($room_data, './schemas/room_schema.json')) {

            $student_id = $this->instructor_service->get_student_id($user_email);

            if ($this->room_repository->add_room($room_data->room_name, $student_id) > 0) {
                return response(['message' => 'Room created successfully'], 200);
            }
        } else {
            return $this->validator->invalid_body();
        }
    }

    public function get_room($id)
    {
        return response($this->room_repository->get_room($id), 200);
    }

    public function search_rooms($search_string)
    {
        return response($this->room_repository->search_rooms($search_string), 200);
    }

    public function get_rooms_by_instructor($instructor_id)
    {
        return response($this->room_repository->get_rooms_by_instructor($instructor_id), 200);
    }

    public function get_room_instructor($room_id)
    {
        return response($this->room_repository->get_room_instructor($room_id), 200);
    }

    public function get_rooms_by_student($student_id)
    {
        return response($this->room_repository->get_rooms_by_student($student_id), 200);
    }

    public function get_room_activities($room_id)
    {
        return response($this->room_repository->get_room_activities($room_id), 200);
    }

    public function update_room_name($room_name, $room_id)
    {
        return response($this->room_repository->update_room_name($room_name, $room_id), 200);
    }

    public function delete_room($room_id)
    {
        return response($this->room_repository->delete_room($room_id), 200);
    }

    public function get_room_participants($room_id)
    {
        return response($this->room_repository->get_room_participants($room_id), 200);
    }

    public function add_room_participant($room_id, $student_id)
    {
        return response($this->room_repository->add_room_participant($room_id, $student_id), 200);
    }
}
