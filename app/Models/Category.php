<?php

namespace app\Models;

class Category extends Model
{
    public static function getCategories($conn) {
        $sql = "SELECT * FROM categories ORDER BY created_at DESC";

        $result = $conn->query($sql);

        $categories = [];
        if($result->num_rows > 0) {
            while($category = $result->fetch_object()) {
                $categories[] = $category;
            }
            return $categories;
        }
        return null;
    }

    public static function create($conn, $title) {
        $sql = "INSERT INTO categories (title) VALUES ('".$title."')";

        $conn->query($sql);
    }

    public static function getNotes($conn, $categoryId) {
        $sql = "SELECT * FROM categories 
                INNER JOIN notes ON categories.id = notes.category_id WHERE categories.id = ".$categoryId;

        $result = $conn->query($sql);

        $notes = [];
        if($result->num_rows > 0) {
            while($note = $result->fetch_object()){
                $notes[] = $note;
            }
            return $notes;
        }
        return null;
    }

    public static function destroy($conn, $id) {
        $sql = "DELETE FROM categories WHERE id = ".$id;

        return $conn->query($sql);
    }

    public static function getCategory($conn, $id) {
        $sql = "SELECT * FROM categories WHERE id = ".$id;

        $result = $conn->query($sql);

        return $result->num_rows > 0 ? $category = $result->fetch_object() : null;
    }

    public static function update($conn, $title, $id) {
        $sql = "UPDATE categories SET title = '".$title."' WHERE id = ".$id;

        return $conn->query($sql);
    }
}