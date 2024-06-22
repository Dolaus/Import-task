<?php

namespace App\database;

require_once "config\DB.php";

use PDO;
use config\DB;
use PDOException;

class DBConnection
{
    public static function getConnection(): PDO
    {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB::HOST . ';dbname=' . DB::DBNAME,
                DB::USERNAME,
                DB::PASSWORD
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}
