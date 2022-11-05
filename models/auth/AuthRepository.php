<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./enums/QueryTypes.php');

class AuthRepository
{
    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function get_account($email)
    {
        $sql = "SELECT * FROM accounts WHERE email = ?";
        $values = [$email];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::FIND_RECORD_EXISTENCE);
    }

    public function add_account($credential, $role)
    {
        $sql = 'INSERT INTO accounts (email, password, role) VALUES (?, ?, ?)';
        $values = [$credential->email, password_hash($credential->password, PASSWORD_BCRYPT),$role];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function add_student($student_data, $account_id)
    {
        $sql = 'INSERT INTO students (first_name, last_name, middle_name, account_id, program, year_level, block) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $values = [
            $student_data->first_name, 
            $student_data->last_name, 
            $student_data->middle_name, 
            $account_id, 
            $student_data->program,
            $student_data->year_level,
            $student_data->block
        ];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function add_instructor($instructor_data, $account_id)
    {
        $sql = 'INSERT INTO instructors (first_name, last_name, middle_name, account_id) VALUES (?, ?, ?, ?)';
        $values = [
            $instructor_data->first_name, 
            $instructor_data->last_name, 
            $instructor_data->middle_name, 
            $account_id
        ];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD_GET_ID);
    }

    public function get_instructor($account_email)
    {
        $sql = "SELECT S.instructor_id FROM instructors as S
                JOIN accounts as A ON S.account_id = A.account_id
                WHERE A.email = ? AND A.role = 'instructor' AND A.is_deleted = 0;";

        $values = [$account_email];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_account_by_id($id)
    {
        $sql = "SELECT a.account_id AS id , a.email, a.role
                FROM accounts AS a WHERE a.is_deleted = 0 AND a.account_id= ?";
        $values = [$id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);  
    }

    public function get_instructor_account($email)
    {
        $sql = "SELECT account_id AS id, email, password, role
                FROM accounts WHERE role = 'instructor' AND is_deleted = 0 AND email = ?";
        $values = [$email];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_student_account($email)
    {
        $sql = "SELECT account_id AS id, email, password, role
                FROM accounts WHERE role = 'student' AND is_deleted = 0 AND email = ?";
        $values = [$email];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }
}
