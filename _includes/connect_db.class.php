<?php
class DB {
    private static ?PDO $instance = null;

    public static function getConnection() {
        if (self::$instance === null)
            self::$instance = self::connect();

        if (self::$instance === null)
            throw new Exception("Cannot connect database");

        return self::$instance;
    }
    public static function connect() {
        $host = '127.0.0.1';
        $db = 'ip_3';
        $user = 'www-aplikace';
        $pass = 'Bezpe4n0Heslo.';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);

        return $pdo;
    }
}