<?php


namespace App\Core;

use PDO;
use PDOException;

class Connection
{
    private $mode = PDO::FETCH_OBJ;

    public $conn;

    public function __construct()
    {
        $this->conn = new PDO('mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("set names utf8");
    }

    public function getData($query){
        $result = $this->conn->query($query);
        $result->setFetchMode($this->mode);
        $result->execute();
        return $result;
    }
}