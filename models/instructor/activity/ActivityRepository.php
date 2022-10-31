<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/room/RoomTemplate.php');

class ActivityRepository implements ActivityTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_activity($room_id, $activity_data)
    {
        $sql = 'INSERT INTO activities (activity_name, activity_description, room_id, deadline, language_assigned) 
        VALUES (?, ?, ?, ?, ?);';

        $values = [
            $activity_data->activity_name,
            $activity_data->activity_description,
            $room_id,
            $activity_data->deadline,
            $activity_data->language_assigned,
        ];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function get_activity($id)
    {
        $sql = "SELECT * FROM activities WHERE is_deleted = 0 AND activity_id = ?;";
        $values = [$id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function search_activity($search_string)
    {
        $sql = "SELECT * FROM activities WHERE is_deleted = 0 AND activity_name LIKE '%?%';";
        $values = [$search_string];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_room_activities($room_id)
    {
        $sql = "SELECT * FROM activities
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function update_activity($activity_data, $activity_id)
    {
        $sql = "UPDATE activities SET activity_name = ?
                WHERE is_deleted = 0 AND activity_id = ?;";
        $values = [$activity_data, $activity_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_activity($activity_id)
    {
        $sql = "UPDATE activities SET is_deleted = 1
                WHERE is_deleted = 0 AND activity_id = ?;";
        $values = [$activity_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }
}
