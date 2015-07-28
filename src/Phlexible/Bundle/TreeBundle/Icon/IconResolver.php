<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Icon;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
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
     * @param RouterInterface $router
     * @param ElementService  $elementService
     */
    public function __construct( RouterInterface $router, ElementService $elementService)
    {
        $this->router = $router;
        $this->elementService = $elementService;
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

        return '/bundles/phlexibletree/node-icons/' . $icon;
    }

    /**
     * Resolve element type to icon
     *
     * @param Elementtype $elementtype
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
    private function resolveElementSource(ElementSource $elementSource)
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
            if ($node->isPublished($language)) {
                $parameters['status'] = $node->isAsync($language) ? 'async': 'online';
            }

            if ($node->getTree()->isInstance($node)) {
                $parameters['instance'] = $node->getTree()->isInstanceMaster($node) ? 'master' : 'slave';
            }

            if ($node->getSortMode() !== TreeInterface::SORT_MODE_FREE) {
                $parameters['sort'] = $node->getSortMode() . '_' . $node->getSortDir();
            }
        }

        $element = $this->elementService->findElement($node->getContentId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $parameters['icon'] = basename($this->resolveElementSource($this->elementService->findElementSource($element->getElementtypeId())));

        return $this->router->generate('tree_icon', $parameters);
    }
}
