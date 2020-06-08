<?php

namespace app\Http\Controllers;

class HomeController extends BaseController
{
    public function index() {
        $data['success'] = isset($_SESSION['successRegistration']) ? $_SESSION['successRegistration'] : '';
        $data['successLogin'] = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : '';
        unset($_SESSION['successRegistration']);
        unset($_SESSION['authenticated']);
        $this->view('home', $data);
    }
}