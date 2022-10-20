<?php
require_once('./config/Config.php');

class AuthRepository
{
    protected PDO $pdo;
    protected Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
        $this->pdo = $this->connection->connect();  
    }

    public function get_account($email){
        $sql = "SELECT * FROM accounts WHERE email = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $email,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return true;
        }

        return false;
    }

    public function add_account($credential, $role){
        $sql = 'INSERT INTO accounts (email, password, role) VALUES (?, ?, ?)';
        $sql = $this->pdo->prepare($sql);
        
        $sql->execute([
            $credential->email,
            password_hash($credential->password, PASSWORD_BCRYPT),
            $role
        ]);

        $account_id= $this->pdo->lastInsertId();

        return $account_id;
    }

    public function add_student($student_data, $account_id){
        $sql = 'INSERT INTO students (first_name, last_name, middle_name, account_id) VALUES (?, ?, ?, ?)';
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $student_data->first_name,
            $student_data->last_name,
            $student_data->middle_name,
            $account_id
        ]);

        $student_id = $this->pdo->lastInsertId();

        return $student_id;
    }

    public function add_instructor($instructor_data, $account_id){
        $sql = 'INSERT INTO instructors (first_name, last_name, middle_name, account_id) VALUES (?, ?, ?, ?)';
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $instructor_data->first_name,
            $instructor_data->last_name,
            $instructor_data->middle_name,
            $account_id,
        ]);

        $student_id = $this->pdo->lastInsertId();

        return $student_id;
    }

    public function get_student_account_by_email($email)
    {
        $sql = "SELECT a.account_id AS id , a.email, a.password, a.role
                FROM accounts AS a WHERE a.role = 'student' AND a.is_deleted = 0 AND a.email = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $email,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function get_instructor_account_by_email($email)
    {
        $sql = "SELECT a.account_id AS id, a.email, a.password, a.role
                FROM accounts as a WHERE a.role = 'instructor' AND a.is_deleted = 0 AND a.email = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $email,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function get_account_by_id($id)
    {
        $sql = "SELECT a.account_id AS id , a.email, a.role
                FROM accounts AS a WHERE a.is_deleted = 0 AND a.account_id= ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $id,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }
}
