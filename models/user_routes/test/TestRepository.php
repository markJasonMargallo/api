<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/test/TestTemplate.php');

class TestRepository implements TestTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_test($test_data, $item_id=null)
    {
        $sql = 'INSERT INTO tests (input, output, points, is_visible, item_id) VALUES (?, ?, ?, ?, ?);';
        $values = [$test_data->input, $test_data->output, $test_data->points, $test_data->is_visible, $test_data->item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function update_test($test_data)
    {
        $sql = "UPDATE tests SET input = ?, output = ?, points = ?, is_visible = ?
                WHERE test_id = ?;";

        $values = [$test_data->input, $test_data->output, $test_data->points, $test_data->is_visible, $test_data->test_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function get_tests($item_id)
    {
        $sql = "SELECT * FROM tests WHERE item_id = ? ORDER BY is_visible DESC;";
        $values = [$item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }
}
