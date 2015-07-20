<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;

/**
 * Teaser mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingTeaserMediator implements TeaserMediatorInterface
{
    /**
     * @var TeaserMediatorInterface[]
     */
    private $mediators = array();

    /**
     * @param TeaserMediatorInterface[] $mediators
     */
    public function __construct(array $mediators = array())
    {
        foreach ($mediators as $mediator) {
            $this->addMediator($mediator);
        }
    }

    /**
     * @param TeaserMediatorInterface $mediator
     *
     * @return $this
     */
    public function addMediator(TeaserMediatorInterface $mediator)
    {
        $this->mediators[] = $mediator;

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function accept(Teaser $teaser)
    {
        return $this->findMediator($teaser) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getField(TeaserManagerInterface $teaserManager, Teaser $teaser, $field, $language)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getField($teaserManager, $teaser, $field, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getContentDocument(TeaserManagerInterface $teaserManager, Teaser $teaser, $language)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getContentDocument($teaserManager, $teaser, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(TeaserManagerInterface $teaserManager, Teaser $teaser)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getTemplate($teaserManager, $teaser);
        }

        return null;
    }

    /**
     * @param Teaser $teaser
     *
     * @return TeaserMediatorInterface|null
     */
    private function findMediator(Teaser $teaser)
    {
        foreach ($this->mediators as $mediator) {
            if ($mediator->accept($teaser)) {
                return $mediator;
            }
        }

        return null;
    }
}
