<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Postgres\Relation\ManyToMany\Cyclic;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\ManyToMany\Cyclic\CyclicManyToManyTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class CyclicManyToManyTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
