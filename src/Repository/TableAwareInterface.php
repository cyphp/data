<?php

namespace Cyphp\Data\Repository;

interface TableAwareInterface
{
    public function setTable(string $table, string $alias = null);

    public function getTable(): string;

    public function getTableAlias(): string;

    public function setPrimaryKey(string $primaryKey = 'id');

    public function getPrimaryKey();
}
