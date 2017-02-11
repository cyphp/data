<?php

namespace Cyphp\Data\Repository;

use Silex\Application;

interface ApplicationAwareInterface
{
    public function setApplication(Application $app);

    public function getApplication(): Application;
}
