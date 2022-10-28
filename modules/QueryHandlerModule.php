<?php
require_once('./enums/QueryTypes.php');
require_once('./config/Config.php');

class QueryHandlerModule
{

    private PDO $pdo;
    private Connection $connection;


    public function __construct()
    {
        $this->connection = new Connection();
        $this->pdo = $this->connection->connect();
    }

    /**
     * @param QueryTypes $query_type the type of query to be executed
     * @param  string $sql the string sql query
     * @param  array $values the values to be mapped against the sql query
     */
    public function handle_query(string $sql, array $values, QueryTypes $query_type)
    {
        $output = null;

        try {
            $sql = $this->pdo->prepare($sql);
            $sql->execute($values);

            switch ($query_type) {
                case QueryTypes::FIND_RECORD_EXISTENCE:
                    $res = $sql->fetch();
                    $output = ($res) ? true : false;
                    break;
                case QueryTypes::SELECT_RECORD:
                    $user = $sql->fetch();
                    $output = ($user) ? $user : null;
                    break;
                case QueryTypes::ADD_RECORD_GET_ID:
                    $output = $this->pdo->lastInsertId();
                    break;
                case QueryTypes::ADD_RECORD:
                    $output = ($this->pdo->lastInsertId() > 0) ? true : false;
                    break;
                case QueryTypes::UPDATE_RECORD:
                    $output = ($sql->rowCount() > 0) ? true : false;
                    break;
            }
        } catch (\PDOException $e) {

            return response(['message' => $e->getMessage()], 400);
        }
        return $output;
    }
}
