<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\SQLServer\Schema;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Schema\CommonTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class CommonTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
