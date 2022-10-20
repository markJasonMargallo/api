<?php

require_once('./modules/AuthModule.php');
require_once('./models/student/InstructorRepository.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class InstructorService
{
    private InstructorRepository $instructor_repository;

    public function __construct()
    {
        $this->instructor_repository = new InstructorRepository();
    }

    public function get_student_id($user_email)
    {
        return $this->instructor_repository->get_instructor($user_email)['instructor_id'];
    }
}
