<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Tests\Permission;

use Phlexible\Component\AccessControl\Permission\Permission;

/**
 * Permission test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testTestBit()
    {
        $permission1 = new Permission('permission1', 1);
        $permission2 = new Permission('permission2', 2);
        $permission3 = new Permission('permission4', 4);

        $this->assertTrue($permission1->test(1));
        $this->assertFalse($permission1->test(2));
        $this->assertFalse($permission1->test(4));

        $this->assertFalse($permission2->test(1));
        $this->assertTrue($permission2->test(2));
        $this->assertFalse($permission2->test(4));

        $this->assertFalse($permission3->test(1));
        $this->assertFalse($permission3->test(2));
        $this->assertTrue($permission3->test(4));
    }
}
