<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;

class SimpleList extends AbstractCriterion
{
    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        $list = $this->value->getValue();
        $isInt = is_numeric(current($list));

        return $qb->andWhere(
            $qb->expr()->in($column, $qb->createNamedParameter($list, $isInt ? Connection::PARAM_INT_ARRAY : Connection::PARAM_STR_ARRAY))
        );
    }
}
