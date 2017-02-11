<?php

namespace Cyphp\Data\Repository\Support;

// domain
use Cyphp\Data\Repository\ConnectionAwareInterface;
use Cyphp\Data\Repository\TableAwareInterface;
use Cyphp\Data\Repository\ApplicationAwareInterface;
use Cyphp\Data\Repository\TimestampsAwareInterface;
use Cyphp\Data\Repository\RepositoryInterface;
use Cyphp\Data\Repository\Support\Criterion;
use Cyphp\Data\Repository\Support\Rel;
// framework
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;

abstract class AbstractRepository implements
    ConnectionAwareInterface,
    TableAwareInterface,
    ApplicationAwareInterface,
    TimestampsAwareInterface,
    RepositoryInterface
{
    protected $app;

    protected $db;

    protected $table;
    protected $entityAlias;

    protected $primaryKey = 'id';

    protected $supportedTimestamps = [];

    // ConnectionInterface
    public function setConnection(Connection $db)
    {
        $this->db = $db;

        return $this;
    }

    public function getConnection(): Connection
    {
        return $this->db;
    }

    // TableAwareInterface
    public function setTable(string $table, string $alias = null)
    {
        $this->table = $table;
        $this->entityAlias = $alias;

        // generate alias by taking first chars
        if (!$this->entityAlias) {
            $parts = explode('_', $this->getTable());

            $this->entityAlias = array_reduce($parts, function ($carry, $part) {
                return $carry.strtolower(substr($part, 0, 1));
            }, '');
        }

        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTableAlias(): string
    {
        return isset($this->entityAlias) ? $this->entityAlias : null;
    }

    public function setPrimaryKey(string $primaryKey = 'id')
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    // ApplicationAwareInterface
    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->app;
    }

    // RepositoryIntrface
    /**
     * Find element by its primary key value.
     *
     * @param int $id primary key
     *
     * @return false|array
     */
    public function get(int $id)
    {
        $results = $this->query([$this->getPrimaryKey() => $id]);

        if (!$results) {
            return false;
        }

        return array_shift($results);
    }
    
    public function add(array $item)
    {
        $this->db->insert(
            $this->getTable(),
            $this->timestamps($item, true)
        );

        return intval($this->db->lastInsertId());
    }

    public function update(array $item, int $id = null)
    {
        $affectedRows = $this->db->update(
            $this->getTable(),
            $this->timestamps($item),
            [$this->getPrimaryKey() => $id]
        );

        return intval($affectedRows);
    }

    public function remove(int $id)
    {
        $affectedRows = $this->db->delete(
            $this->getTable(),
            [$this->getPrimaryKey() => $id]
        );

        return intval($affectedRows);
    }

    public function hide(int $id)
    {
        return $this->update($id, [
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function query($criteria)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb->select('*')
            ->from($this->getTable(), $this->getTableAlias());

        $this->addCriteriaTo($criteria, $qb);

        return $qb->execute()->fetchAll();
    }

    public function all($order = [])
    {
        $sql = "SELECT * FROM {$this->table}";

        if (count($order) === 0) {
            return $this->db->fetchAll($sql);
        }

        $orderBy = [];
        array_walk($order, function ($value, $column) use (&$orderBy) {
            $orderBy[] = "$column $value";
        });

        return $this->db->fetchAll(sprintf('%s ORDER BY %s', $sql, implode(', ', $orderBy)));
    }

    // TimestampsAwareInterface
    public function timestamps(array $values, $isCreation = false)
    {
        if ($isCreation && ($this->supportedTimestamps['created_at'] ?? false)) {
            $values['created_at'] = date('Y-m-d H:i:s');
        }

        if ($this->supportedTimestamps['updated_at'] ?? false) {
            $values['updated_at'] = date('Y-m-d H:i:s');
        }

        return $values;
    }

    public function setSupportedTimestamps(array $timestamps = ['created_at' => true, 'updated_at' => true])
    {
        $this->supportedTimestamps = $timestamps;

        return $this;
    }

    public function getSupportedTimestamps(): array
    {
        return $this->supportedTimestamps;
    }

    // class helper methods
    protected function addCriteriaTo(array $criteria, QueryBuilder $qb)
    {
        // use named parameter - to save coder from writing setParameter(...)
        foreach ($criteria as $key => $value) {
            $this->addCriterionTo($key, $value, $qb);
        }
    }

    protected function addCriterionTo(string $key, $value, QueryBuilder $qb)
    {
        if ($value instanceof Rel) {
            return $this->makeCriterion($key, $value, $qb);
        }

        if (is_scalar($value)) {
            $value = Rel::eq($value);

            return $this->makeCriterion($key, $value, $qb);
        }

        $isArray = is_array($value);

        if ($isArray && !isset($value['from']) && !isset($value['to'])) {
            $value = Rel::in($value);

            return $this->makeCriterion($key, $value, $qb);
        }

        if ($isArray && isset($value['from']) && isset($value['to'])) {
            $value = Rel::within([$value['from'], $value['to']]);

            return $this->makeCriterion($key, $value, $qb);
        }

        if ($isArray && isset($value['from'])) {
            $value = Rel::gte($value['from']);

            return $this->makeCriterion($key, $value, $qb);
        }

        if ($isArray && isset($value['to'])) {
            $value = Rel::lt($value['to']);

            return $this->makeCriterion($key, $value, $qb);
        }
    }

    protected function makeCriterion(string $key, Rel $rel, QueryBuilder $qb)
    {
        return Criterion\AbstractCriterion::getCriterionMaker($key, $rel)
            ->makeCriterion(
                (false === strstr($key, '.') ? $this->getTableField($key) : $key),
                $qb
            );
    }

    protected function getTableField($field, $as = null)
    {
        return $this->getTableAlias().'.'.$field.($as ? ' AS '.$as : '');
    }
}
