<?php

namespace Aiiro\Factory\Connections;

class Sqlite implements DatabaseContract
{

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Sqlite constructor.
     *
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchTables()
    {
        $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT IN ('migrations', 'sqlite_sequence');");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function fetchColumns($table)
    {
        $columns = [];

        $stmt = $this->pdo->prepare("PRAGMA table_info($table)");
        $stmt->execute();
        foreach ($stmt->fetchAll() as $column) {
            $columns[] = $column['name'];
        }

        return $columns;
    }
}
