<?php

const DB_HOST = 'localhost';
const DB_NAME = 'task';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "erro na conexão do banco de dados" . $e->getMessage()]);
    exit;
}