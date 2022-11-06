<?php

require_once('./models/user_routes/code/CodeRepository.php');
require_once('./models/user_routes/code/CodeTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class CodeService implements CodeTemplate
{
    private CodeRepository $submission_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->submission_repository = new CodeRepository();
    }

    public function add_code($code_data, $student_id){
        if ($this->submission_repository->add_code($code_data, $student_id)) {
            return response(['message' => 'Code saved.'], 200);
        }
    }

    public function update_code($code_data, $student_id){
        if ($this->submission_repository->update_code($code_data, $student_id)) {
            return response(["message" => "Code updated."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }

    public function get_code($code_id){
        return response($this->submission_repository->get_code($code_id), 200);
    }

}
