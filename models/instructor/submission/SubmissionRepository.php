<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/instructor/submission/SubmissionTemplate.php');

class SubmissionRepository implements SubmissionTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
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

}
