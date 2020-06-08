<?php

namespace app\Models;

class User extends Model
{
    public static function findByEmail($conn, $email) {

        try {
            $sql = "SELECT * FROM users WHERE email = :email AND user_active = 1 LIMIT 1";
//           echo $sql;die();
            $stm = $conn->prepare($sql);

            $stm->execute([
                ':email' => $email
            ]);

            $user = $stm->fetch(\PDO::FETCH_OBJ);

            return $user ? $user : false;

        } catch (\PDOException $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public static function checkEmail($conn, $email) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

            $stm = $conn->prepare($sql);

            $stm->execute([':email' => $email]);
            $user = $stm->fetch(\PDO::FETCH_OBJ);

            return $user ? true : false;

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }
//
//    public static function checkEmailActivation($conn, $email) {
//        $config = include '../config/app.php';
//        $sql = "SELECT * FROM users WHERE SHA1(CONCAT('".$config['salt']."', email)) = '".$email."' AND user_active = 0 LIMIT 1";
//
//        $result = $conn->query($sql);
//
//        return $result->num_rows > 0 ? true : false;
//    }
//
//    public static function updateUserActive($conn, $email) {
//        $config = include '../config/app.php';
//
//        $sql = "UPDATE users SET user_active = 1
//                WHERE SHA1(CONCAT('".$config['salt']."', email)) = '".$email."' LIMIT 1";
//
//        return $conn->query($sql);
//    }
//
    public static function findByUsername($conn, $username) {
        try {
            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

            $stm = $conn->prepare($sql);

            $stm->execute([':username' => $username]);
            $user = $stm->fetch(\PDO::FETCH_OBJ);

            return $user ? true : false;

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }

    public static function create($conn, $firstname, $username, $email, $password) {
        try {
            $sql = "INSERT INTO users (firstname, username, email, password) 
                VALUES (:firstname, :username, :email, :password)";

            $user = $conn->prepare($sql);

            $user->execute([
                ':firstname' => $firstname,
                ':username' => $username,
                ':email' => $email,
                ':password' => $password
            ]);

        } catch (\PDOException $e) {
            die('Error: '.$e->getMessage());
        }
    }

}