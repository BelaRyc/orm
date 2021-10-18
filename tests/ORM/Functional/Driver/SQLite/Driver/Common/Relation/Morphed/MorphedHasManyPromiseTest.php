<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\SQLite\Driver\Common\Relation\Morphed;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\Morphed\MorphedHasManyPromiseTest as CommonTest;

/**
 * @group driver
 * @group driver-sqlite
 */
class MorphedHasManyPromiseTest extends CommonTest
{
    public const DRIVER = 'sqlite';
}