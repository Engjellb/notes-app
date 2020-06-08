<?php

namespace app\Models;

class Category extends Model
{
    public static function getCategories($conn) {
        try {
            $sql = "SELECT * FROM categories";

            $stm = $conn->prepare($sql);
            $stm->execute();

            $categories = [];

            while ($category = $stm->fetch(\PDO::FETCH_OBJ)) {
                $categories[] = $category;
            }

            return $categories;

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }

    }

    public static function create($conn, $title) {
        try {
            $sql = "INSERT INTO categories (title) VALUES (:title)";

            $stm = $conn->prepare($sql);

            $c = $stm->execute([':title' => $title]);

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }

    public static function getNotes($conn, $categoryId) {
        try {
            $sql = "SELECT * FROM categories 
                INNER JOIN notes ON categories.id = notes.categoryId WHERE categories.id = :categoryId";

            $stm = $conn->prepare($sql);

            $stm->execute([':categoryId' => $categoryId]);
            $notes = [];

            while ($note = $stm->fetch(\PDO::FETCH_OBJ)) {
                $notes[] = $note;
            }
            return $notes;

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }

    public static function destroy($conn, $id) {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";

            $stm = $conn->prepare($sql);
            $stm->execute([':id' => $id]);

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }

    }

    public static function getCategory($conn, $id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";

            $stm = $conn->prepare($sql);

            $stm->execute([':id' => $id]);
            $category = $stm->fetch(\PDO::FETCH_OBJ);

            return $category ? $category : null;

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }

    public static function update($conn, $title, $id) {
        try {
            $sql = "UPDATE categories SET title = :title WHERE id = :id";

            $stm = $conn->prepare($sql);

            $stm->execute([
                ':title' => $title,
                ':id' => $id
            ]);

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }
}