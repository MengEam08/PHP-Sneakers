<?php
    $host = 'localhost';
    $db = 'bagisto_db';
    $user = 'root';
    $pass = ''; // XAMPP default
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $conn = new PDO($dsn, $user, $pass);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

