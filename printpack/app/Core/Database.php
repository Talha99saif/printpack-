<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
use PDOException;

final class Database {
    private static ?PDO $pdo = null;
    public static function connection(): PDO {
        if (self::$pdo) return self::$pdo;
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $name = $_ENV['DB_NAME'] ?? 'printpack';
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        try {
            self::$pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Database connection failed. Check your .env settings and import database/schema.sql.');
        }
        return self::$pdo;
    }
}
