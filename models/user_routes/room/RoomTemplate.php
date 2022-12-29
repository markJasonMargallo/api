<?php
interface RoomTemplate
{
    public function add_room($room_name, $instructor_email);

    public function get_room($id);

    public function get_course_rooms($course_uuid);

    public function get_instructor_rooms($instructor_id);

    public function search_rooms($search_string);

    public function update_room($room_data);

    public function delete_room($room_uuid);

    public function get_student_rooms($room_data);
}