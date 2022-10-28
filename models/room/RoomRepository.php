<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/room/RoomTemplate.php');

class RoomRepository implements RoomTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_room($room_name, $instructor_id)
    {
        $sql = 'INSERT INTO rooms (room_name, instructor_id) VALUES (?, ?);';
        $values = [$room_name, $instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function get_room($id)
    {
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD); 
    }

    public function search_rooms($search_string)
    {
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND room_name LIKE '%?%';";
        $values = [$search_string];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);

    }

    public function get_rooms_by_instructor($instructor_id)
    {
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND instructor_id = ?;";
        $values = [$instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function get_room_instructor($room_id)
    {
        $sql = "SELECT * FROM instructors AS I
                JOIN room AS R on R.instructor_id = I.instructor_id
                WHERE R.is_deleted = 0 AND R.instructor_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_rooms_by_student($student_id)
    {
        $sql = "SELECT * FROM room as R 
                JOIN room_participants as RP ON RP.room_id = R.room_id 
                WHERE R.is_deleted = 0 AND RP.student_id = ?;";
        $values = [$student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_room_activities($room_id)
    {
        $sql = "SELECT * FROM activities 
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function update_room_name($room_name, $room_id)
    {
        $sql = "UPDATE rooms SET room_name = ?
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_name, $room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_room($room_id)
    {
        $sql = "UPDATE rooms SET is_deleted = 1
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function get_room_participants($room_id){

        $sql = "SELECT * FROM room_participants as RP
                JOIN students AS S ON RP.student_id = S.student_id
                WHERE RP.room_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function add_room_participant($room_id, $student_id){

        $sql = 'INSERT INTO room_participants (room_id, student_id) VALUES (?, ?);';
        $values = [$room_id, $student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }
}
