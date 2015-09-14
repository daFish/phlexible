<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\NodeType\Tests;

use Phlexible\Component\NodeType\Domain\NodeTypeConstraint;
use Phlexible\Component\NodeType\NodeTypeConstraintsResolver;

/**
 * Node type constraint resolver test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeTypeConstraintsResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveWithoutChildNodes()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getNodeConstraints()->willReturn(array(
            'homepage' => new NodeTypeConstraint('homepage', true),
        ));

        $siteManager = $this->prophesize('Phlexible\Component\Site\Model\SiteManagerInterface');
        $siteManager->find(1)->willReturn($site->reveal());

        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getSiterootId()->willReturn(1);
        $node->getContentType()->willReturn('homepage');

        $resolver = new NodeTypeConstraintsResolver($siteManager->reveal());

        $types = $resolver->resolve($node->reveal());

        $this->assertSame(array(), $types);
    }

    public function testResolveWithChildNodeThatIsNotConfigured()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getNodeConstraints()->willReturn(array(
            'homepage' => new NodeTypeConstraint('homepage', true, array('article')),
        ));

        $siteManager = $this->prophesize('Phlexible\Component\Site\Model\SiteManagerInterface');
        $siteManager->find(1)->willReturn($site->reveal());

        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getSiterootId()->willReturn(1);
        $node->getContentType()->willReturn('homepage');

        $resolver = new NodeTypeConstraintsResolver($siteManager->reveal());

        $types = $resolver->resolve($node->reveal());

        $this->assertSame(array(), $types);
    }

    public function testResolveWithChildNodeThatIsNotAllowed()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getNodeConstraints()->willReturn(array(
            'homepage' => new NodeTypeConstraint('homepage', true, array('article')),
            'article'  => new NodeTypeConstraint('article', false),
        ));

        $siteManager = $this->prophesize('Phlexible\Component\Site\Model\SiteManagerInterface');
        $siteManager->find(1)->willReturn($site->reveal());

        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getSiterootId()->willReturn(1);
        $node->getContentType()->willReturn('homepage');

        $resolver = new NodeTypeConstraintsResolver($siteManager->reveal());

        $types = $resolver->resolve($node->reveal());

        $this->assertSame(array(), $types);
    }

    public function testResolveWithChildNodeThatIsAllowed()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getNodeConstraints()->willReturn(array(
            'homepage' => new NodeTypeConstraint('homepage', true, array('article')),
            'article'  => new NodeTypeConstraint('article', true),
        ));

        $siteManager = $this->prophesize('Phlexible\Component\Site\Model\SiteManagerInterface');
        $siteManager->find(1)->willReturn($site->reveal());

        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getSiterootId()->willReturn(1);
        $node->getContentType()->willReturn('homepage');

        $resolver = new NodeTypeConstraintsResolver($siteManager->reveal());

        $types = $resolver->resolve($node->reveal());

        $this->assertSame(array('article'), $types);
    }
}
