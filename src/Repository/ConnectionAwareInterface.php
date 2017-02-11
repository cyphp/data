<?php

namespace Cyphp\Data\Repository;

use Doctrine\DBAL\Connection;

interface ConnectionAwareInterface
{
    public function setConnection(Connection $connection);

    public function getConnection(): Connection;
}
