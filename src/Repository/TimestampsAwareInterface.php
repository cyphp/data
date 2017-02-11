<?php

namespace Cyphp\Data\Repository;

interface TimestampsAwareInterface
{
    public function timestamps(array $values, $isCreation = false);

    public function setSupportedTimestamps(array $timestamps = ['created_at' => true, 'updated_at' => true]);

    public function getSupportedTimestamps(): array;
}
