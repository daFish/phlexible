<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator\VersionStrategy;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;

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
     * @param TeaserManagerInterface $teaserManager
     */
    public function setTeaserManager(TeaserManagerInterface $teaserManager)
    {
        $this->teaserManager = $teaserManager;
    }

    /**
     * {@inheritdoc}
     */
    public function find(TeaserManagerInterface $teaserManager, Teaser $teaser, $language)
    {
        if (!$language) {
            throw new \InvalidArgumentException("No language");
        }

        $element = $this->elementService->findElement($teaser->getTypeId());

        return $this->elementService->findElementVersion(
            $element,
            $teaserManager->getPublishedVersion($teaser, $language)
        );
    }
}
