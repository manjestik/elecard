<?php


namespace App\Database;


use Exception;
use mysqli;

class DataBase
{
    /** @var mysqli */
    private $mysqli;

    /**
     * DataBase constructor.
     * @param array $dbInfo
     * @throws Exception
     */
    public function __construct(array $dbInfo)
    {
        $mysqli = $this->connectDB($dbInfo);
        $this->mysqli = $mysqli;
        $this->getDB($dbInfo['name']);
    }

    /**
     * @param array $dbInfo
     * @return mysqli
     * @throws Exception
     */
    private function connectDB(array $dbInfo): mysqli
    {
        $mysqli = new mysqli($dbInfo['hostname'], $dbInfo['username'], $dbInfo['password']);
        if ($mysqli->connect_errno) {
            throw new Exception("Не удалось подключиться к MySQL: ($mysqli->connect_errno) $mysqli->connect_error");
        }
        return $mysqli;
    }

    /**
     * @return mysqli
     */
    public function getMysql(): mysqli
    {
        return $this->mysqli;
    }

    private function createDB(string $dbName)
    {
        $sql = "CREATE DATABASE $dbName";
        $this->mysqli->query($sql);
    }

    /**
     * @param string $dbName
     */
    private function getDB(string $dbName)
    {
        $mysql = $this->mysqli;
        if (!$mysql->select_db($dbName)) {
            $this->createDB($dbName);
            $mysql->select_db($dbName);
        }
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS category (
                    id INT NOT NULL AUTO_INCREMENT, 
                    name VARCHAR(255) NOT NULL, 
                    PRIMARY KEY (id)
                )";
        $this->mysqli->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS products (
                    id INT NOT NULL AUTO_INCREMENT, 
                    name VARCHAR(255) NOT NULL, 
                    description TEXT NOT NULL, 
                    id_category INT,
                    PRIMARY KEY (id),
                    INDEX id_cat (id_category),
                    FOREIGN KEY (id_category) REFERENCES category(id) ON DELETE CASCADE
                )";
        $this->mysqli->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS attributes (
                    id INT NOT NULL AUTO_INCREMENT, 
                    name VARCHAR(255) NOT NULL, 
                    type VARCHAR(255) NOT NULL, 
                    PRIMARY KEY (id)
                )";
        $this->mysqli->query($sql);

        $sql = "CREATE TABLE attributes_value (
                    id INT NOT NULL AUTO_INCREMENT, 
                    value VARCHAR(255) NOT NULL, 
                    id_attribute INT,
                    id_product INT,
                    PRIMARY KEY (id),
                    INDEX id_atr (id_attribute),
                    FOREIGN KEY (id_attribute) REFERENCES attributes(id) ON DELETE CASCADE,
                    INDEX id_prod (id_product),
                    FOREIGN KEY (id_product) REFERENCES products(id) ON DELETE CASCADE
                )";
        $this->mysqli->query($sql);
    }

    /**
     * @param string $sql
     * @return bool
     */
    public function query(string $sql): bool
    {
        if ($this->mysqli->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $sql
     * @param string $class
     * @return array|null
     */
    public function select(string $sql, string $class): ?array
    {
        $result = $this->mysqli->query($sql);
        if ($result === false) {
            $this->createTable();
            return null;
        }

        $values = [];
        while (($row = $result->fetch_object($class)) != false) {
            $values[] = $row;
        }

        return $values;
    }

    public function selectST(string $sql): ?array
    {
        $result = $this->mysqli->query($sql);
        if ($result === false) {
            $this->createTable();
            return null;
        }

        $values = [];
        while (($row = $result->fetch_array()) != false) {
            $values[] = $row;
        }

        return $values;
    }

    /**
     * @param array $values
     * @return array
     */
    public function clearSQL(array $values): array
    {
        $mysql = $this->mysqli;
        foreach ($values as $valueKey => $value) {
            $values[$valueKey] = $mysql->real_escape_string($value);
        }

        return $values;
    }
}
