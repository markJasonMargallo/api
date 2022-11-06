<?php
require_once('./modules/QueryHandlerModule.php');
require_once('./models/user_routes/item/ItemTemplate.php');

class ItemRepository implements ItemTemplate
{

    private QueryHandlerModule $query_handler;

    public function __construct()
    {
        $this->query_handler = new QueryHandlerModule();
    }

    public function add_item($item_data)
    {
        $sql = 'INSERT INTO items (item_instruction, item_score, activity_id) VALUES (?, ?, ?);';
        $values = [$item_data->item_instruction, $item_data->item_score, $item_data->activity_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::ADD_RECORD);
    }

    public function get_item($item_id)
    {
        $sql = "SELECT * FROM items WHERE is_deleted = 0 AND item_id = ?;";
        $values = [$item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_RECORD);
    }

    public function get_items($activity_id)
    {
        $sql = "SELECT * FROM items WHERE is_deleted = 0 AND activity_id = ?;";
        $values = [$activity_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::SELECT_MULTIPLE_RECORDS);
    }

    public function update_item($item_data)
    {
        $sql = "UPDATE items SET item_instruction = ?, item_score = ?
                WHERE is_deleted = 0 AND activity_id = ? AND item_id = ?;";
        $values = [$item_data->item_instruction, $item_data->item_score, $item_data->activity_id, $item_data->item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

    public function delete_item($item_id)
    {
        $sql = "UPDATE items SET is_deleted = 1
                WHERE is_deleted = 0 AND item_id = ?;";
        $values = [$item_id];

        return $this->query_handler->handle_query($sql, $values, QueryTypes::UPDATE_RECORD);
    }

}
