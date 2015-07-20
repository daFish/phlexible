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
 * Latest version strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PreviewVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var int|null
     */
    private $version;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * @param int|null $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function findElementVersion(NodeContext $node, $language)
    {
        $element = $this->elementService->findElement($node->getContentId());

        if (!$element) {
            return null;
        }

        $version = $this->version;
        if (!$version) {
            $version = $element->getLatestVersion();
        }

        return $this->elementService->findElementVersion($element, $version);
    }
}
