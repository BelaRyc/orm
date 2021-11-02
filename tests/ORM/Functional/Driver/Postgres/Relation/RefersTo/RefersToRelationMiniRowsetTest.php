<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Postgres\Relation\RefersTo;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\RefersTo\RefersToRelationMiniRowsetTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class RefersToRelationMiniRowsetTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
