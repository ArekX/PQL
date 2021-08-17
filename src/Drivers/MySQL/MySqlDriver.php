<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\RawQuery;
use PDO;
use PDOStatement;

class MySqlDriver implements Driver
{
    protected $connection = null;

    protected ?\PDO $pdo = null;

    public function configure(string $dsn, string $username, string $password, array $options = [])
    {
        $this->connection = [$dsn, $username, $password, $options];
    }

    protected function prepareAndExecute(RawQuery $query): \PDOStatement
    {
        $statement = $this->prepare($query);

        if (!$statement->execute()) {
            throw new \PDOException('Error');
        }

        return $statement;
    }

    public function open(): bool
    {
        if (!$this->pdo) {
            $this->pdo = new \PDO(...$this->connection);
        }

        return true;
    }

    public function close(): void
    {
        $this->pdo = null;
    }

    public function isOpen(): bool
    {
        return $this->pdo !== null;
    }

    protected function prepare(RawQuery $query): PDOStatement
    {
        $this->open();
        $statement = $this->pdo->prepare($query->getQuery(), $query->getConfig());

        foreach ($query->getParams() as $key => $value) {
            $type = PDO::PARAM_STR;

            if (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            }

            $statement->bindParam($key, $value, $type);
        }

        return $statement;
    }

    public function execute(RawQuery $query): int
    {
        $statement = $this->prepareAndExecute($query);
        $statement->closeCursor();
        return $statement->rowCount();
    }

    public function fetchFirst(RawQuery $query)
    {
        $statement = $this->prepareAndExecute($query);
        $result = $statement->fetch(PDO::FETCH_ASSOC) ?: null;
        $statement->closeCursor();
        return $result;
    }

    public function fetchAll(RawQuery $query): array
    {
        $statement = $this->prepareAndExecute($query);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $results;
    }

    public function exists(RawQuery $query): bool
    {
        return $this->fetchFirst($query) !== null;
    }

    public function fetchColumn(RawQuery $query, $column = null): array
    {
        $statement = $this->prepareAndExecute($query);
        $column = $column ?: 0;
        $results = [];
        while(true) {
            $result = $statement->fetchColumn($column);
            if ($result === false) {
                break;
            }

            $results[] = $result;
        }

        $statement->closeCursor();

        return $results;
    }

    public function fetchIndexed(string $byColumn, RawQuery $query): array
    {
        $fetched = $this->fetchAll($query);
        $results = [];

        foreach ($fetched as $row) {
            $results[$row[$byColumn]] = $row;
        }

        return $results;
    }

    public function fetchScalar(RawQuery $query)
    {
       $statement = $this->prepareAndExecute($query);
       $result = $statement->fetchColumn();
       $statement->closeCursor();
       return $result;
    }

    public function iterateRow(RawQuery $query): \Generator
    {
        $statement = $this->prepareAndExecute($query);

        while(true) {
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                break;
            }

            yield $result;
        }

        $statement->closeCursor();
    }

    public function iterateBatch(int $batchSize, RawQuery $query): \Generator
    {
        $batch = [];
        foreach ($this->iterateRow($query) as $row) {
            $batch[] = $row;
            if (count($batch) === $batchSize) {
                yield $batch;
                $batch = [];
            }
        }
    }

    public function getLastInsertId()
    {
        return $this->pdo ? $this->pdo->lastInsertId() : null;
    }
}