<?php
class Database {
    private $host     = 'localhost';
    private $dbname   = 'ql_phong_tro';
    private $username = 'root';
    private $password = '';
    private static $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=ql_phong_tro;charset=utf8mb4",
                    'root', '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );
            } catch (PDOException $e) {
                die("Lỗi kết nối CSDL: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    // Giữ lại cách cũ để tương thích
    public function connect(): PDO {
        return self::getInstance();
    }
}       