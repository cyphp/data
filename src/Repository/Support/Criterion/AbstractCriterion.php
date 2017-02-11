<?php

namespace Cyphp\Data\Repository\Support\Criterion;

use Cyphp\Data\Repository\Support\Rel;

abstract class AbstractCriterion
{
    const IS_NOT_NULL = 'value_is_not_null';
    const IS_NULL = 'value_is_null';

    protected $key;
    protected $value;

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public static function getCriterionMaker(string $key, Rel $rel)
    {
        switch ($rel->getOperator()) {
            case Rel::OPERATOR_EQUAL:
            case Rel::OPERATOR_NOT_EQUAL:
            case Rel::OPERATOR_LESS_THAN:
            case Rel::OPERATOR_LESS_THAN_AND_EQUAL:
            case Rel::OPERATOR_GREATER_THAN:
            case Rel::OPERATOR_GREATER_THAN_AND_EQUAL:
                return new Scalar($key, $rel);
            case Rel::OPERATOR_IN:
            case Rel::OPERATOR_NOT_IN:
                return new SimpleList($key, $rel);
            case Rel::OPERATOR_IS_NULL:
            case Rel::OPERATOR_IS_NOT_NULL:
                return new Nullable($key, $rel);
            case Rel::OPERATOR_BETWEEN:
            case Rel::OPERATOR_NOT_BETWEEN:
            case Rel::OPERATOR_WITHIN:
                return new Ranged($key, $rel);
            case Rel::OPERATOR_LIKE:
            case Rel::OPERATOR_NOT_LIKE:
                return new Similar($key, $rel);
        }
    }
}
