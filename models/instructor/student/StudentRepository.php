<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/instructor/student/StudentTemplate.php');

class StudentRepository implements StudentTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function get_student($student_id)
    {
        $sql = "SELECT * FROM students WHERE student_id = ?;";
        $values = [$student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_students($room_data)
    {
        $sql = "SELECT * FROM students WHERE program = ? AND year_level = ? AND block = ?;";
        $values = [$room_data->program, $room_data->year_level, $room_data->block];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

}
