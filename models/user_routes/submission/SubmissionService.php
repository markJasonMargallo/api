<?php

require_once('./models/user_routes/submission/SubmissionRepository.php');
require_once('./models/user_routes/submission/SubmissionTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class SubmissionService implements SubmissionTemplate
{
    private SubmissionRepository $submission_repository;
    private Validation $validator;


    public function __construct()
    {
        $this->validator = new Validation();
        $this->submission_repository = new SubmissionRepository();
    }

    public function get_submission($submission_id)
    {
        return response($this->submission_repository->get_submission($submission_id), 200);
    }

    public function get_submissions($item_id)
    {
        return response($this->submission_repository->get_submissions($item_id), 200);
    }

    public function add_submission($submission_data, $student_id)
    {
        if ($this->submission_repository->add_submission($submission_data, $student_id)) {
            return response(['message' => 'Solution submitted.'], 200);
        }
    }

    public function update_student_submission($submission_data, $student_id)
    {
        if ($this->submission_repository->update_student_submission($submission_data,  $student_id)) {
            return response(["message" => "Solution updated."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }

}
