<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Meta;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Element meta set resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMetaSetResolver
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     * @param ElementService          $elementService
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, ElementService $elementService)
    {
        $this->metaSetManager = $metaSetManager;
        $this->elementService = $elementService;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return \Phlexible\Component\MetaSet\Domain\MetaSet
     */
    public function resolve(ElementVersion $elementVersion)
    {
        $elementtype = $this->elementService->findElementtype($elementVersion->getElement());

        if (!$elementtype->getMetaSetId()) {
            return null;
        }

        $metaSet = $this->metaSetManager->find($elementtype->getMetaSetId());

        return $metaSet;
    }
}
