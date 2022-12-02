<?php
require_once("./models/compiler/ExecutionService.php");
require_once("./models/user_routes/test/TestRepository.php");

class CodeTestingService
{

    private TestRepository $test_repository;
    private int $item_id;
    private $execution_data;
    private $testing_conditions = array();
    private string $method;

    public function __construct($method, $request_body)
    {
        $this->method = $method;
        $this->test_repository = new TestRepository();
        $this->item_id = $request_body->item_id;
        $this->execution_data = $request_body->execution_data;

        $this->testing_conditions = $this->test_repository->get_tests($this->item_id);
    }

    public function run_tests()
    {
        $results = array();

        foreach ($this->testing_conditions as $condition) {

            $modified_body = array(
                "code" => $this->execution_data->code,
                "language" => $this->execution_data->language,
                "input" => $condition['input']
            );

            $execution_service = new ExecutionService($this->method, $modified_body);
            $output = json_decode($execution_service->run_code());

            if($condition['is_visible']>0){
                $rest = array(
                    "input" => $condition['input'],
                    "output" => $output->output,
                    "expected_output" => $condition['output'],
                    "status" => strcmp($condition['output'], $output->output) == 0 ? "success" : "fail",
                );
            }else{
                $rest = array(
                    // "input" => $condition['input'],
                    "output" => $output->output,
                    // "expected_output" => "hidden",
                    "status" => strcmp($condition['output'], $output->output) == 0 ? "success" : "fail",
                );
            }
            array_push($results, $rest);
        }
        echo(json_encode($results));
    }
}
