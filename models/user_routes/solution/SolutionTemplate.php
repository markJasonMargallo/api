<?php
interface SolutionTemplate
{
    public function add_code($solution_data);

    public function update_code($solution_data);

    public function get_code($solution_id);
}