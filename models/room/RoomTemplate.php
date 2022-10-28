<?php
interface RoomTemplate
{
    public function add_room($room_name, $instructor_email);

    public function get_room($id);

    public function search_rooms($search_string);

    public function get_rooms_by_instructor($instructor_id);

    public function get_room_instructor($room_id);

    public function get_rooms_by_student($student_id);

    public function get_room_activities($room_id);

    public function update_room_name($room_name, $room_id);

    public function delete_room($room_id);

    public function get_room_participants($room_id);

    public function add_room_participant($room_id, $student_id);
    
}