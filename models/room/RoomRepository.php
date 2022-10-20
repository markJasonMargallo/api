<?php
require_once('./config/Config.php');

class RoomRepository
{
    protected PDO $pdo;
    protected Connection $connection;

    public function __construct()
    {

        $this->connection = new Connection();
        $this->pdo = $this->connection->connect();
        
    }

    public function add_room($room_name, $instructor_id){
        $sql = 'INSERT INTO rooms (room_name, instructor_id) VALUES (?, ?)';
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $room_name,
            $instructor_id
        ]);

        $account_id= $this->pdo->lastInsertId();

        return $account_id;
    }

    public function get_room_by($id)
    {
        $sql = "SELECT * FROM room WHERE is_deleted = 0 AND room_id = ?";
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $id,
        ]);

        $room = $sql->fetch();

        if ($room) {
            return $room;
        }

        return null;
    }

    public function search_rooms($search_string)
    {
        $sql = "SELECT * FROM room WHERE is_deleted = 0 AND room_name LIKE '%?%'";
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $search_string,
        ]);

        $rooms = $sql->fetch();

        if ($rooms) {
            return $rooms;
        }

        return null;
    }

    public function get_rooms_by_instructor($instructor_id)
    {
        $sql = "SELECT * FROM room WHERE is_deleted = 0 AND instructor_id = ?";
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $instructor_id,
        ]);

        $rooms = $sql->fetch();

        if ($rooms) {
            return $rooms;
        }

        return null;
    }

    public function get_room_instructor($room_id)
    {
        $sql = "SELECT * FROM instructors AS I
                JOIN room AS R on R.instructor_id = I.instructor_id
                WHERE R.is_deleted = 0 AND R.instructor_id = ?"; 
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $room_id,
        ]);

        $rooms = $sql->fetch();

        if ($rooms) {
            return $rooms;
        }

        return null;
    }

    public function get_rooms_by_student($student_id)
    {
        $sql = "SELECT * FROM room as R 
                JOIN room_participants as RP ON RP.room_id = R.room_id 
                WHERE R.is_deleted = 0 AND RP.student_id = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $student_id,
        ]);

        $rooms = $sql->fetch();

        if ($rooms) {
            return $rooms;
        }

        return null;
    }

    public function get_room_activities($room_id)
    {
        $sql = "SELECT * FROM activities 
                WHERE is_deleted = 0 AND room_id = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $room_id,
        ]);

        $rooms = $sql->fetch();

        if ($rooms) {
            return $rooms;
        }

        return null;
    }

    public function update_room_name($room_name, $room_id)
    {
        $sql = "UPDATE rooms SET room_name = ?
                WHERE is_deleted = 0 AND room_id = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $room_name,
            $room_id
        ]);

        $COUNT = $sql->rowCount();

        return ['count' => $COUNT];
    }

    public function delete_room($room_id)
    {
        $sql = "UPDATE rooms SET is_deleted = 1
                WHERE is_deleted = 0 AND room_id = ?";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $room_id
        ]);

        $COUNT = $sql->rowCount();

        return ['count' => $COUNT];
    }

}