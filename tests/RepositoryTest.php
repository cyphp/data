<?php

namespace Cyphp\Data\Test;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testRep()
    {
        $repository = new Fixtures\Repository();

        $this->assertTrue(true, (bool) $repository);
    }
}
