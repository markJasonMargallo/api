<?php

require_once('./models/user_routes/student/StudentRepository.php');
require_once('./models/user_routes/student/StudentTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class StudentService implements StudentTemplate
{
    private StudentRepository $student_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->student_repository = new StudentRepository();
    }

    public function get_student($student_id)
    {
        return response($this->student_repository->get_student($student_id), 200);
    }

    public function get_students($student_data)
    {
        return response($this->student_repository->get_students($student_data), 200);
    }

}
