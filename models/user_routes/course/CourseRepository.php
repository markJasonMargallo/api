<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/course/CourseTemplate.php');

class CourseRepository implements CourseTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_course($course_data, $instructor_id)
    {
        $sql = "INSERT INTO courses (course_uuid, course_name, course_code, instructor_id)
        VALUES (uuid(),?,?,?)";
        $values = [$course_data->course_name, $course_data->course_code, $instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function get_course($uuid)
    {
        $sql = "SELECT * FROM courses WHERE is_deleted = 0 AND course_uuid = ?;";
        $values = [$uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD); 
    }

    public function get_instructor_courses($instructor_id)
    {
        $sql = "SELECT * FROM courses as C JOIN instructors as I ON C.instructor_id = I.instructor_id  WHERE C.is_deleted = 0 AND C.instructor_id = ?;";
        $values = [$instructor_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function update_course($course_data)
    {
        $sql = "UPDATE courses SET course_name = ?, course_code = ?
                WHERE is_deleted = 0 AND course_uuid = ?;";
        $values = [$course_data->course_name,$course_data->course_code, $course_data->course_uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_course($uuid)
    {
        $sql = "UPDATE courses SET is_deleted = 1
                WHERE is_deleted = 0 AND course_uuid = ?;";
        $values = [$uuid];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }
}
