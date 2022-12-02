<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/submission/SubmissionTemplate.php');

class SubmissionRepository implements SubmissionTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function find_submission($student_id, $item_id)
    {
        $sql = "SELECT * FROM submissions WHERE student_id = ? AND item_id = ? ;";
        $values = [$student_id, $item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_submission($submission_id)
    {
        $sql = "SELECT * FROM submissions WHERE submission_id = ?;";
        $values = [$submission_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_submissions($item_id)
    {
        $sql = "SELECT * FROM submissions as SU
        JOIN students as ST  ON ST.student_id = SU.student_id WHERE SU.item_id = ?;";
        $values = [$item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function add_submission($submission_data, $student_id)
    {
        $sql = 'INSERT INTO submissions (code, item_id, student_id, score, test_result) VALUES (?, ?, ?, ?, ?);';
        $values = [$submission_data->code, $submission_data->item_id, $student_id, $submission_data->score, $submission_data->test_result];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function update_student_submission($submission_data, $student_id)
    {
        $sql = "UPDATE submissions SET code = ?, score = ?, test_result = ?
                WHERE item_id = ? AND student_id = ?;";
        $values = [$submission_data->code, $submission_data->score, $submission_data->test_result, $submission_data->item_id, $student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }
}
