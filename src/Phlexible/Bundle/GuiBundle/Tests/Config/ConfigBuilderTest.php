<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Config;

use Phlexible\Bundle\GuiBundle\Config\ConfigBuilder;
use Prophecy\Argument;

/**
 * Config builder test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConfigBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyValues()
    {
        $eventDispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher->dispatch(
            'phlexible_gui.get_config',
            Argument::type('Phlexible\Bundle\GuiBundle\Event\GetConfigEvent')
        )->shouldBeCalled();

        $configBuilder = new ConfigBuilder($eventDispatcher->reveal());

        $config = $configBuilder->build();

        $this->assertInstanceOf('Phlexible\Bundle\GuiBundle\Config\Config', $config);
    }
}
