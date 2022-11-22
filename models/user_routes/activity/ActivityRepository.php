<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/activity/ActivityTemplate.php');

class ActivityRepository implements ActivityTemplate
{


    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_activity($room_id, $activity_data)
    {
        $sql = 'INSERT INTO activities (activity_name, activity_description, room_id, deadline, language_specified) 
        VALUES (?, ?, ?, ?, ?);';

        $values = [
            $activity_data->activity_name,
            $activity_data->activity_description,
            $activity_data->room_id,
            $activity_data->deadline,
            $activity_data->language_specified,
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
        $sql = "SELECT * FROM activities WHERE is_deleted = 0 AND activity_name LIKE CONCAT('%',?,'%');;";
        $values = [$search_string];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SEARCH_RECORDS);
    }

    public function get_activities($room_id, $instructor_id)
    {
        $sql = "SELECT * FROM activities AS A JOIN rooms AS R ON A.room_id = R.room_id
                WHERE A.is_deleted = 0 AND A.room_id = ? AND R.instructor_id = ?;";
        $values = [$room_id, $instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function update_activity($activity_data)
    {
        echo json_encode($activity_data);
        $sql = "UPDATE activities SET activity_name = ?, activity_description = ?, deadline = ?, language_specified = ?
                WHERE activity_id = ? AND is_deleted = 0;";

        $values = [
            $activity_data->activity_name,
            $activity_data->activity_description,
            $activity_data->deadline,
            $activity_data->language_specified,
            $activity_data->activity_id
        ];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_activity($activity_id)
    {
        $sql = "UPDATE activities SET is_deleted = 1
                WHERE is_deleted = 0 AND activity_id = ?;";
        $values = [$activity_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function get_activities_by_block($student_data){

        $sql = "SELECT * FROM activities AS A JOIN rooms AS R ON A.room_id = R.room_id
                WHERE A.is_deleted = 0 AND  R.is_deleted = 0 AND R.program = ? AND R.year_level = ? AND R.block = ?";
        $values = [$student_data->program, $student_data->year_level, $student_data->block];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function get_activities_by_room($room_id){
        $sql = "SELECT * FROM activities AS A JOIN rooms AS R ON A.room_id = R.room_id
                WHERE A.is_deleted = 0 AND  R.is_deleted = 0 AND R.room_id = ?";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }


}
