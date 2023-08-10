<?php

namespace Humbrain\Framework\database;

use Pagerfanta\Pagerfanta;
use PDO;

class Repository
{
    protected string $table_name;

    protected string|null $entity;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $page
     * @param int $limitPerPage
     * @return Pagerfanta
     */
    public function findPaginated(int $page = 0, int $limitPerPage = 10): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table_name}",
            $this->entity
        );
        return (new Pagerfanta($query))->setMaxPerPage($limitPerPage)->setCurrentPage($page);
    }

    /**
     * @return string
     */
    protected function paginationQuery(): string
    {
        return "SELECT * FROM {$this->table_name}";
    }

    public function findList(string $key, string $value): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table_name}");
        $result = $query->fetchAll();
        $return = [];
        foreach ($result as $item) {
            $return[$item->$key] = $item->$value;
        }
        return $return;
    }

    /**
     * @param int $id
     * @return $this->entity
     */
    public function find(int $id): mixed
    {
        $smtp = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $smtp->execute([$id]);
        if ($this->entity === null) :
            return $smtp->fetch();
        endif;
        $smtp->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        return $smtp->fetch();
    }

    /**
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $fields): bool
    {
        $sqlFields = $this->buildFieldQuery($fields);
        $smtp = $this->pdo->prepare("UPDATE {$this->table_name} SET $sqlFields WHERE id = :id");
        $smtp->bindParam(':id', $id, PDO::PARAM_INT);
        foreach ($fields as $k => $v) :
            $smtp->bindValue(":$k", $v);
        endforeach;
        return $smtp->execute();
    }

    /**
     * @param array $fields
     * @return string
     */
    private function buildFieldQuery(array $fields): string
    {
        return join(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
    }

    public function create(object|array $params): bool
    {
        $sqlFields = $this->buildFieldQuery($params);
        $smtp = $this->pdo->prepare("INSERT INTO {$this->table_name} SET $sqlFields");
        return $smtp->execute($params);
    }

    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        return $query->execute([$id]);
    }

    /**
     * @return string|null
     */
    public function getTableName(): ?string
    {
        return $this->table_name;
    }

    /**
     * @return string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function exists(mixed $value): bool
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $query->execute([$value]);
        return $query->fetch() !== false;
    }
}
