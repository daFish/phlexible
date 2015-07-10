<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Icon;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Symfony\Component\Routing\RouterInterface;

/**
 * Icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param RouterInterface        $router
     * @param ElementService         $elementService
     * @param TeaserManagerInterface $teaserManager
     */
    public function __construct(
        RouterInterface $router,
        ElementService $elementService,
        TeaserManagerInterface $teaserManager)
    {
        $this->router = $router;
        $this->elementService = $elementService;
        $this->teaserManager = $teaserManager;
    }

    /**
     * Resolve icon
     *
     * @param string $icon
     *
     * @return string
     */
    public function resolveIcon($icon)
    {
        if (!$icon) {
            $icon = '_fallback.gif';
        }

        return '/bundles/phlexibleelementtype/elementtypes/' . $icon;
    }

    /**
     * Resolve element type to icon
     *
     * @param \Phlexible\Component\Elementtype\Model\Elementtype $elementtype
     *
     * @return string
     */
    public function resolveElementtype(Elementtype $elementtype)
    {
        return $this->resolveIcon($elementtype->getIcon());
    }

    /**
     * Resolve element source to icon
     *
     * @param ElementSource $elementSource
     *
     * @return string
     */
    public function resolveElementSource(ElementSource $elementSource)
    {
        return $this->resolveIcon($elementSource->getIcon());
    }

    /**
     * Resolve element to icon
     *
     * @param Element $element
     *
     * @return string
     */
    public function resolveElement(Element $element)
    {
        $elementSource = $this->elementService->findElementSource($element->getElementtypeId());

        return $this->resolveElementSource($elementSource);
    }

    /**
     * Resolve tree node to icon
     *
     * @param NodeContext $node
     * @param string      $language
     *
     * @return string
     */
    public function resolveNode(NodeContext $node, $language)
    {
        $parameters = array();

        if (!$node->getTree()->isRoot($node)) {
            $tree = $node->getTree();

            if ($tree->isPublished($node, $language)) {
                $parameters['status'] = $tree->isAsync($node, $language) ? 'async': 'online';
            }

            if ($tree->isInstance($node)) {
                $parameters['instance'] = $tree->isInstanceMaster($node) ? 'master' : 'slave';
            }

            if ($node->getSortMode() !== TreeInterface::SORT_MODE_FREE) {
                $parameters['sort'] = $node->getSortMode() . '_' . $node->getSortDir();
            }
        }

        $element = $this->elementService->findElement($node->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $parameters['icon'] = basename($this->resolveElementSource($this->elementService->findElementSource($element->getElementtypeId())));

        return $this->router->generate('elements_icon', $parameters);
    }

    /**
     * Resolve teaser to icon
     *
     * @param Teaser $teaser
     * @param string $language
     *
     * @return string
     */
    public function resolveTeaser(Teaser $teaser, $language)
    {
        $parameters = array();

        if ($this->teaserManager->isPublished($teaser, $language)) {
            $parameters['status'] = $this->teaserManager->isAsync($teaser, $language) ? 'async': 'online';
        }

        if ($this->teaserManager->isInstance($teaser)) {
            $parameters['instance'] = $this->teaserManager->isInstanceMaster($teaser) ? 'master' : 'slave';
        }

        $element = $this->elementService->findElement($teaser->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $parameters['icon'] = basename($this->resolveElementSource($this->elementService->findElementSource($element->getElementtypeId())));

        return $this->router->generate('elements_icon', $parameters);
    }
}
