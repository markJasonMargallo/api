<?php

require_once('./models/instructor/submission/SubmissionRepository.php');
require_once('./models/instructor/submission/SubmissionTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class SubmissionService implements SubmissionTemplate
{
    private SubmissionRepository $item_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->item_repository = new SubmissionRepository();
    }

    public function get_submission($submission_id)
    {
        return response($this->item_repository->get_submission($submission_id), 200);
    }

    public function get_submissions($item_id)
    {
        return response($this->item_repository->get_submissions($item_id), 200);
    }

}
