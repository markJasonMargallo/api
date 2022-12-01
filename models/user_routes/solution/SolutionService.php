<?php

require_once('./models/user_routes/solution/SolutionRepository.php');
require_once('./models/user_routes/solution/SolutionTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class SolutionService implements SolutionTemplate
{
    private SolutionRepository $solution_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->solution_repository = new SolutionRepository();
    }

    public function add_code($solution_data)
    {
        if ($this->solution_repository->add_code($solution_data)) {
            return response(['message' => 'Code saved.'], 200);
        }
    }

    public function update_code($solution_data)
    {
        if ($this->solution_repository->update_code($solution_data)) {
            return response(["message" => "Code updated."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }

    public function get_code($solution_id)
    {
        return response($this->solution_repository->get_code($solution_id), 200);
    }

    public function add_test($solution_data)
    {
        if ($this->solution_repository->add_test($solution_data)) {
            return response(["message" => "Test added."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }
}
