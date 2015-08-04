<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Event\ElementtypeUsageEvent;
use Phlexible\Component\Elementtype\Model\ViabilityManagerInterface;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var \Phlexible\Component\Elementtype\ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @param ElementtypeService        $elementtypeService
     * @param ViabilityManagerInterface $viabilityManager
     */
    public function __construct(ElementtypeService $elementtypeService, ViabilityManagerInterface $viabilityManager)
    {
        $this->elementtypeService = $elementtypeService;
        $this->viabilityManager = $viabilityManager;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtype = $event->getElementtype();

        if ($elementtype->getType() === 'reference') {
            $elementtypes = $this->elementtypeService->findElementtypesUsingReferenceElementtype($elementtype);
            foreach ($elementtypes as $elementtype) {
                $event->addUsage(
                    new Usage(
                        $elementtype->getType() . ' elementtype',
                        'reference',
                        $elementtype->getId(),
                        $elementtype->getName(),
                        $elementtype->getRevision()
                    )
                );
            }
        }

        if ($elementtype->getType() === 'layout') {
            foreach ($this->viabilityManager->findAllowedParents($elementtype) as $viability) {
                $viabilityElementtype = $this->elementtypeService->findElementtype($viability->getUnderElementtypeId());
                $event->addUsage(
                    new Usage(
                        $viabilityElementtype->getType() . ' elementtype',
                        'layout area',
                        $viabilityElementtype->getId(),
                        $viabilityElementtype->getName(),
                        $viabilityElementtype->getRevision()
                    )
                );
            }
        }
    }
}
