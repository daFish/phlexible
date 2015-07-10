<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserContext;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig element extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementExtension extends \Twig_Extension
{
    /**
     * @var ContentElementLoader
     */
    private $contentElementLoader;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param ContentElementLoader   $contentElementLoader
     * @param TeaserManagerInterface $teaserManager
     * @param RequestStack           $requestStack
     */
    public function __construct(ContentElementLoader $contentElementLoader, TeaserManagerInterface $teaserManager, RequestStack $requestStack)
    {
        $this->contentElementLoader = $contentElementLoader;
        $this->teaserManager = $teaserManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('element', array($this, 'element')),
        );
    }

    /**
     * @param TeaserContext|NodeContext|int $eid
     *
     * @return ContentElement|null
     */
    public function element($eid)
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if ($eid instanceof TeaserContext) {
            $teaser = $eid->getTeaser();
            $eid = $teaser->getTypeId();
            $version = $this->teaserManager->getPublishedVersion($teaser, $language);
        } elseif ($eid instanceof NodeContext) {
            $node = $eid;
            $eid = $node->getTypeId();
            $version = $node->getTree()->getPublishedVersion($node, $language);
        } else {
            return null;
        }

        return $this->contentElementLoader->load($eid, $version, $language);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_element';
    }
}
