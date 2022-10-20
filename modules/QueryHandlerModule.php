<?php
require_once('./enums/QueryTypes.php');
require_once('./config/Config.php');

class QueryHandlerModule{

    protected PDO $pdo;
    protected Connection $connection;


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
    public function handle_query(string $sql, array $values, QueryTypes $query_type){

        $output = null;

        $sql = $this->pdo->prepare($sql);
        $sql->execute($values);

        switch ($query_type) {
            case QueryTypes::SELECT:
                
                break;
            case QueryTypes::INSERT:
                break;
            case QueryTypes::UPDATE:
                break;
        }

    }

}