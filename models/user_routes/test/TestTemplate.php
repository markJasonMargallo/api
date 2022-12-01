<?php
interface TestTemplate
{
    public function add_test($test_data, $item_id=null);

    public function update_test($test_data);

    public function get_tests($item_id);
}
