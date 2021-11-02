<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\MySQL\Mapper;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Mapper\MapperTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class MapperTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
