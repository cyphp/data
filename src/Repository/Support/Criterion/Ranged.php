<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Cyphp\Data\Repository\Support\Rel;
use Doctrine\DBAL\Query\QueryBuilder;

class Ranged extends AbstractCriterion
{
    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        $operator = $this->value->getOperator();
        $ranged = $this->value->getValue();
        $expr = $qb->expr();

        if (Rel::OPERATOR_BETWEEN === $operator) {
            return $qb->andWhere($column." BETWEEN :from_$column AND :to_$column")
                ->setParameter(":from_$column", $ranged[0])
                ->setParameter(":to_$column", $ranged[1]);
        }

        if (Rel::OPERATOR_WITHIN === $operator) {
            return $qb->andWhere(
                $expr->andX(
                    $expr->gte($column, $qb->createNamedParameter($ranged[0])),
                    $expr->lt($column, $qb->createNamedParameter($ranged[1]))
                )
            );
        }

        // not between
        return $qb->andWhere(
            $expr->andX(
                $expr->lt($column, $qb->createNamedParameter($ranged[0])),
                $expr->gt($column, $qb->createNamedParameter($ranged[1]))
            )
        );
    }
}
