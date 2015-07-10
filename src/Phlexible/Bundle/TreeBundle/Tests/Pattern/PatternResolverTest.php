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
        $siteroot = $this->prophesize('Phlexible\Bundle\SiterootBundle\Entity\Siteroot');
        $elementVersion = $this->prophesize('Phlexible\Bundle\ElementBundle\Entity\ElementVersion');
        $elementVersion->getPageTitle('de')->willReturn('pageTitle');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replace('test', $siteroot->reveal(), $elementVersion->reveal(), 'de');

        $this->assertSame('pageTitle', $result);
    }

    public function testReplaceWithKnownPattern()
    {
        $siteroot = $this->prophesize('Phlexible\Bundle\SiterootBundle\Entity\Siteroot');
        $elementVersion = $this->prophesize('Phlexible\Bundle\ElementBundle\Entity\ElementVersion');

        $patternResolver = new PatternResolver(array('test' => '%t'), 'title');
        $result = $patternResolver->replace('test', $siteroot->reveal(), $elementVersion->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePattern()
    {
        $siteroot = $this->prophesize('Phlexible\Bundle\SiterootBundle\Entity\Siteroot');
        $elementVersion = $this->prophesize('Phlexible\Bundle\ElementBundle\Entity\ElementVersion');

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('%t', $siteroot->reveal(), $elementVersion->reveal(), 'de');

        $this->assertSame('title', $result);
    }

    public function testReplacePatterWithoutPlaceholders()
    {
        $siteroot = $this->prophesize('Phlexible\Bundle\SiterootBundle\Entity\Siteroot');
        $siteroot->getTitle('de')->shouldNotBeCalled();
        $elementVersion = $this->prophesize('Phlexible\Bundle\ElementBundle\Entity\ElementVersion');
        $elementVersion->getBackendTitle('de')->shouldNotBeCalled();
        $elementVersion->getPageTitle('de')->shouldNotBeCalled();
        $elementVersion->getNavigationTitle('de')->shouldNotBeCalled();

        $patternResolver = new PatternResolver(array(), 'title');
        $result = $patternResolver->replacePattern('foo', $siteroot->reveal(), $elementVersion->reveal(), 'de');

        $this->assertSame('foo', $result);
    }
}
