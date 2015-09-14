<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Menu\Loader;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Menu\Loader\XmlFileLoader;

/**
 * YAML file loader test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new XmlFileLoader();

        $this->assertTrue($loader->supports('test.xml'));
        $this->assertFalse($loader->supports('test.yml'));
    }

    public function testLoad()
    {
        $menuItems = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item name="menus" handle="menus" />

    <item name="config" parent="menus" handle="configuration">
        <roles satisfy="any">
            <role>a</role>
            <role>b</role>
        </roles>
    </item>
</items>
EOF;

        vfsStream::setup('root', null, array('items.xml' => $menuItems));

        $loader = new XmlFileLoader();
        $menuItems = $loader->load(vfsStream::url('root/items.xml'));

        $this->assertCount(2, $menuItems);
        $items = $menuItems->getItems();
        $this->assertArrayHasKey('menus', $items);
        $this->assertSame('menus', $items['menus']->getHandle());
        $this->assertArrayHasKey('config', $items);
        $this->assertSame('configuration', $items['config']->getHandle());
        $this->assertSame('menus', $items['config']->getParent());
        $this->assertSame(array('a', 'b'), $items['config']->getRoles());
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     * @expectedExceptionMessage The attribute 'name' is required but missing
     */
    public function testValidateWithMissingName()
    {
        $items = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item />
</items>
EOF;

        vfsStream::setup('root', null, array('missingName.xml' => $items));

        $loader = new XmlFileLoader();
        $items = $loader->load(vfsStream::url('root/missingName.xml'));

        print_r($items);
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     * @expectedExceptionMessage The attribute 'name' is required but missing
     */
    public function testLoadWithMissingHandler()
    {
        $items = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item name="config" />
</items>
EOF;

        vfsStream::setup('root', null, array('missingHandler.xml' => $items));

        $loader = new XmlFileLoader();
        $loader->load(vfsStream::url('root/missingHandler.xml'));
    }
}
