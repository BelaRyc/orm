<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\MySQL\Relation\Morphed;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\Morphed\MorphedHasOnePromiseTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class MorphedHasOnePromiseTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
