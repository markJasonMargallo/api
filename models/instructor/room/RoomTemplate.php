<?php
interface RoomTemplate
{
    public function add_room($room_name, $instructor_email);

    public function get_room($id);

    public function search_rooms($search_string);

    public function get_rooms($instructor_id);

    public function update_room($room_name, $room_id);

    public function delete_room($room_id);
}