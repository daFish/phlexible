<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Suggest\Tests;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Component\Suggest\SuggestEvents;
use Phlexible\Component\Suggest\Event\GarbageCollectEvent;
use Phlexible\Component\Suggest\GarbageCollector\GarbageCollector;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DataSource Test
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class GarbageCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GarbageCollector
     */
    private $garbageCollector;

    /**
     * @var \Phlexible\Component\Suggest\Model\DataSourceManagerInterface|ObjectProphecy
     */
    private $manager;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $eventDispatcher;

    /**
     * @var DataSource
     */
    private $datasource;

    public function setUp()
    {

        $this->eventDispatcher = new EventDispatcher();
        $this->manager = $this->prophesize('Phlexible\Component\Suggest\Model\DataSourceManagerInterface');
        $this->garbageCollector = new GarbageCollector($this->manager->reveal(), $this->eventDispatcher);

        $this->datasource = new DataSource();
        $this->datasource->setTitle('testDatasource');
        $values = new DataSourceValueBag();
        $values->setLanguage('de');
        $this->datasource->addValueBag($values);
    }

    public function testEvents()
    {
        $fired = 0;
        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function() use (&$fired) {
                $fired++;
            }
        );
        $this->eventDispatcher->addListener(
            SuggestEvents::GARBAGE_COLLECT,
            function() use (&$fired) {
                $fired++;
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $this->garbageCollector->run();

        $this->assertSame(2, $fired);
    }

    public function testRunWithNoValues()
    {
        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run();

        $this->assertCount(0, $result['testDatasource']['de']['remove']);
        $this->assertCount(0, $result['testDatasource']['de']['active']);
        $this->assertCount(0, $result['testDatasource']['de']['inactive']);
    }

    public function testRunRemovesUnusedValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array(),
                'remove' => array('value1', 'value2'),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunRemovesUnusedValuesInModeUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array(),
                'remove' => array('value1', 'value2'),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunMarksUnusedValuesAsInactiveInModeUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array('value1', 'value2'),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array(),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeRemoveUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array(),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array(),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsInactiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array('value1', 'value2'),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunRemovesInactiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array(),
                'remove' => array('value1', 'value2'),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsInactiveValuesInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(array('value1', 'value2'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array(),
                'inactive' => array('value1', 'value2'),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
                $event->markInactive(array('value3', 'value4'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array('value3', 'value4'),
                'remove' => array('value5', 'value6'),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeRemoveUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
                $event->markInactive(array('value3', 'value4'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array(),
                'remove' => array('value3', 'value4', 'value5', 'value6'),
            ),
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            SuggestEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(array('value1', 'value2'));
                $event->markInactive(array('value3', 'value4'));
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn(array($this->datasource));
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            array(
                'active' => array('value1', 'value2'),
                'inactive' => array('value3', 'value4', 'value5', 'value6'),
                'remove' => array(),
            ),
            $result['testDatasource']['de']
        );
    }
}
