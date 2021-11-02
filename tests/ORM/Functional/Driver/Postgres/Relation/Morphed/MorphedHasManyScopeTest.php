<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Postgres\Relation\Morphed;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\Morphed\MorphedHasManyScopeTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class MorphedHasManyScopeTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
