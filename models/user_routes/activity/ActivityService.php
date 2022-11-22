<?php
require_once('./models/user_routes/activity/ActivityRepository.php');
require_once('./models/user_routes/activity/ActivityTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');
require_once('./models/user_routes/room/RoomRepository.php');
require_once('./models/user_routes/item/ItemRepository.php');

class ActivityService implements ActivityTemplate
{
    private ItemRepository $item_repository;
    private RoomRepository $room_repository;
    private ActivityRepository $activity_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->item_repository = new ItemRepository();
        $this->room_repository = new RoomRepository();
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

    public function delete_activity($activity_id)
    {
        if ($this->activity_repository->delete_activity($activity_id)) {
            return response(['message' => 'Activity deleted.'], 200);
        } else {
            return response(['message' => 'Resource not found.'], 400);
        }
    }

    public function get_activities_by_block($student_data){
        return response($this->activity_repository->get_activities_by_block($student_data), 200);
    }

    public function get_activities_by_room($room_id){
        return response($this->activity_repository->get_activities_by_room($room_id), 200);
    }


    public function get_activities_by_instructor($instructor_id)
    {
        $result = array();
        $rooms = $this->room_repository->get_instructor_rooms($instructor_id);

        //get activities per room
        foreach ($rooms as $room) {

            $activities = $this->activity_repository->get_activities_by_room($room['room_id']);

            if ($activities) {

                foreach ($activities as $key => $activity) {
                    $items = $this->item_repository->get_items($activity['activity_id']);

                    if($items){
                        $acts[$key]['items'] = $items;
                    }
                    
                }

                array_push($result, ['room_name' => $room['room_name'], 'activities' => $activities]);
            }
        }

        return response($result, 200);
    }

}
