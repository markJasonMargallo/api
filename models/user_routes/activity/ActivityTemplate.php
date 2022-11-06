<?php
interface ActivityTemplate
{
    public function add_activity($room_id, $activity_details);

    public function get_activity($activity_id);

    public function search_activity($search_string);

    public function get_activities($room_id, $instructor_id);

    public function update_activity($activity_data);

    public function delete_activity($activity_id);

    public function get_activities_by_block($student_data);

    public function get_activities_by_room($room_id);

}
