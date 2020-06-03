<?php

namespace app\Models;

class User extends Model
{
    public static function findByEmail($conn, $email) {
        $sql = "SELECT * FROM users WHERE email = '".$email."' AND user_active = 1 LIMIT 1";
//        echo $sql;die();
        $result = $conn->query($sql);

        return $result->num_rows > 0 ? $user = $result->fetch_object() : false;
    }

    public static function checkEmail($conn, $email) {
        $sql = "SELECT * FROM users WHERE email = '".$email."' LIMIT 1";

        $result = $conn->query($sql);

        return $result->num_rows > 0 ? $user = $result->fetch_object() : false;
    }

    public static function checkEmailActivation($conn, $email) {
        $config = include '../config/app.php';
        $sql = "SELECT * FROM users WHERE SHA1(CONCAT('".$config['salt']."', email)) = '".$email."' AND user_active = 0 LIMIT 1";

        $result = $conn->query($sql);

        return $result->num_rows > 0 ? true : false;
    }

    public static function updateUserActive($conn, $email) {
        $config = include '../config/app.php';

        $sql = "UPDATE users SET user_active = 1  
                WHERE SHA1(CONCAT('".$config['salt']."', email)) = '".$email."' LIMIT 1";

        return $conn->query($sql);
    }

    public static function findByUsername($conn, $username) {
        $sql = "SELECT * FROM users WHERE username = '".$username."' LIMIT 1";

        $result = $conn->query($sql);
        return $result->num_rows > 0 ? true : false;
    }

    public static function create($conn, $firstname, $username, $email, $password) {
        $sql = "INSERT INTO users (firstname, username, email, password) 
                VALUES ('".$firstname."', '".$username."', '".$email."', '".$password."')";

       return $conn->query($sql);
    }

}