<?php
namespace App;

use PDO;

class Database {

    public static function connect(): PDO {
        return new PDO(
            "mysql:host=127.0.0.1;dbname=b420chan;charset=utf8mb4",
            "root",
            "root",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
