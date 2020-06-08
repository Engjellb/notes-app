<?php

namespace app\Http\Controllers;

use app\Models\User;

class LoginController extends BaseController
{
    public function loginForm() {
        $data['errorV'] = isset($_SESSION['wrongPass']) ? $_SESSION['wrongPass'] : '';
        $data['sessionExp'] = isset($_SESSION['sessionExpired']) ? $_SESSION['sessionExpired'] : '';
        unset($_SESSION['sessionExpired']);
        $this->view('auth/login', $data);
    }

    public function login() {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

        $user = User::findByEmail($this->conn, $email);

        if($user) {
            if(password_verify($password, $user->password)) {
                $_SESSION['isAuth'] = true;
                $_SESSION['userId'] = $user->id;
//                $_SESSION['level'] = $user->level;
//                $_SESSION['loggedTime'] = time();
                $_SESSION['isAdmin'] = ($user->level == 1) ? true : false;
                $_SESSION['authenticated'] = 'You are logged in';
                die();
            }
        }
        $errorV['wrongPass'] = 'Your emails or password is incorrect';
        echo json_encode($errorV);
    }

    public function logout() {
        session_destroy();
        header('Location: /login/loginForm');
    }
}