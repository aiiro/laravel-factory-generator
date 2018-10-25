<?php

namespace Aiiro\Factory\Connections;

class MySql implements DatabaseContract
{

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * MySql constructor.
     *
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchTables()
    {
        $stmt = $this->pdo->prepare("SHOW TABLES");
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function fetchColumns($table)
    {
        $columns = [];

        $stmt = $this->pdo->prepare("DESCRIBE $table");
        $stmt->execute();

        foreach ($stmt->fetchAll() as $column) {
            $columns[] = $column['Field'];
        }

        return $columns;
    }
}
