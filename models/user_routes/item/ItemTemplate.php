<?php
interface ItemTemplate
{
    public function add_item($item_data);

    public function get_item($item_id);

    public function get_items($activity_id);

    public function update_item($item_data);

    public function delete_item($item_id);
}
