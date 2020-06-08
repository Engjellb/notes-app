<?php


namespace app\Models;


class Like extends Model
{
    public static function create($conn, $userId, $noteId) {
        $sql = "INSERT INTO likes (user_id, note_id) VALUES ('".$userId."', '".$noteId."')";

        return $conn->query($sql);
    }

    public static function getLikes($conn, $noteId) {
        $sql = "SELECT COUNT(*) AS total
                FROM `likes`  WHERE note_id = ".$noteId;

        $result = $conn->query($sql);

            $likes = $result->fetch_object();
            return $likes->total;
    }

    public static function countLike($conn, $userId, $noteId) {
        $sql = "SELECT COUNT(*) AS total FROM likes 
                WHERE user_id = '".$userId."' AND note_id = '".$noteId."'";

        $result = $conn->query($sql);
        $like = $result->fetch_object();

        if($like->total > 0) {
            return true;
        }
        return false;
    }
}