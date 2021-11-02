<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Postgres\Relation;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\Eager2Test as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class Eager2Test extends CommonClass
{
    public const DRIVER = 'postgres';
}
