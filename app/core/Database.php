<?php
namespace app\core;
class Database {
    private $connection;

    public function __construct() {
        $config = require '../config/config.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
        try {
            $this->connection = new \PDO($dsn, $config['db_user'], $config['db_password']);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        }

    public function getConnection() {
        return $this->connection;
    }
}