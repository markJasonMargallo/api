<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/solution/SolutionTemplate.php');

class SolutionRepository implements SolutionTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }



    public function add_code($solution_data){

        $sql = 'INSERT INTO solutions (code, item_id, test_result) VALUES (?, ?, "");';
        $values = [$solution_data->code, $solution_data->item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function update_code($solution_data){

        $sql = "UPDATE solutions SET code = ?
                WHERE item_id = ?;";
        $values = [$solution_data->code, $solution_data->item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function get_code($solution_id){

        $sql = "SELECT * FROM solutions WHERE item_id = ?;";
        $values = [$solution_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function add_test($solution_data)
    {
        $sql = "UPDATE solutions SET test_result = ?
                WHERE item_id = ?;";
        $values = [$solution_data->result, $solution_data->item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }
}
