<?php
require_once("./models/compiler/ExecutionService.php");
require_once("./models/user_routes/test/TestRepository.php");
require_once("./models/user_routes/submission/SubmissionService.php");

class CodeSubmissionService
{
    private Middleware $middleware;
    private SubmissionService $submission_service;
    private TestRepository $test_repository;
    private int $item_id;
    private $execution_data;
    private $testing_conditions = array();
    private string $method;
    private $request_body;

    public function __construct($method, $request_body, Middleware $middleware)
    {
        $this->request_body = $request_body;
        $this->submission_service = new SubmissionService();
        $this->middleware = $middleware;
        $this->submission_repository = new SubmissionRepository();
        $this->method = $method;
        $this->test_repository = new TestRepository();
        $this->item_id = $request_body->item_id;
        $this->execution_data = $request_body->execution_data;

        $this->testing_conditions = $this->test_repository->get_tests($this->item_id);
    }

    public function run_tests()
    {
        $results = array();
        $score = 0;
        $total_score = 0;

        foreach ($this->testing_conditions as $condition) {

            $points = $condition['points'];

            $modified_body = array(
                "code" => $this->execution_data->code,
                "language" => $this->execution_data->language,
                "input" => $condition['input']
            );

            $execution_service = new ExecutionService($this->method, $modified_body);
            $output = json_decode($execution_service->run_code());

            if(!$output->success){
                echo("Your code is erroneous");
                return;
            }

            $test_status = strcmp($condition['output'], $output->output) == 0 ? "success" : "fail";

            if ($test_status == 'success') {
                $score += $points;
            }

            $total_score += $points;

            if ($condition['is_visible'] > 0) {
                $rest = array(
                    "input" => $condition['input'],
                    "output" => $output->output,
                    "expected_output" => $condition['output'],
                    "status" => $test_status,
                );
            } else {
                $rest = array(
                    "input" => "hidden",
                    "output" => $output->output,
                    "expected_output" => "hidden",
                    "status" => $test_status,
                );
            }
            array_push($results, $rest);
        }

        $submission_payload = (object)[
            "item_id" => $this->item_id,
            "code" =>  $this->execution_data->code,
            "score" => $score,
            "test_result" => json_encode($results)
        ];


        $this->submission_service->add_submission($submission_payload, $this->middleware->get_owner_id());
        echo (json_encode(response(["score" => $score, "total_score" => $total_score, "test_results" => $results], 200)));
    }
}