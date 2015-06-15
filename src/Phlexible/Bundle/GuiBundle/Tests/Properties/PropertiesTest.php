<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Properties;

use Phlexible\Bundle\GuiBundle\Entity\Property;
use Phlexible\Bundle\GuiBundle\Properties\Properties;

/**
 * Properties test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testProperties()
    {
        $property = new Property();
        $property->setComponent('foo');
        $property->setName('bar');
        $property->setValue('baz');
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManagerInterface');
        $entityRepository = $this->prophesize('Doctrine\Common\Persistence\ObjectRepository');
        $entityManager->getRepository('PhlexibleGuiBundle:Property')->willReturn($entityRepository->reveal());
        $entityRepository->findAll()->willReturn(array($property));
        $entityManager->remove($property)->shouldBeCalled();

        $properties = new Properties($entityManager->reveal());

        $this->assertAttributeEmpty('properties', $properties);
        $this->assertFalse($properties->has('foo', 'bax'));
        $this->assertAttributeCount(1, 'properties', $properties);
        $this->assertTrue($properties->has('foo', 'bar'));
        $this->assertSame('baz', $properties->get('foo', 'bar'));
        $properties->remove('foo', 'bar');
        $this->assertAttributeCount(0, 'properties', $properties);
    }
}
