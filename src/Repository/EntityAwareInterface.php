<?php

namespace Cyphp\Data\Repository;

interface EntityAwareInterface
{
    public function setEntityId(int $entityId);

    public function getEntityId(): int;
}
