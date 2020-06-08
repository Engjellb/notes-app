<?php

namespace app\Models;

class Favorite extends Model
{
    public static function create($conn, $userId, $noteId) {
        $sql = "INSERT INTO favorites (user_id, note_id) VALUES ('".$userId."', '".$noteId."')";

        return $conn->query($sql);
    }

    public static function getFavorites($conn, $userId) {
        $sql = "SELECT favorites.user_id, favorites.note_id, notes.id AS noteId, 
                notes.title, notes.content, notes.created_at 
                FROM favorites INNER JOIN users 
                ON users.id = favorites.user_id INNER JOIN notes 
                ON notes.id = favorites.note_id WHERE users.id = ".$userId;

//        echo $sql;die();
        $result = $conn->query($sql);

        $favorites = [];
        if($result->num_rows > 0) {
            while($favorite = $result->fetch_object()) {
                $favorites[] = $favorite;
            }
            return $favorites;
        }
        return null;
    }

    public static function countFavorite($conn, $userId, $noteId) {
        $sql = "SELECT COUNT(*) AS total FROM favorites 
                WHERE user_id = '".$userId."' AND note_id = '".$noteId."'";

        $result = $conn->query($sql);
        $favorite = $result->fetch_object();

        return $favorite->total > 0 ? true : false;
    }
}