<?php
interface CourseTemplate
{
    public function add_course($course_data, $instructor_email);

    public function get_course($uuid);

    public function get_instructor_courses($instructor_id);

    public function update_course($room_data);

    public function delete_course($room_id);
}