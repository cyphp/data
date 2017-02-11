<?php

namespace Cyphp\Data\Repository\Support;

// domain
use Cyphp\Data\Repository\EntityAttributeValueTableAwareInterface;
use Cyphp\Data\Repository\EntityAttributeValueAwareRepositoryInterface;
// framework
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;

abstract class AbstractEntityAttributeValueAwareRepository extends AbstractRepository implements EntityAttributeValueTableAwareInterface, EntityAttributeValueAwareRepositoryInterface
{
    protected $valuesTable;
    /**
     * Values table alias.
     *
     * @var string
     */
    protected $valuesAlias;

    /**
     * Foreign column name in the values table
     *
     * @var string
     */
    protected $foreignKey;

    // ValuesTableAwareInterface
    public function setEntityAttributeValueTable(string $valuesTable, string $alias = null)
    {
        $this->valuesTable = $valuesTable;
        $this->valuesAlias = $alias;

        if (!$this->valuesAlias) {
            $parts = explode('_', $this->getValuesTable());

            $this->valuesAlias = array_reduce($parts, function ($carry, $part) {
                return $carry.strtolower(substr($part, 0, 1));
            }, '');
        }

        return $this;
    }

    public function getEntityAttributeValueTable(): string
    {
        return $this->valuesTable;
    }

    public function getEntityAttributeValueTableAlias(): string
    {
        return $this->valuesAlias ?? null;
    }

    public function setForeignKey(string $key)
    {
        $this->foreignKey = $key;

        return $this;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getEntityAttributeValueRepository(int $entityId): EntityAttributeValueRepository
    {
        $repository = new EntityAttributeValueRepository();

        $repository
            ->setApplication($this->getApplication())
            ->setConnection($this->getConnection())
            ->setTable($this->getEntityAttributeValueTable(), $this->getEntityAttributeValueTableAlias())
            ->setPrimaryKey($this->getForeignKey())
            ->setEntityId($entityId);

        return $repository;
    }

    /**
     * This is just a convenient alias of getEntityAttributeValueRepository(int $entityId)
     *
     * @return EntityAttributeValueRepository
     */
    public function getEAVRepository($entityId = null)
    {
        return $this->getEntityAttributeValueRepository((int) $entityId);
    }
}
