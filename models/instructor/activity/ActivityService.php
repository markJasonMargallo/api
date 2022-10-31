<?php
require_once('./models/activity/ActivityRepository.php');
require_once('./models/activity/ActivityTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class RoomService implements ActivityTemplate
{
    private ActivityRepository $activity_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->activity_repository = new ActivityRepository();
        $this->validator = new Validation();
    }

    public function add_activity($room_id, $activity_data)
    {
        if ($this->activity_repository->add_activity($room_id, $activity_data)){
            return response(['message' => 'Activity Added Successfully.'], 200);
        }
    }

    public function get_activity($activity_id)
    {
        return response($this->activity_repository->get_activity($activity_id), 200);
    }

    public function search_activity($search_string)
    {
    }

    public function get_room_activities($room_id)
    {
    }

    public function update_activity($room_id, $activity_details)
    {
    }

    public function delete_activity($room_id)
    {
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
}
