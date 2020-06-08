<?php


namespace app\Models;


class Like extends Model
{
    public static function create($conn, $userId, $noteId) {
        $sql = "INSERT INTO likes (user_id, note_id) VALUES (:userId, :noteId)";

        $stm = $conn->prepare($sql);
        return $stm->execute([
           ':userId' => $userId,
           ':noteId' => $noteId
        ]);
    }

    public static function getLikes($conn, $noteId) {
        $sql = "SELECT COUNT(*) AS total
                FROM `likes`  WHERE note_id = :noteId";

        $stm = $conn->prepare($sql);
        $stm->execute([':noteId' => $noteId]);

        return $stm->fetchColumn();
    }

    public static function countLike($conn, $userId, $noteId) {
        $sql = "SELECT COUNT(*) AS total FROM likes 
                WHERE user_id = :userId AND note_id = :noteId";

        $stm = $conn->prepare($sql);

        $stm->execute([
            ':noteId' => $noteId,
            ':userId' => $userId
        ]);

        return $stm->fetchColumn() > 0 ? true : false;
    }
}