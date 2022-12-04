<?php
require_once("./models/compiler/ExecutionService.php");
require_once("./models/user_routes/test/TestRepository.php");

class CodeTestingService
{
    private Middleware $middleware;
    private TestRepository $test_repository;
    private int $item_id;
    private $execution_data;
    private $testing_conditions = array();
    private string $method;

    public function __construct($method, $request_body, Middleware $middleware)
    {
        $this->middleware = $middleware;
        $this->method = $method;
        $this->test_repository = new TestRepository();
        $this->item_id = $request_body->item_id;
        $this->execution_data = $request_body->execution_data;

        $this->testing_conditions = $this->test_repository->get_tests($this->item_id);
    }

    public function run_tests()
    {
        $is_instructor = $this->middleware->is_instructor()? true: false;
        $results = array();
        $score = 0;
        $total_score = 0;
        $test_id = 0;

        foreach ($this->testing_conditions as $condition) {

            $test_id+=1;

            $modified_body = array(
                "code" => $this->execution_data->code,
                "language" => $this->execution_data->language,
                "input" => $condition['input']
            );

            $execution_service = new ExecutionService($this->method, $modified_body);
            $output = json_decode($execution_service->run_code());

            if (!$output->success) {
                echo(json_encode($output));
                return;
            }

            $test_status = strcmp($condition['output'], $output->output) == 0 ? "success" : "fail";

            if ($test_status == "success") {
                $score += $condition['points'];
            }

            $total_score += $condition['points'];

            if ($condition['is_visible'] > 0 || $is_instructor) {
                $rest = array(
                    "hidden" => false,
                    "id" => $test_id,
                    "input" => $condition['input'],
                    "output" => $output->output,
                    "expected_output" => $condition['output'],
                    "status" => $test_status,
                );
            } else {
                $rest = array(
                    "hidden" => true,
                    "id" => $test_id,
                    "input" => "hidden",
                    "output" => $output->output,
                    "expected_output" => "hidden",
                    "status" => $test_status,
                );
            }
            array_push($results, $rest);
        }

        echo (json_encode(response(["score" => $score, "total_score" => $total_score, "test_results" => $results], 200)));
    }
}