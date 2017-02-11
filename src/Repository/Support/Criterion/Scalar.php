<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Cyphp\Data\Repository\Support\Rel;
use Doctrine\DBAL\Query\QueryBuilder;

class Scalar extends AbstractCriterion
{
    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        switch ($this->value->getOperator()) {
            case Rel::OPERATOR_EQUAL:
                $op = 'eq';
                break;
            case Rel::OPERATOR_NOT_EQUAL:
                $op = 'neq';
                break;
            case Rel::OPERATOR_LESS_THAN:
                $op = 'lt';
                break;
            case Rel::OPERATOR_LESS_THAN_AND_EQUAL:
                $op = 'lte';
                break;
            case Rel::OPERATOR_GREATER_THAN:
                $op = 'gt';
                break;
            case Rel::OPERATOR_GREATER_THAN_AND_EQUAL:
                $op = 'gte';
                break;
            default:
                $op = 'eq';
        }

        $qb->andWhere(
            $qb->expr()->{$op}($column, $qb->createNamedParameter($this->value->getValue()))
        );
    }
}
