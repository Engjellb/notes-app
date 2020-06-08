<?php

namespace app\Http\Controllers;

use app\Models\Category;

class CategoryController extends BaseController
{
    public function index() {
        $data['success'] = isset($_SESSION['categoryAdded']) ? $_SESSION['categoryAdded'] : '';
        $data['categories'] = Category::getCategories($this->conn);
        unset($_SESSION['categoryAdded']);
        $this->view('categories/index', $data);
    }

    public function addCategoryForm() {
        $this->view('categories/addCategory');
    }

    public function editCategoryForm($id) {
        $data['category'] = Category::getCategory($this->conn, $id);
        $this->view('categories/edit', $data);
    }

    public function editCategory() {
        $categoryId = (int) $_POST['categoryId'];

        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);

        if(mb_strlen($title) < 3 || mb_strlen($title) > 15) {
            $errorV['title'] = 'Title should be min 3 and max 15 characters';
        }

        if(!empty($errorV)) {
            echo json_encode($errorV);
        } else {
            Category::update($this->conn, $title, $categoryId);
            $_SESSION['categoryAdded'] = 'You have updated the category';
        }
    }

    public function addCategory() {
        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);

        if(mb_strlen($title) < 3 || mb_strlen($title) > 15) {
            $errorV['title'] = 'Title should be min 3 and max 15 characters';
        }

        if(!empty($errorV)) {
            echo json_encode($errorV);
        } else {
            Category::create($this->conn, $title);
            $_SESSION['categoryAdded'] = 'You have added a category';
        }
    }

    public function notes($id) {
        $data['notes'] = Category::getNotes($this->conn, $id);

        if(empty($data['notes'])) {
            $data['raport'] = 'There is no note for this category';
            $this->view('raport', $data);
        } else {
            $this->view('categories/notes', $data);
        }
    }

    public function deleteCategory($id) {
        Category::destroy($this->conn, $id);
        header('Location: /category/index');

    }
}