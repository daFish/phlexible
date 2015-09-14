<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Tests\File;

use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\File\SiteManager;
use Phlexible\Component\Site\File\SiteRepositoryInterface;
use Phlexible\Component\Site\SiteEvents;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Site manager test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SiteRepositoryInterface
     */
    private $repository;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var SiteManager
     */
    private $manager;

    public function setUp()
    {
        $this->repository = $this->prophesize('Phlexible\Component\Site\File\SiteRepositoryInterface');
        $validator = $this->prophesize('Symfony\Component\Validator\Validator\ValidatorInterface');
        $this->dispatcher = new EventDispatcher();

        $this->manager = new SiteManager(
            $this->repository->reveal(),
            $validator->reveal(),
            $this->dispatcher
        );

        $validator->validate(Argument::cetera())->willReturn(new ConstraintViolationList());
    }

    public function testFindReturnsSite()
    {
        $site = new Site();
        $this->repository->load(1)->willReturn($site);

        $result = $this->manager->find(1);

        $this->assertSame($site, $result);
    }

    public function testFindReturnsNullOnUnknownSite()
    {
        $this->repository->load('invalid')->willReturn(null);

        $result = $this->manager->find('invalid');

        $this->assertNull($result);
    }

    public function testFindAllReturnsSites()
    {
        $sites = array(new Site());
        $this->repository->loadAll()->willReturn($sites);

        $result = $this->manager->findAll();
        $this->assertSame($sites, $result);
    }

    public function testUpdateSiteDispatchesCreateSiteEvents()
    {
        $site = new Site();
        $listener = new TestEventListener();

        $this->dispatcher->addListener(SiteEvents::BEFORE_CREATE_SITE, array($listener, 'beforeCreateSite'));
        $this->dispatcher->addListener(SiteEvents::CREATE_SITE, array($listener, 'createSite'));

        $this->manager->updateSite($site);

        $this->assertTrue($listener->beforeCreateSiteInvoked);
        $this->assertTrue($listener->createSiteInvoked);
    }

    /**
     * @expectedException \Phlexible\Component\Site\Exception\CreateCancelledException
     */
    public function testCancellingBeforeCreateSiteEventStopsCreate()
    {
        $site = new Site();
        $listener = new TestEventListener();

        $this->dispatcher->addListener(SiteEvents::BEFORE_CREATE_SITE, array($listener, 'beforeCreateSiteWithStop'));
        $this->dispatcher->addListener(SiteEvents::CREATE_SITE, array($listener, 'createSite'));

        $this->manager->updateSite($site);

        $this->assertTrue($listener->beforeCreateSiteInvoked);
        $this->assertFalse($listener->createSiteInvoked);
    }

    public function testUpdateSiteDispatchesUpdateSiteEvents()
    {
        $site = new Site(123);
        $listener = new TestEventListener();

        $this->dispatcher->addListener(SiteEvents::BEFORE_UPDATE_SITE, array($listener, 'beforeUpdateSite'));
        $this->dispatcher->addListener(SiteEvents::UPDATE_SITE, array($listener, 'updateSite'));

        $this->manager->updateSite($site);

        $this->assertTrue($listener->beforeUpdateSiteInvoked);
        $this->assertTrue($listener->updateSiteInvoked);
    }

    /**
     * @expectedException \Phlexible\Component\Site\Exception\UpdateCancelledException
     */
    public function testCancellingBeforeUpdateSiteEventStopsUpdate()
    {
        $site = new Site(123);
        $listener = new TestEventListener();

        $this->dispatcher->addListener(SiteEvents::BEFORE_UPDATE_SITE, array($listener, 'beforeUpdateSiteWithStop'));
        $this->dispatcher->addListener(SiteEvents::UPDATE_SITE, array($listener, 'updateSite'));

        $this->manager->updateSite($site);

        $this->assertTrue($listener->beforeUpdateSiteInvoked);
        $this->assertFalse($listener->updateSiteInvoked);
    }

    public function testDeleteSite()
    {
        $this->markTestSkipped('Need implementation');

        $site = new Site();

        $this->manager->deleteSite($site);
    }
}


class TestEventListener
{
    public $beforeCreateSiteInvoked = false;
    public $createSiteInvoked = false;
    public $beforeUpdateSiteInvoked = false;
    public $updateSiteInvoked = false;

    public function beforeCreateSite(Event $e)
    {
        $this->beforeCreateSiteInvoked = true;
    }

    public function beforeCreateSiteWithStop(Event $e)
    {
        $this->beforeCreateSiteInvoked = true;
        $e->stopPropagation();
    }

    public function createSite(Event $e)
    {
        $this->createSiteInvoked = true;
    }

    public function beforeUpdateSite(Event $e)
    {
        $this->beforeUpdateSiteInvoked = true;
    }

    public function beforeUpdateSiteWithStop(Event $e)
    {
        $this->beforeUpdateSiteInvoked = true;
        $e->stopPropagation();
    }

    public function updateSite(Event $e)
    {
        $this->updateSiteInvoked = true;
    }
}
