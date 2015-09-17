<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Tests\Controller;

use Phlexible\Bundle\SiterootBundle\Controller\SitesController;
use Phlexible\Bundle\SiterootBundle\Tests\Model\InMemorySiteManager;
use Phlexible\Component\Site\Domain\Site;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SitesControllerTest extends WebTestCase
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var InMemorySiteManager
     */
    private $siteManager;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var SitesController
     */
    private $controller;

    public function setUp()
    {
        $this->site = new Site('123');
        $this->siteManager = new InMemorySiteManager(array($this->site));
        $formFactory = $this->prophesize('Symfony\Component\Form\FormFactoryInterface');
        $this->form = $this->prophesize('Symfony\Component\Form\FormInterface');
        $router = $this->prophesize('Symfony\Component\Routing\RouterInterface');
        $this->controller = new SitesController($this->siteManager, $formFactory->reveal(), $router->reveal());

        $formFactory->create(Argument::cetera())->willReturn($this->form);
    }

    public function testGetSitesReturnsSites()
    {
        $collection = $this->controller->getSitesAction();

        $this->assertInstanceOf('Phlexible\Component\Site\Domain\SiteCollection', $collection);
        $this->assertSame(1, $collection->total);
        $this->assertSame(array('123' => $this->site), $collection->sites);
    }

    public function testGetSiteReturnsSite()
    {
        $result = $this->controller->getSiteAction('123');

        $this->assertSame($this->site, $result);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetSiteThrowsExceptionForUnknownSite()
    {
        $this->controller->getSiteAction('invalid');
    }

    public function testPostSitesCreatesNewSite()
    {
        $data = array(
            'site' => array(
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'specialTids' => array(
                    array('name' => 'testSpecialTid', 'language' => 'de', 'treeId' => 123),
                ),
                'navigations' => array(
                    array('title' => 'testNavigation', 'startTreeId' => 123),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname', 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $request = new Request(array(), $data);

        $this->form->submit($request)->shouldBeCalled();
        $this->form->isValid()->willReturn(true);

        $result = $this->controller->postSitesAction($request);

        $this->assertCount(2, $this->siteManager->findAll());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
    }

    public function testPostSitesWillReturnViewOnInvalidData()
    {
        $data = array(
            'siteroot' => array(
                'default' => true,
                //'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'navigations' => array(
                    array('title' => null, 'handler' => 'testHandler'),
                ),
                'urls' => array(
                    array('hostname' => null, 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $request = new Request(array(), $data);

        $this->form->submit($request)->shouldBeCalled();
        $this->form->isValid()->willReturn(false);

        $result = $this->controller->postSitesAction($request);

        $this->assertInstanceOf('FOS\RestBundle\View\View', $result);
    }

    public function testPutSiteUpdatesExistingSite()
    {
        $data = array(
            'siteroot' => array(
                'default' => true,
                //'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'navigations' => array(
                    array('title' => 'testTitle', 'startTreeId' => 123),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname', 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $request = new Request(array(), $data);

        $this->form->submit($request)->shouldBeCalled();
        $this->form->isValid()->willReturn(true);

        $result = $this->controller->putSiteAction($request, '123');

        $this->assertCount(1, $this->siteManager->findAll());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
    }

    public function testPutSiteReturnsViewOnInvalidData()
    {
        $data = array(
            'siteroot' => array(
                'default' => true,
                'navigations' => array(
                    array('title' => 'testNavigation'),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname'),
                ),
            ),
        );

        $request = new Request(array(), $data);

        $this->form->submit($request)->shouldBeCalled();
        $this->form->isValid()->willReturn(false);

        $result = $this->controller->putSiteAction($request, '123');

        $this->assertInstanceOf('FOS\RestBundle\View\View', $result);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testPutSiteThrowsNotFoundExceptionForUnknownSite()
    {
        $data = array(
            'siteroot' => array(
                'default' => true,
                'navigations' => array(
                    array('title' => 'testNavigation'),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname'),
                ),
            ),
        );

        $request = new Request(array(), $data);
        $this->controller->putSiteAction($request, 'invalid');
    }

    public function testDeleteSiteDeletesSite()
    {
        $this->controller->deleteSiteAction('123');

        $this->assertNull($this->siteManager->find('123'));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testDeleteSiteThrowsNotFoundExceptionForUnknownSite()
    {
        $this->controller->deleteSiteAction('invalid');
    }
}
