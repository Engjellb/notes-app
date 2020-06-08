<?php

namespace app\Models;

class Favorite extends Model
{
    public static function create($conn, $userId, $noteId) {
        $sql = "INSERT INTO favorites (user_id, note_id) VALUES (:userId, :noteId)";

        $stm = $conn->prepare($sql);

        return $stm->execute([
           ':userId' => $userId,
           ':noteId' => $noteId
        ]);
    }

    public static function getFavorites($conn, $userId) {
        $sql = "SELECT favorites.user_id, favorites.note_id, notes.id AS noteId, 
                notes.title, notes.content
                FROM favorites INNER JOIN users 
                ON users.id = favorites.user_id INNER JOIN notes 
                ON notes.id = favorites.note_id WHERE users.id = :userId";

//        echo $sql;die();
        $stm = $conn->prepare($sql);
        $stm->execute([':userId' => $userId]);

        $favorites = [];
        while ($favorite = $stm->fetch(\PDO::FETCH_OBJ)) {
            $favorites[] = $favorite;
        }
        return $favorites;
    }

    public static function countFavorite($conn, $userId, $noteId) {
        $sql = "SELECT COUNT(*) AS total FROM favorites 
                WHERE user_id = :userId AND note_id = :noteId";

        $stm = $conn->prepare($sql);

        $stm->execute([
           ':userId' => $userId,
           ':noteId' => $noteId
        ]);

        return $stm->fetchColumn() > 0 ? true : false;
    }
}