<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Event\ElementtypeUsageEvent;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
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

        // TODO: switch to type manager
        return;
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
