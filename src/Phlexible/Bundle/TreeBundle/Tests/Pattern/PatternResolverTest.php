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
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('page', 'de')->willReturn('pageTitle');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replace('test', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('pageTitle', $result);
    }

    public function testReplaceWithKnownPattern()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $patternResolver = new PatternResolver(array('test' => '%t'), 'title');
        $result = $patternResolver->replace('test', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePattern()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%t', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePatterWithoutPlaceholders()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getTitle('de')->shouldNotBeCalled();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->shouldNotBeCalled();
        $node->getField('page', 'de')->shouldNotBeCalled();
        $node->getField('navigation', 'de')->shouldNotBeCalled();

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('foo', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo', $result);
    }

    public function testReplacePatterWithSitePlaceholder()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getTitle('de')->willReturn('foo');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->shouldNotBeCalled();
        $node->getField('page', 'de')->shouldNotBeCalled();
        $node->getField('navigation', 'de')->shouldNotBeCalled();

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%s-test', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo-test', $result);
    }

    public function testReplacePatterWithElementVersionPlaceholder()
    {
        $site = $this->prophesize('Phlexible\Component\Site\Domain\Site');
        $site->getTitle('de')->shouldNotBeCalled();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getField('backend', 'de')->willReturn('foo');
        $node->getField('page', 'de')->willReturn('bar');
        $node->getField('navigation', 'de')->willReturn('baz');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%b-%p-%n-test', $site->reveal(), $node->reveal(), 'de');

        $this->assertSame('foo-bar-baz-test', $result);
    }
}
