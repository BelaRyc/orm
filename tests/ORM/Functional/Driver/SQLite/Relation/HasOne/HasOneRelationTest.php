<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\SQLite\Relation\HasOne;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\HasOne\HasOneRelationTest as CommonTest;

/**
 * @group driver
 * @group driver-sqlite
 */
class HasOneRelationTest extends CommonTest
{
    public const DRIVER = 'sqlite';
}