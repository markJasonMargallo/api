<?php
require_once('./config/Config.php');

class AuthRepository{
    private PDO $pdo;
    private Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
        $this->pdo = $this->connection->connect();
    }

    public function get_user_by_email_address($email)
    {
        $sql = "SELECT a.account_id AS id , a.username, a.password
                FROM accounts AS a LEFT JOIN students AS s
                ON s.account_id = a.account_id
                WHERE a.username = ?";

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
}