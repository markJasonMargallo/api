<?php
require_once('./models/user_routes/activity/ActivityRepository.php');
require_once('./models/user_routes/activity/ActivityTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');
require_once('./models/user_routes/room/RoomRepository.php');
require_once('./models/user_routes/item/ItemRepository.php');
require_once('./models/user_routes/student/StudentRepository.php');
require_once('./models/user_routes/submission/SubmissionRepository.php');
require_once('./models/user_routes/test/TestRepository.php');

class ActivityService implements ActivityTemplate
{
    private TestRepository $test_repository;
    private SubmissionRepository $submission_repository;
    private StudentRepository $student_repository;
    private ItemRepository $item_repository;
    private RoomRepository $room_repository;
    private ActivityRepository $activity_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->test_repository = new TestRepository();
        $this->submission_repository = new SubmissionRepository();
        $this->student_repository = new StudentRepository();
        $this->item_repository = new ItemRepository();
        $this->room_repository = new RoomRepository();
        $this->activity_repository = new ActivityRepository();
        $this->validator = new Validation();
    }

    public function add_activity($activity_data)
    {
        if ($this->activity_repository->add_activity($activity_data)) {
            return response(['message' => 'Activity Added Successfully.'], 200);
        }
    }

    public function get_activity($activity_id)
    {
        return response($this->activity_repository->get_activity($activity_id), 200);
    }

    public function search_activity($search_string)
    {
        return response($this->activity_repository->search_activity($search_string), 200);
    }

    public function get_activities($room_id, $instructor_id)
    {
        return response($this->activity_repository->get_activities($room_id, $instructor_id), 200);
    }

    public function update_activity($activity_data)
    {
        if ($this->activity_repository->update_activity($activity_data)) {
            return response(['message' => 'Activity updated.'], 200);
        } else {
            return response(['message' => 'Nothing to update.'], 400);
        }
    }

    public function delete_activity($activity_id)
    {
        if ($this->activity_repository->delete_activity($activity_id)) {
            return response(['message' => 'Activity deleted.'], 200);
        } else {
            return response(['message' => 'Resource not found.'], 400);
        }
    }

    public function get_activities_by_block($student_data)
    {
        return response($this->activity_repository->get_activities_by_block($student_data), 200);
    }

    public function get_activities_by_room($room_id)
    {
        return response($this->activity_repository->get_activities_by_room($room_id), 200);
    }


    public function get_activities_by_instructor($instructor_id)
    {
        $result = array();
        $rooms = $this->room_repository->get_instructor_rooms($instructor_id);

        //get activities per room
        foreach ($rooms as $room) {

            $activities = $this->activity_repository->get_activities_by_room($room['room_id']);

            if ($activities) {
                foreach ($activities as $key => $activity) {

                    $items = $this->item_repository->get_items($activity['activity_id']);

                    if ($items) {
                        $acts[$key]['items'] = $items;
                    }
                }
                array_push($result, ['room_name' => $room['room_name'], 'activities' => $activities]);
            }
        }
        return response($result, 200);
    }

    public function get_activities_by_student($request_body)
    {
        $result = array();
        $rooms = $this->room_repository->get_student_rooms($request_body);
        
        //get activities per room
        foreach ($rooms as $room) {
            
            $activities = $this->activity_repository->get_activities_by_room($room['room_id']);

            if ($activities) {
                
                foreach ($activities as $key => $activity) {

                    $total_score = 0;
                    $score = 0;
                    $item_count = 0;
                    $submission_count = 0;

                    $items = $this->item_repository->get_items($activity['activity_id']);

                    if ($items) {

                        foreach ($items as $item) {
                            $item_count += 1;
                            $total_score += $item['item_score'];
                            $submission = $this->submission_repository->get_student_submission($item['item_id'], $request_body->student_id);
                            if($submission){
                                $submission_count += 1;
                                $score += $submission['score'];
                            }
                        }
                    }

                    $activities[$key]['progress'] = $submission_count."/".$item_count;
                    $activities[$key]['total_score'] = $total_score;
                    $activities[$key]['score'] = $score;
                }
                array_push($result, ['room_name' => $room['room_name'], 'activities' => $activities]);
            }
        }

        return response($result, 200);
    }

    public function get_activity_summary($request_body)
    {

        $submission_summary = array();

        $students = $this->student_repository->get_students($request_body->room_data);
        $items = $this->item_repository->get_items($request_body->activity_id);

        foreach ($students as $student) {

            $activity_total_score = 0;
            $activity_score = 0;

            $item_count = 0;
            $scores = array();

            foreach ($items as $item) {
                $activity_total_score += $item["item_score"];

                $item_count += 1;
                $score  = $this->submission_repository->get_submission_score($item['item_id'], $student['student_id']);

                if ($score) {
                    $activity_score += $score["score"];
                }

                $score_value = $score ? $score["score"] : 0;

                array_push($scores, ["$item_count" => ["score" => $score_value, "total" => $item["item_score"]]]);
            }

            array_push($submission_summary, ["score" => $activity_score, "total_score" => $activity_total_score, "student_id" => $student['student_id'], "student_name" => $student['first_name'] . " " . $student['middle_name'][0] . ". " . $student['last_name'], "scores" => $scores]);
        }

        return response($submission_summary, 200);
    }

    public function create_activity($request_body)
    {

        $items = $request_body->items;
        $activity_id = $this->activity_repository->add_activity($request_body);

        foreach ($items as $item) {

            $testing_conditions = $item->testing_conditions;
            $item_id = $this->item_repository->add_item_by_id($item, $activity_id);
            // echo(json_encode($item_id."\n"));

            foreach ($testing_conditions as $testing_condition) {
                $this->test_repository->add_test_by_item_id($testing_condition, $item_id);
            }

            // echo(json_encode($testing_conditions = $item->testing_conditions));
        }

        return response(["message" => "Activity Successfully created."], 200);
    }
}
