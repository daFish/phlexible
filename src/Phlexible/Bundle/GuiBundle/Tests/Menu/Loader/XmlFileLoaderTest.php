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
use Phlexible\Bundle\GuiBundle\Menu\Loader\YamlFileLoader;

/**
 * YAML file loader test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new YamlFileLoader();

        $this->assertTrue($loader->supports('test.xml'));
        $this->assertFalse($loader->supports('test.yml'));
    }

    public function testLoad()
    {
        $items = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item id="menus" handle="menus" />

    <item id="config" parent="menus" handle="configuration">
        <roles satisfy="any">
            <role>a</role>
            <role>b</role>
        </roles>
    </item>
</items>
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $items = $loader->load(vfsStream::url('root/items.yml'));

        $this->assertCount(2, $items);
        $this->assertArrayHasKey('menus', $items->getItems());
        $this->assertSame('menus', $items->getItems()['menus']->getHandle());
        $this->assertArrayHasKey('config', $items->getItems());
        $this->assertSame('configuration', $items->getItems()['config']->getHandle());
        $this->assertSame('menus', $items->getItems()['config']->getParent());
        $this->assertSame(array('a', 'b'), $items->getItems()['config']->getRoles());
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithInvalidParent()
    {
        $items = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item id="config" parent="123" handle="configuration" />
</items>
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $items = $loader->load(vfsStream::url('root/items.yml'));

    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithMissingHandler()
    {
        $items = <<<EOF
<items xmlns="http://phlexible.net/schema/menu"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://phlexible.net/schema/menu http://phlexible.net/schema/menu/menu-1.0.xsd">

    <item id="config" parent="123" />
</items>
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $loader->load(vfsStream::url('root/items.yml'));
    }
}
