<?php

namespace Cyphp\Data\Repository\Support;

class Results extends \ArrayObject
{
    public function map(MapperInterface $mapper)
    {
        $it = $this->getIterator();

        while ($it->valid()) {
            $item = $it->current();

            $it->offsetSet($it->key(), $mapper->map($item));

            $it->next();
        }

        return $this;
    }

    public function transform(TransformerInterface $transformer, bool $immutable = false)
    {
        $items = $this->getArrayCopy();

        $results = $immutable ? new self($items) : $this;

        $results->exchangeArray($transformer->transform($items));

        return $results;
    }
}
