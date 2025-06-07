<?php
    $host = 'localhost';
    $db = 'bagisto_db';
    $user = 'root';
    $pass = ''; // XAMPP default
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Show errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch as associative arrays
];
    try {
        $conn = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

