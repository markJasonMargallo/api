<?php
interface SubmissionTemplate
{
    public function add_submission($submission_data, $student_id);

    public function update_student_submission($submission_data, $student_id);

    public function get_submission($submission_id);

    public function get_submissions($item_id);
}
