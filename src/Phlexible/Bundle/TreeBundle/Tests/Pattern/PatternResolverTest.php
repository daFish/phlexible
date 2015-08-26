<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tests\Pattern;

use Phlexible\Bundle\TreeBundle\Pattern\PatternResolver;

/**
 * Pattern resolver test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PatternResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testReplaceWithUnknownPatternWillFallbackToElementVersionPageTitle()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('page', 'de')->willReturn('pageTitle');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replace('test', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('pageTitle', $result);
    }

    public function testReplaceWithKnownPattern()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $patternResolver = new PatternResolver(array('test' => '%t'), 'title');
        $result = $patternResolver->replace('test', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePattern()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%t', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePatterWithoutPlaceholders()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $siteroot->getTitle('de')->shouldNotBeCalled();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->shouldNotBeCalled();
        $node->getField('page', 'de')->shouldNotBeCalled();
        $node->getField('navigation', 'de')->shouldNotBeCalled();

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('foo', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo', $result);
    }

    public function testReplacePatterWithSiterootPlaceholder()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $siteroot->getTitle('de')->willReturn('foo');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->shouldNotBeCalled();
        $node->getField('page', 'de')->shouldNotBeCalled();
        $node->getField('navigation', 'de')->shouldNotBeCalled();

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%s-test', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo-test', $result);
    }

    public function testReplacePatterWithElementVersionPlaceholder()
    {
        $siteroot = $this->prophesize('Phlexible\Component\Site\Domain\Siteroot');
        $siteroot->getTitle('de')->shouldNotBeCalled();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->willReturn('foo');
        $node->getField('page', 'de')->willReturn('bar');
        $node->getField('navigation', 'de')->willReturn('baz');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%b-%p-%n-test', $siteroot->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo-bar-baz-test', $result);
    }
}
