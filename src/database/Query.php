<?php

namespace Humbrain\Framework\database;

use PDO;
use PDOStatement;

/**
 * Class Query
 * @class Query
 * @package Humbrain\Framework\database
 */
class Query
{
    private array $select = [];
    private array $from = [];
    private array $where = [];
    private array $group = [];
    private array $order = [];
    private int $limit;
    private array $params = [];
    private PDO $pdo;

    /**
     * Query constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set the table to query FROM
     * @param string $table
     * @param string|null $alias
     * @return $this
     */
    public function from(string $table, string $alias = null): self
    {
        if (is_null($alias)) :
            $this->from[] = $table;
        else :
            $this->from[$alias] = $table;
        endif;
        return $this;
    }

    /**
     * Add parameters to the query
     * @param array $params
     * @return $this
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Add fields to select
     * @param string ...$fields
     * @return $this
     */
    public function select(string ...$fields): self
    {
        $this->select = array_merge($this->select, $fields);
        return $this;
    }

    /**
     * Add conditions to WHERE
     * @param string ...$condition
     * @return $this
     */
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    /**
     * Add fields to GROUP BY
     * @param string ...$fields
     * @return $this
     */
    public function groupBy(string ...$fields): self
    {
        $this->group = array_merge($this->group, $fields);
        return $this;
    }

    /**
     * Add fields to ORDER BY
     * @param string ...$fields
     * @return $this
     */
    public function orderBy(string ...$fields): self
    {
        $this->order = array_merge($this->order, $fields);
        return $this;
    }

    /**
     * Set the number of results to return
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Return number of results
     * @return int
     */
    public function count(): int
    {
        $query = clone $this;
        $query->select = ['COUNT(*)'];
        return $query->execute()->fetchColumn();
    }

    /**
     * Build the query
     * @return string
     */
    public function __toString(): string
    {
        $parts = ['SELECT'];
        if (empty($this->select)) :
            $parts[] = '*';
        else :
            $parts[] = implode(', ', $this->select);
        endif;
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) :
            $parts[] = 'WHERE';
            $parts[] = '(' . implode(') AND (', $this->where) . ')';
        endif;
        if (!empty($this->group)) :
            $parts[] = 'GROUP BY';
            $parts[] = implode(', ', $this->group);
        endif;
        if (!empty($this->order)) :
            $parts[] = 'ORDER BY';
            $parts[] = implode(', ', $this->order);
        endif;
        if (!empty($this->limit)) :
            $parts[] = 'LIMIT';
            $parts[] = $this->limit;
        endif;
        return implode(' ', $parts);
    }

    /**
     * Build the FROM part of the query
     * @return string
     */
    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) :
            if (is_string($key)) :
                $from[] = "$value AS $key";
            else :
                $from[] = $value;
            endif;
        endforeach;
        return implode(', ', $from);
    }

    /**
     * Execute the query
     * @return false|PDOStatement
     */
    private function execute(): false|PDOStatement
    {
        $query = $this->__toString();
        if (!empty($this->params)) :
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        else :
            return $this->pdo->query($query);
        endif;
    }
}
