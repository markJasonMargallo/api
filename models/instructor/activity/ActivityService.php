<?php
require_once('./models/instructor/activity/ActivityRepository.php');
require_once('./models/instructor/activity/ActivityTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class ActivityService implements ActivityTemplate
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
        if ($this->activity_repository->add_activity($room_id, $activity_data)) {
            return response(['message' => 'Activity Added Successfully.'], 200);
        }
    }

    public function get_activity($activity_id)
    {
        return response($this->activity_repository->get_activity($activity_id), 200);
    }

    public function search_activity($search_string)
    {
        return response($this->activity_repository->search_activity($search_string), 200);
    }

    public function get_activities($room_id, $instructor_id)
    {
        return response($this->activity_repository->get_activities($room_id, $instructor_id), 200);
    }

    public function update_activity($activity_data)
    {
        if ($this->activity_repository->update_activity($activity_data)) {
            return response(['message' => 'Activity updated.'], 200);
        } else {
            return response(['message' => 'Nothing to update.'], 400);
        }
    }

    public function delete_activity($room_id)
    {
        if ($this->activity_repository->delete_activity($room_id)) {
            return response(['message' => 'Activity deleted.'], 200);
        } else {
            return response(['message' => 'Resource not found.'], 400);
        }
    }
}
