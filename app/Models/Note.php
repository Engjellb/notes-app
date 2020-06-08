<?php

namespace app\Models;

use League\CommonMark\CommonMarkConverter;

class Note extends Model
{
    public static function create($conn, $title, $content, $categoryId)
    {
        $sql = "INSERT INTO notes (title, content, category_id) VALUES ('" . $title . "', '" . $content . "', " . $categoryId . ")";

        return $conn->query($sql);
    }

    public static function update($conn, $title, $content, $categoryId, $noteId)
    {
        $sql = "UPDATE notes SET title = '" . $title . "', content = '" . $content . "', category_id = '" . $categoryId . "'
                WHERE id = " . $noteId;
//        echo $sql;die();
        return $conn->query($sql);
    }

    public static function getNotes($conn, $limit = 4, $offset = 0)
    {
        $sql = "SELECT notes.id, notes.title, categories.title AS categoryTitle, notes.content, notes.created_at 
                FROM `notes` LEFT JOIN categories 
                ON notes.category_id = categories.id 
                ORDER BY notes.created_at DESC
                LIMIT " . $limit . " OFFSET " . $offset;

        $result = $conn->query($sql);

        $converter = new CommonMarkConverter();

        $notes = [];
        if ($result->num_rows > 0) {
            while ($note = $result->fetch_object()) {
                $notes[] = ['id' => $note->id, 'title' => $note->title,
                            'categoryTitle' => $note->categoryTitle, 'content' => $converter->convertToHtml($note->content), 'created_at' => $note->created_at];
            }
            return $notes;
        }
        return null;
    }

    public static function countNotes($conn)
    {
        $sql = "SELECT COUNT(*) AS total FROM `notes` LEFT JOIN categories 
                ON notes.category_id = categories.id";

        $result = $conn->query($sql);
        $notes = $result->fetch_object();

        return $notes->total;
    }

    public static function countNotesBySearch($conn, $simpleSearch)
    {
        $sql = "SELECT COUNT(*) AS total FROM `notes` LEFT JOIN categories 
                ON notes.category_id = categories.id 
                WHERE notes.title LIKE '%" . $simpleSearch . "%' OR notes.content LIKE '%" . $simpleSearch . "%'";
//        echo $sql;die();
        $result = $conn->query($sql);
        $notes = $result->fetch_object();

        return $notes->total;
    }

    public static function allNotesBySearch($conn, $simpleSearch, $limit = 4, $offset = 0)
    {
        $sql = "SELECT notes.id, notes.title, categories.title AS categoryTitle, notes.content, notes.created_at 
                FROM `notes` LEFT JOIN categories 
                ON notes.category_id = categories.id 
                WHERE notes.title LIKE '%" . $simpleSearch . "%' OR notes.content LIKE '%" . $simpleSearch . "%'
                ORDER BY notes.created_at DESC
                LIMIT " . $limit . " OFFSET " . $offset;
//        echo $sql;die();
        $result = $conn->query($sql);

        $notes = [];
        if ($result->num_rows > 0) {
            while ($note = $result->fetch_object()) {
                $notes[] = $note;
            }
            return $notes;
        }
        return null;
    }

    public static function getNote($conn, $id)
    {
        $sql = "SELECT notes.id, notes.title, notes.content, categories.title AS categoryTitle, categories.id AS categoryId
                FROM notes LEFT JOIN categories 
                ON notes.category_id = categories.id WHERE notes.id = " . $id;
//        echo $sql;die();
        $result = $conn->query($sql);
        $note = $result->fetch_object();

        $converter = new CommonMarkConverter();

        return $result->num_rows > 0 ? $note = ['id' => $note->id, 'title' => $note->title,
            'categoryTitle' => $note->categoryTitle, 'content' => $converter->convertToHtml($note->content)] : null;
    }

    public static function getComments($conn, $id)
    {
        $sql = "SELECT users.username, comments.content 
                FROM `comments` INNER JOIN users ON comments.user_id = users.id
                INNER JOIN notes ON comments.note_id = notes.id WHERE notes.id = " . $id;

        $result = $conn->query($sql);

        $comments = [];
        if ($result->num_rows > 0) {
            while ($comment = $result->fetch_object()) {
                $comments[] = $comment;
            }
            return $comments;
        }
        return null;
    }

    public static function destroy($conn, $noteId)
    {
        $sql = "DELETE FROM notes WHERE id = " . $noteId;

        return $conn->query($sql);
    }

    public static function countNotesByAdvancedSearch($conn, $search, $categoryId)
    {
        $sql = "SELECT COUNT(*) AS total FROM (SELECT notes.id, notes.title, categories.id AS categoryId, 
                MATCH (notes.title, notes.content) AGAINST ('" . $search . "' IN BOOLEAN MODE) AS coeficient 
                FROM notes LEFT JOIN categories 
                ON notes.category_id = categories.id) AS tab2 WHERE coeficient > 0 ";

        if ($categoryId > 0) {
            $sql .= "AND categoryId = " . $categoryId;
        }

        $result = $conn->query($sql);

        $notes = $result->fetch_object();
        return $notes->total;
    }

    public static function NotesByAdvancedSearch($conn, $search, $categoryId, $limit = 4, $offset = 0)
    {
        $sql = "SELECT * FROM (SELECT notes.id, notes.title, notes.content, notes.created_at, categories.id AS categoryId, 
                categories.title AS categoryTitle,
                MATCH (notes.title, notes.content) AGAINST ('" . $search . "' IN BOOLEAN MODE) AS coeficient 
                FROM notes LEFT JOIN categories 
                ON notes.category_id = categories.id) AS tab2 WHERE coeficient > 0 ";
//        echo $sql;die();

        if ($categoryId > 0) {
            $sql .= "AND categoryId = " . $categoryId;
        }

        $sql .= " LIMIT ".$limit." OFFSET ".$offset;

        $result = $conn->query($sql);

        $notes = [];
        if ($result->num_rows > 0) {
            while ($note = $result->fetch_object()) {
                $notes[] = $note;
            }
            return $notes;
        }
        return null;
    }
}