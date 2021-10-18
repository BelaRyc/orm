<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\MySQL\Driver\Common\Relation;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\DoubleLinkedTest as CommonTest;

/**
 * @group driver
 * @group driver-mysql
 */
class DoubleLinkedTest extends CommonTest
{
    public const DRIVER = 'mysql';
}