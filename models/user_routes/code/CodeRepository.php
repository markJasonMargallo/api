<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/code/CodeTemplate.php');

class CodeRepository implements CodeTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }



    public function add_code($code_data, $student_id){

        $sql = 'INSERT INTO codes (code, item_id, student_id) VALUES (?, ?, ?);';
        $values = [$code_data->code, $code_data->item_id, $student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function update_code($code_data, $student_id){

        $sql = "UPDATE codes SET code = ?
                WHERE code_id = ? AND student_id = ?;";
        $values = [$code_data->code, $code_data->code_id, $student_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);

    }

    public function get_code($code_id){

        $sql = "SELECT * FROM codes WHERE code_id = ?;";
        $values = [$code_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);

    }


}
