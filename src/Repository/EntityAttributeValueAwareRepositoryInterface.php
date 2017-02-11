<?php

namespace Cyphp\Data\Repository;

interface EntityAttributeValueAwareRepositoryInterface
{
    public function getEntityAttributeValueRepository(int $entityId): Support\EntityAttributeValueRepository;
}
