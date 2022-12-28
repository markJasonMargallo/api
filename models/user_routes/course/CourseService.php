<?php

require_once('./models/user_routes/course/CourseRepository.php');
require_once('./models/user_routes/course/CourseTemplate.php');
require_once('./models/auth/AuthService.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class CourseService implements CourseTemplate
{
    private CourseRepository $course_repository;
    private AuthService $auth_service;
    private Validation $validator;

    public function __construct()
    {

        $this->auth_repository = new AuthRepository();
        $this->validator = new Validation();
        $this->auth_service = new AuthService();
        $this->course_repository = new CourseRepository();
    }

    public function add_course($course_data, $instructor_id)
    {

        if ($this->course_repository->add_course($course_data, $instructor_id) > 0) {
            return response(['message' => 'Course created successfully'], 200);
        }

        // if ($this->validator->is_body_valid($room_data, './schemas/room_schema.json')) {

           
        // } else {
        //     return $this->validator->invalid_body();
        // }
    }

    public function get_course($uuid)
    {
        return response($this->course_repository->get_course($uuid), 200);
    }

    public function get_instructor_courses($instructor_id)
    {
        return response($this->course_repository->get_instructor_courses($instructor_id), 200);
    }

    public function update_course($course_data)
    {
        if($this->course_repository->update_course($course_data)){
            return response(["message" => "Course updated."], 200);
        }else{
            return response(["message" => "Nothing to update."], 400);
        }
    }

    public function delete_course($uuid)
    {
        if ($this->course_repository->delete_course($uuid)) {
            return response(["message" => "Course deleted."], 200);
        } else {
            return response(["message" => "Resource not found."], 404);
        }
    }
}
