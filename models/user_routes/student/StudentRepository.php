<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/student/StudentTemplate.php');

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
        $sql = "SELECT S.student_id, S.first_name, S.last_name, S.middle_name, S.program, S.year_level, S.block, A.email
                FROM students as S JOIN accounts as A ON S.account_id =  A.account_id WHERE S.program = ? AND S.year_level = ? AND S.block = ?;";

        $values = [$room_data->program, $room_data->year_level, $room_data->block];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function get_student_id($account_id){

        $sql = "SELECT student_id FROM students S JOIN accounts A ON S.account_id = A.account_id WHERE student_id account_id = ?";
        $values = [$$account_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

}
