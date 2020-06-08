<?php

namespace app\Models;

use League\CommonMark\CommonMarkConverter;

class Note extends Model
{
    public static function create($conn, $title, $content, $categoryId)
    {
        try {
            $sql = "INSERT INTO notes (title, content, categoryId) 
                    VALUES (:title, :content, :categoryId)";

            $stm = $conn->prepare($sql);

            $stm->execute([
               ':title' => $title,
               ':content' => $content,
               ':categoryId' => $categoryId
            ]);

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }

    }

    public static function update($conn, $title, $content, $categoryId, $noteId)
    {
        $sql = "UPDATE notes SET title = :title, content = :content, categoryId = :categoryId
                WHERE id = :noteId";
//        echo $sql;die();
        $stm = $conn->prepare($sql);

        $stm->execute([
            ':title' => $title,
            ':content' => $content,
            ':categoryId' => $categoryId,
            ':noteId' => $noteId
        ]);
    }

    public static function getNotes($conn, $limit = 4, $offset = 0)
    {
        $sql = "SELECT notes.id, notes.title, categories.title AS categoryTitle, notes.content
                FROM `notes` LEFT JOIN categories 
                ON notes.categoryId = categories.id 
                ORDER BY notes.id DESC
                LIMIT :limit OFFSET :offset";

        $stm = $conn->prepare($sql);

        $stm->execute([
           ':limit' => $limit,
           ':offset' => $offset
        ]);

        $converter = new CommonMarkConverter();

        $notes = [];

        while ($note = $stm->fetch(\PDO::FETCH_OBJ)) {
            $notes[] = ['id' => $note->id, 'title' => $note->title,
                'categoryTitle' => $note->categoryTitle, 'content' => $converter->convertToHtml($note->content)];
        }
        return $notes;
    }

    public static function countNotes($conn)
    {
        $sql = "SELECT COUNT(*) AS total FROM `notes` LEFT JOIN categories 
                ON notes.categoryId = categories.id";

        $stm = $conn->prepare($sql);
        $stm->execute();

        return $stm->fetchColumn();
    }

    public static function countNotesBySearch($conn, $simpleSearch)
    {
        $sql = "SELECT COUNT(*) AS total FROM `notes` LEFT JOIN categories 
                ON notes.categoryId = categories.id 
                WHERE notes.title LIKE :simpleSearch OR notes.content LIKE :simpleSearch";
//        echo $sql;die();
        $stm = $conn->prepare($sql);

        $stm->execute([':simpleSearch' => '%'.$simpleSearch.'%']);

        return $stm->fetchColumn();
    }

    public static function allNotesBySearch($conn, $simpleSearch, $limit = 4, $offset = 0)
    {
        $sql = "SELECT notes.id, notes.title, categories.title AS categoryTitle, notes.content
                FROM `notes` LEFT JOIN categories 
                ON notes.categoryId = categories.id 
                WHERE notes.title LIKE :simpleSearch OR notes.content LIKE :simpleSearch
                ORDER BY notes.id DESC
                LIMIT :limit OFFSET :offset";
//        echo $sql;die();
        $stm = $conn->prepare($sql);

        $stm->execute([
           ':simpleSearch' => '%'.$simpleSearch.'%',
           ':limit' => $limit,
           ':offset' => $offset
        ]);

        $notes = [];
        while ($note = $stm->fetch(\PDO::FETCH_OBJ)) {
            $notes[] = $note;
        }
        return $notes;
    }

    public static function getNote($conn, $id)
    {
        $sql = "SELECT notes.id, notes.title, notes.content, categories.title AS categoryTitle, categories.id AS categoryId
                FROM notes LEFT JOIN categories 
                ON notes.categoryId = categories.id WHERE notes.id = :noteId";
//        echo $sql;die();
        $stm = $conn->prepare($sql);
        $stm->execute([':noteId' => $id]);

        $converter = new CommonMarkConverter();

        $row = $stm->fetch(\PDO::FETCH_OBJ);

        $note = [];
        if ($row) {
            $note = ['id' => $row->id, 'title' => $row->title,
                'categoryTitle' => $row->categoryTitle, 'content' => $converter->convertToHtml($row->content)];
        }
        return $note;
    }

    public static function getComments($conn, $id)
    {
        $sql = "SELECT users.username, comments.content 
                FROM `comments` INNER JOIN users ON comments.user_id = users.id
                INNER JOIN notes ON comments.note_id = notes.id WHERE notes.id = :noteId";

        $stm = $conn->prepare($sql);

        $stm->execute([':noteId' => $id]);

        $comments = [];

        while ($comment = $stm->fetch(\PDO::FETCH_OBJ)) {
            $comments[] = $comment;
        }
        return $comments;
    }

    public static function destroy($conn, $noteId)
    {
        $sql = "DELETE FROM notes WHERE id = :noteId";

        $stm = $conn->prepare($sql);
        return $stm->execute([':noteId' => $noteId]);

    }

    public static function countNotesByAdvancedSearch($conn, $categoryId, $search)
    {
        $sql = "SELECT COUNT(*) AS Total FROM notes_search INNER JOIN categories 
                ON notes_search.categoryId = categories.id WHERE notes_search MATCH :search ";

//        if($categoryId > 0) {
//            $sql .= "AND notes_search.categoryId = :id";
//        }
        $stm = $conn->prepare($sql);
        $stm->execute([
            ':search' => $search
        ]);

        return $stm->fetchColumn();
    }

    public static function NotesByAdvancedSearch($conn, $search, $categoryId, $limit = 4, $offset = 0)
    {

        $sql = "SELECT note.title, note.content, categories.title AS categoryTitle 
                FROM notes_search AS note INNER JOIN categories 
                ON note.categoryId = categories.id WHERE notes_search MATCH :search ";

//        if($categoryId > 0) {
//            $sql .= " AND note.categoryId = :id";
//        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stm = $conn->prepare($sql);

        $stm->execute([
            ':search' => $search,
            ':limit' => $limit,
            ':offset' => $offset
        ]);

        $notes = [];
        while ($note = $stm->fetch(\PDO::FETCH_OBJ)) {
            $notes[] = $note;
        }
        return $notes;
    }
}