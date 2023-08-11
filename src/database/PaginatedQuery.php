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
     * @param array $params
     */
    public function __construct(
        private PDO $pdo,
        private string $query,
        private string $queryCount,
        private string $entity,
        private array $params = []
    ) {
    }

    public function getNbResults(): int
    {
        if (!empty($this->params)) :
            $stmt = $this->pdo->prepare($this->queryCount);
            $stmt->execute($this->params);
        else :
            $stmt = $this->pdo->query($this->queryCount);
        endif;
        return $stmt->fetchColumn();
    }

    /**
     * @param int $offset
     * @param int $length
     * @return iterable
     */
    public function getSlice(int $offset = 0, int $length = 0): iterable
    {
        $query = $this->pdo->prepare($this->query . " LIMIT :offset, :length");
        if (!empty($this->params)) :
            foreach ($this->params as $key => $value) :
                $query->bindParam($key, $value);
            endforeach;
        endif;
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':length', $length, PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        return $query->fetchAll();
    }
}
