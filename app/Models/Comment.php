<?php


namespace app\Models;


class Comment extends Model
{
    public static function create($conn, $userId, $noteId, $content) {
        $sql = "INSERT INTO comments (user_id, note_id, content) 
                VALUES ('".$userId."', '".$noteId."', '".$content."')";

        return $conn->query($sql);
    }
}