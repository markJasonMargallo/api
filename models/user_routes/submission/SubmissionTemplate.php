<?php
interface SubmissionTemplate
{
    public function get_submission($submission_id);

    public function get_submissions($item_id);
}
