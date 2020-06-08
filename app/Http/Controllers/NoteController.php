<?php

namespace app\Http\Controllers;

use app\Models\Category;
use app\Models\Comment;
use app\Models\Favorite;
use app\Models\Like;
use app\Models\Note;

class NoteController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        include '../config/session.php';
        getSession();
    }

    public function index() {
        $data['success'] = isset($_SESSION['noteAdded']) ? $_SESSION['noteAdded'] : '';
        $data['successLogin'] = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : '';

        $data['per_page'] = (int) isset($_GET['perPage']) ? $_GET['perPage'] : 4 ;
        $data['page'] = (int) isset($_GET['page']) ? $_GET['page'] : 1 ;

        $total = Note::countNotes($this->conn);
        $ofsset = ($data['page'] - 1) * $data['per_page'];

        $data['notes'] = Note::getNotes($this->conn, $data['per_page'], $ofsset);
        $data['pages'] = ceil($total/$data['per_page']);

        $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
        $favoritesNotes = Favorite::getFavorites($this->conn, $userId);
//        var_dump($favoritesNotes);die();
        $data['noteId'] = [];
        if($favoritesNotes != null) {
            foreach ($favoritesNotes as $key => $value) {
                $data['noteId'][] = $favoritesNotes[$key]->noteId;
            }
        }
        unset($_SESSION['noteAdded']);
        unset($_SESSION['authenticated']);
        $this->view('notes/index', $data);
    }

    public function addNoteForm() {
        $data['categories'] = Category::getCategories($this->conn);
        $this->view('notes/addNote', $data);
    }

    public function addNote() {
        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
        $content = $_POST['content'];
        $categoryId = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

        if(mb_strlen($title) < 5 || mb_strlen($title) > 100) {
            $errorV['title'] = 'Title should be min 5 and max 100 characters';
        }

        if(mb_strlen($content) < 10 || mb_strlen($content) > 1000) {
            $errorV['content'] = 'Content should be min 10 and max 1000 characters';
        }


        if(!empty($errorV)) {
            echo json_encode($errorV);
        } else {
            Note::create($this->conn, $title, $content, $categoryId);
            $_SESSION['noteAdded'] = 'You have added a note';

        }
    }

    public function edit($id) {
        $data['note'] = Note::getNote($this->conn, $id);
        $data['categories'] = Category::getCategories($this->conn);

        $this->view('notes/edit', $data);
    }

    public function update() {
        $noteId = (int) $_POST['noteId'];
        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
        $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
        $categoryId = (int) $_POST['category'];

        if(mb_strlen($title) < 5 || mb_strlen($title) > 100) {
            $errorV['title'] = 'Title should be min 5 and max 100 characters';
        }

        if(mb_strlen($content) < 10 || mb_strlen($content) > 1000) {
            $errorV['content'] = 'Content should be min 10 and max 1000 characters';
        }

        if(!empty($errorV)) {
            echo json_encode($errorV);
        } else {
            Note::update($this->conn, $title, $content, $categoryId, $noteId);
            $_SESSION['noteAdded'] = 'You updated a note';
        }

    }

    public function show($id) {
        $data['note'] = Note::getNote($this->conn, $id);
        $data['comments'] = Note::getComments($this->conn, $id);
        $data['likes'] = Like::getLikes($this->conn, $id);
        $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
        $data['like'] = Like::countLike($this->conn, $userId, $id);

        if(empty($data['note'])) {
            $data['raport'] = 'There is no note with this id';
            $this->view('raport', $data);
        } else {
            $this->view('notes/show', $data);
        }
    }

    public function addComment() {
        $noteId = (int) $_POST['noteId'];
        $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);

        if(Comment::create($this->conn, $_SESSION['userId'], $noteId, $content)) {
            header('Location: /note/show/'.$noteId);
        }
    }

    public function addLike() {
        $noteId = (int) $_POST['noteId'];

        if(Like::create($this->conn, $_SESSION['userId'], $noteId)) {
            header('Location: /note/show/'.$noteId);
        }
    }

    public function addFavorite() {
        $noteId = (int) $_POST['noteId'];

        if(Favorite::create($this->conn, $_SESSION['userId'], $noteId)) {
            header('Location: /note/index');
        }
    }

    public function favorites() {
        $data['notes'] = Favorite::getFavorites($this->conn, $_SESSION['userId']);

        $this->view('notes/favorite', $data);
    }

    public function simpleSearch() {
        $simpleSearch = filter_var($_GET['simpleSearch'], FILTER_SANITIZE_STRING);

        $data['per_page'] = (int) isset($_GET['perPage']) ? $_GET['perPage'] : 4 ;
        $data['page'] = (int) isset($_GET['page']) ? $_GET['page'] : 1 ;

        $total = Note::countNotesBySearch($this->conn, $simpleSearch);
        $ofsset = ($data['page'] - 1) * $data['per_page'];

        $data['notes'] = Note::allNotesBySearch($this->conn, $simpleSearch, $data['per_page'], $ofsset);
        $data['pages'] = ceil($total/$data['per_page']);

        $data['resultSearch'] = $simpleSearch;
        $this->view('notes/simpleSearch', $data);

    }

    public function delete() {
        $noteId = (int) $_POST['noteId'];

        if(Note::destroy($this->conn, $noteId)) {
            header('Location: /note/index');
        }
    }

    public function advancedSearchForm() {
        $data['categories'] = Category::getCategories($this->conn);

        $this->view('notes/advancedSearch', $data);
    }

    public function advancedSearch() {
        $search = filter_var($_GET['search'], FILTER_SANITIZE_STRING);
        $categoryId = (int) $_GET['category'];

        $data['per_page'] = (int) isset($_GET['perPage']) ? $_GET['perPage'] : 4 ;
        $data['page'] = (int) isset($_GET['page']) ? $_GET['page'] : 1 ;

        $total = Note::countNotesByAdvancedSearch($this->conn, $categoryId,  $search);

        $ofsset = ($data['page'] - 1) * $data['per_page'];
        $data['pages'] = ceil($total/$data['per_page']);

        $notes = Note::NotesByAdvancedSearch($this->conn, $search, $categoryId, $data['per_page'], $ofsset);

        if($notes != null) {
            $data['notes'] = $notes;
            $data['advancedSearch'] = $search;
            $data['categoryId'] = $categoryId;
            $this->view('notes/advancedSearchResult', $data);
        } else {
            $data['raport'] = 'There is no note with these criteria';
            $this->view('raport', $data);
        }
    }
}