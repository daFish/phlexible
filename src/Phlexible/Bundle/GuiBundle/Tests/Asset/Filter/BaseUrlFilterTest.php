<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;

/**
 * Base url filter test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BaseUrlFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new BaseUrlFilter('/', '/app.php');

        $this->assertSame('/app.php/test.gif', $filter->filter('/BASE_PATH/test.gif'));
        $this->assertSame('/test.gif', $filter->filter('/BASE_URL/test.gif'));
        $this->assertSame('/app.php/bundles/test.gif', $filter->filter('/BUNDLES_PATH/test.gif'));
        $this->assertSame('/bundles/test.gif', $filter->filter('/BUNDLES_URL/test.gif'));
    }
}
