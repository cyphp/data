<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Cyphp\Data\Repository\Support\Rel;
use Doctrine\DBAL\Query\QueryBuilder;

class Nullable extends AbstractCriterion
{
    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        $qb->andWhere(
            Rel::OPERATOR_IS_NULL === $this->value->getOperator()
                ? $qb->expr()->isNull($column)
                : $qb->expr()->isNotNull($column)
        );
    }
}
