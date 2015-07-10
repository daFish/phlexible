<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator\VersionStrategy;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Online version strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlineVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * {@inheritdoc}
     */
    public function find(NodeContext $node, $language)
    {
        if (!$language) {
            throw new \InvalidArgumentException("No language");
        }

        $element = $this->elementService->findElement($node->getNode()->getTypeId());

        return $this->elementService->findElementVersion(
            $element,
            $node->getTree()->getPublishedVersion($node, $language)
        );
    }
}
