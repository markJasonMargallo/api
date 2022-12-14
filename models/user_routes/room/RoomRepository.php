<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/room/RoomTemplate.php');

class RoomRepository implements RoomTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_room($room_data, $instructor_id)
    {
        $sql = 'INSERT INTO rooms (room_uuid, year_level, program, block, course_id) VALUES (uuid(), ?, ?, ?, ?);';
        $values = [$room_data->year_level, $room_data->program, $room_data->block, $room_data->course_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function get_room($uuid)
    {
        $sql = "SELECT * FROM rooms R JOIN courses C ON R.course_id = C.course_id WHERE R.is_deleted = 0 AND R.room_uuid = ?;";
        $values = [$uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD); 
    }

    public function get_course_rooms($course_uuid)
    {
        $sql = "SELECT * FROM rooms R JOIN courses C ON R.course_id = C.course_id WHERE R.is_deleted = 0 AND C.course_uuid = ? ";
        $values = [$course_uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function search_rooms($search_string)
    {
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND room_name LIKE CONCAT('%',?,'%');";
        $values = [$search_string];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SEARCH_RECORDS);
    }

    public function get_instructor_rooms($instructor_id)
    {
        $sql = "SELECT * FROM rooms as R JOIN instructors as I ON R.instructor_id = I.instructor_id  WHERE R.is_deleted = 0 AND R.instructor_id = ?;";
        $values = [$instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function update_room($room_data)
    {
        $sql = "UPDATE rooms SET room_name = ?
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_data->room_name, $room_data->room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_room($room_uuid)
    {
        $sql = "UPDATE rooms SET is_deleted = 1
                WHERE is_deleted = 0 AND room_uuid = ?;";
        $values = [$room_uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function get_student_rooms($room_data)
    {
        $sql = "SELECT * FROM rooms as R JOIN instructors as I ON R.instructor_id = I.instructor_id  
        WHERE is_deleted = 0 AND program = ? AND year_level = ? AND block = ?;";
        
        $values = [$room_data->program, $room_data->year_level, $room_data->block ];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

}
