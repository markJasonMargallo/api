<?php

require_once('./models/user_routes/test/TestRepository.php');
require_once('./models/user_routes/test/TestTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class TestService implements TestTemplate
{
    private TestRepository $test_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->test_repository = new TestRepository();
    }

    public function add_test($test_data, $item_id=null)
    {
        if ($this->test_repository->add_test($test_data)) {
            return response(['message' => 'Code saved.'], 200);
        }
    }

    public function add_tests($tests, $item_id)
    {
        foreach ($tests as $test) {
            $this->test_repository->add_test($test, $item_id);
        }
    }

    public function update_test($test_data)
    {
        if ($this->test_repository->update_test($test_data)) {
            return response(["message" => "Test updated."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }

    public function get_tests($item_id)
    {
        return response($this->test_repository->get_tests($item_id), 200);
    }
}
