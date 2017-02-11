<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Cyphp\Data\Repository\Support\Rel;
use Doctrine\DBAL\Query\QueryBuilder;

class Similar extends AbstractCriterion
{
    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        $operator = $this->value->getOperator();

        if (Rel::OPERATOR_LIKE === $operator) {
            return $qb->andWhere(
                $qb->expr()->like($column, $qb->createNamedParameter('%'.$this->value->getValue().'%'))
            );
        }

        return $qb->andWhere(
            $qb->expr()->notLike($column, $qb->createNamedParameter('%'.$this->value->getValue().'%'))
        );
    }
}
