<?php

namespace Cyphp\Data\Repository\Support;

use Cyphp\Data\Repository\EntityAwareInterface;
use Doctrine\DBAL\Query\QueryBuilder;

class EntityAttributeValueRepository extends AbstractRepository implements EntityAwareInterface
{
    protected $entityId;
    protected $supportedTimestamps = [];

    public function setEntityId(int $entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

     // RepositoryIntrface
    /**
     * Find element by its primary key value.
     *
     * @param int $id primary key
     *
     * @return false|array
     */
    public function get(int $attributeId)
    {
        $results = $this->query([
            'attribute_id' => $attributeId,
        ]);

        if (!$results) {
            return false;
        }

        $eav = array_shift($results);

        return $eav['value'] ?: null;
    }
    
    public function add(array $item)
    {
        $item = $this->makeListOf($item);

        $this->getConnection()->beginTransaction();

        foreach ($item as $eav) {
            $eav[$this->getPrimaryKey()] = $this->getEntityId();

            parent::add($eav);
        }

        $this->getConnection()->commit();

        return $this;
    }

    public function replace(array $item)
    {
        $item = $this->makeListOf($item);

        $inputed = array_column($item, 'attribute_id');
        $existed = array_column($this->query([
            'attribute_id' => [],
        ]), 'attribute_id');

        $removing = array_diff($existed, $inputed);

        foreach ($removing as $attributeId) {
            $this->remove($attributeId);
        }

        return $this->merge($item);
    }

    public function merge(array $item)
    {
        $item = $this->makeListOf($item);
        
        $this->getConnection()->beginTransaction();

        $query = 'INSERT into '.$this->getTable().'('.$this->getPrimaryKey().', attribute_id, value)
             VALUES (:entityId, :attrId, :value) ON DUPLICATE KEY UPDATE value = :value';

        $entityId = $this->getEntityId();

        foreach ($item as $eav) {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->bindParam(':entityId', $entityId, \PDO::PARAM_STR);
            $stmt->bindParam(':attrId', $eav['attribute_id'], \PDO::PARAM_STR);
            $stmt->bindParam(':value', $eav['value'], \PDO::PARAM_STR);

            $stmt->execute();
        }

        $this->getConnection()->commit();

        return $this;
    }

    public function update(array $item, int $id = null)
    {
        $item = $this->makeListOf($item);

        $this->getConnection()->beginTransaction();

        foreach ($item as $eav) {
            $identifier = [
                $this->getPrimaryKey() => $id ?: $this->getEntityId(),
                'attribute_id' => $eav['attribute_id'],
            ];

            $affectedRows = $this->db->update(
                $this->getTable(),
                $this->timestamps($eav),
                $identifier
            );
        }

        $this->getConnection()->commit();

        return $this;
    }

    public function remove(int $attributeId)
    {
        $identifier = [
            $this->getPrimaryKey() => $this->getEntityId(),
            'attribute_id' => $attributeId,
        ];

        $affectedRows = $this->db->delete($this->getTable(), $identifier);

        if (!intval($affectedRows)) {
            throw new \Exception('Failed to remove '.json_encoed($identifier), 500);
        }

        return $this;
    }

    public function hide(int $id)
    {
        throw new \Exception('EAV does not support soft deletion.');
    }

    public function query($criteria, array $options = [])
    {
        $criteria[$this->getPrimaryKey()] = $this->getEntityId();

        return parent::query($criteria, $options);
    }

    public function all()
    {
        return $this->query([]);
    }

    protected function makeListOf(array $item)
    {
        if (isset($item['attribute_id'])) {
            $item = [$item];
        }

        return $item;
    }
}
