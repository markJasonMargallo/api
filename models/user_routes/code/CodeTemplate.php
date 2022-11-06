<?php
interface CodeTemplate
{
    public function add_code($code_data, $student_id);

    public function update_code($code_data, $student_id);

    public function get_code($code_id);

}
