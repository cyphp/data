<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;

class AttributeValue extends AbstractCriterion
{
    public function predicate()
    {
        return is_scalar($this->value) && $this->key == 'value';
    }

    public function makeCriterion(string $column, QueryBuilder $qb)
    {
        // very bizzare: in doctrine collection "same" ExpressionBuilder has
        // "contains" method. But DBAl's Query\ExpressionBuilder does not.
        $qb->andWhere(
            $qb->expr()->like($column, $qb->createNamedParameter('%'.$this->value.'%'))
        );
    }
}
