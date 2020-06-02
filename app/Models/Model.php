<?php

namespace app\Models;

class Model
{
    protected $conn;

    public function __construct()
    {
        $config = include '../config/database.php';
        $this->conn = new \mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        if($this->conn->connect_error) {
            die('Connection failed: '.$this->conn->connect_error);
        } else {
            $this->conn->set_charset('utf8');
        }
    }
}