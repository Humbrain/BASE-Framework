<?php

namespace Humbrain\Framework\database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{

    /**
     * @param PDO $pdo
     * @param string $query
     * @param string $queryCount
     * @param string $entity
     */
    public function __construct(
        private PDO $pdo,
        private string $query,
        private string $queryCount,
        private string $entity
    ) {
    }

    public function getNbResults(): int
    {
        return $this->pdo->query($this->queryCount)->fetchColumn();
    }

    /**
     * @param int $offset
     * @param int $length
     * @return $this->entity[]
     */
    public function getSlice(int $offset, int $length): iterable
    {
        $stmt = $this->pdo->prepare($this->query . " LIMIT :offset, :limit");
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":limit", $length, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->entity);
    }
}
