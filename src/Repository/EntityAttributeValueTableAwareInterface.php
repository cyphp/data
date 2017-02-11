<?php

namespace Cyphp\Data\Repository;

interface EntityAttributeValueTableAwareInterface
{
    public function setEntityAttributeValueTable(string $table, string $alias = null);

    public function getEntityAttributeValueTable(): string;

    public function getEntityAttributeValueTableAlias(): string;

    public function setForeignKey(string $key);

    public function getForeignKey(): string;
}
