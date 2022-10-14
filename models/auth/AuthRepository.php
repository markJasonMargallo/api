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

    public function get_student_account_by_username($username)
    {
        $sql = "SELECT a.account_id AS id , a.username, a.password, a.role
                FROM accounts AS a LEFT JOIN students AS s
                ON s.account_id = a.account_id
                WHERE a.role = 'student' AND a.is_deleted = 0 AND a.username = ?";


        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $username,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function get_instructor_account_by_username($username)
    {
        $sql = "SELECT a.account_id AS id , a.username, a.password, a.role
                FROM accounts AS a LEFT JOIN teachers AS t
                ON t.accounts_account_id = a.account_id
                WHERE a.role = 'instructor' AND a.is_deleted = 0 AND a.username = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $username,
        ]);

        $user = $sql->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function get_account_by_id($id)
    {
        $sql = "SELECT a.account_id AS id , a.username, a.role
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
