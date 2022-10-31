<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/instructor/room/RoomTemplate.php');

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
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND room_name LIKE CONCAT('%',?,'%');";
        $values = [$search_string];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SEARCH_MULTIPLE_RECORDS);

    }

    public function get_rooms($instructor_id)
    {
        $sql = "SELECT * FROM rooms WHERE is_deleted = 0 AND instructor_id = ?;";
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

    public function delete_room($room_id)
    {
        $sql = "UPDATE rooms SET is_deleted = 1
                WHERE is_deleted = 0 AND room_id = ?;";
        $values = [$room_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }
}
