<?php

namespace Cyphp\Data\Repository\Support;

class Rel
{
    const OPERATOR_LIKE = 'like';
    const OPERATOR_NOT_LIKE = 'not_like';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_NOT_BETWEEN = 'not_between';
    const OPERATOR_WITHIN = 'within';
    const OPERATOR_OR_X = 'or';
    const OPERATOR_IN = 'in';
    const OPERATOR_NOT_IN = 'not_in';
    const OPERATOR_IS_NULL = 'is_null';
    const OPERATOR_IS_NOT_NULL = 'is_not_null';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_NOT_EQUAL = 'not_equal';
    const OPERATOR_LESS_THAN = 'lt';
    const OPERATOR_LESS_THAN_AND_EQUAL = 'lte';
    const OPERATOR_GREATER_THAN = 'gt';
    const OPERATOR_GREATER_THAN_AND_EQUAL = 'gte';

    protected $value;
    protected $operator;

    public function __construct($value, $operator)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public static function eq($value)
    {
        return new self($value, self::OPERATOR_EQUAL);
    }

    public static function neq($value)
    {
        return new self($value, self::OPERATOR_NOT_EQUAL);
    }

    public static function lt($value)
    {
        return new self($value, self::OPERATOR_LESS_THAN);
    }

    public static function lte($value)
    {
        return new self($value, self::OPERATOR_LESS_THAN_AND_EQUAL);
    }

    public static function gt($value)
    {
        return new self($value, self::OPERATOR_GREATER_THAN);
    }

    public static function gte($value)
    {
        return new self($value, self::OPERATOR_GREATER_THAN_AND_EQUAL);
    }

    public static function contains($value)
    {
        return self::like($value);
    }

    public static function like($value)
    {
        return new self($value, self::OPERATOR_LIKE);
    }

    public static function notContains($value)
    {
        return new self($value, self::OPERATOR_NOT_LIKE);
    }

    public static function between(array $value)
    {
        return new self($value, self::OPERATOR_BETWEEN);
    }

    public static function notBetween(array $value)
    {
        return new self($value, self::OPERATOR_NOT_BETWEEN);
    }

    public static function within(array $value)
    {
        return new self($value, self::OPERATOR_WITHIN);
    }

    public static function orX(array $value)
    {
        return new self($value, self::OPERATOR_OR_X);
    }

    public static function in(array $value)
    {
        return new self($value, self::OPERATOR_IN);
    }

    public static function notIn(array $value)
    {
        return new self($value, self::OPERATOR_NOT_IN);
    }

    public static function isNull()
    {
        return new self(null, self::OPERATOR_IS_NULL);
    }

    public static function isNotNull()
    {
        return new self(null, self::OPERATOR_IS_NOT_NULL);
    }
}
