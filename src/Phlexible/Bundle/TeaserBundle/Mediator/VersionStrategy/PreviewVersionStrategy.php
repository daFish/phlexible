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
    public function find(TeaserManagerInterface $teaserManager, Teaser $teaser, $language)
    {
        $element = $this->elementService->findElement($teaser->getTypeId());

        $version = $this->version;
        if (!$version) {
        } else {
            $version = $element->getLatestVersion();
        }

        return $this->elementService->findElementVersion($element, $this->version);
    }
}
