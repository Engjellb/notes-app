<?php

namespace app\Http\Controllers;

use app\Models\User;

class RegisterController extends BaseController
{
    public function registerForm() {
        $data['firstname'] = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '';
        $data['username'] = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $data['emails'] = isset($_SESSION['emails']) ? $_SESSION['emails'] : '';
        $data['errorsV'] = isset($_SESSION['errorsV']) ? $_SESSION['errorsV'] : '';
        $this->view('auth/register', $data);
    }

    public function register()
    {
        unset($_SESSION['firstname']);
        unset($_SESSION['username']);
        unset($_SESSION['emails']);
        unset($_SESSION['errorsV']);

        $firsname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        $confirmpassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);

        $errorsValidation = [];

        if (mb_strlen($firsname) < 3) {
            $errorsValidation['firstname'] = 'Firstname should have 3 or more letters';
        } else {
            $firsname = ucfirst($firsname);
        }

        if (mb_strlen($username) < 3) {
            $errorsValidation['username'] = 'Username should have 3 or more letters';
        } else {
            if(User::findByUsername($this->conn, $username)) {
                $errorsValidation['usernameExists'] = 'Username is already in use';
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorsValidation['email'] = 'Email is not valid';
        } else {
            if(User::checkEmail($this->conn, $email)) {
                $errorsValidation['emailExists'] = 'Email is already in use';
            }
        }

        if(mb_strlen($password) < 4) {
            $errorsValidation['passLength'] = 'Your password should have min 4 letters';
        } else {
            if(strcmp($password, $confirmpassword) != 0) {
                $errorsValidation['passMatch'] = 'Your password do not match';
            }
        }


        if(!empty($errorsValidation)) {
             $_SESSION['firstname'] = $firsname;
             $_SESSION['username'] = $username;
             $_SESSION['emails'] = $email;
             $_SESSION['errorsV'] = $errorsValidation;
            echo json_encode($errorsValidation);
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            User::create($this->conn, $firsname, $username, $email, $passwordHash);
                $_SESSION['successRegistration'] = 'You have been registred successfully';
                $app = include '../config/app.php';
                $subject = 'Endless Univers registration - ' . $email;
                $link = $app['app_host'] . "/register/activation/" . sha1($app['salt'] . $email);

                $body = include '../resources/views/emails/activation.php';
//                if($this->sendEmail($email, $firsname, $subject, $body)) {
//                    $_SESSION['registration'] = 'Thank you for registration '.$firsname;
//                }
            }
        }

    public function verifyEmail() {
        $data['success'] = isset($_SESSION['registration']) ? $_SESSION['registration'] : '';
        unset($_SESSION['registration']);
        $this->view('emails/verifyEmail', $data);
    }

    public function activation($email) {
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        if(User::checkEmailActivation($this->conn, $email)) {
            if(User::updateUserActive($this->conn, $email)) {
                header('Location: /home/index');
            }
        }
        $data['raport'] = 'Invalid Email';
        $this->view('raport', $data);
    }
}