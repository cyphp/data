<?php

namespace Cyphp\Data\Repository;

interface RepositoryInterface
{
    public function add(array $item);

    public function update(array $item, int $id = null);

    public function remove(int $id);

    public function hide(int $id);

    public function query($criteria);

    public function get(int $id);

    public function all();
}
