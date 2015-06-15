<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\SiterootEvents;
use Phlexible\Bundle\SiterootBundle\SiterootsMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * In memory siteroot manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InMemorySiterootManager implements SiterootManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var Siteroot[]|ArrayCollection
     */
    private $siteroots;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messagePoster
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster)
    {
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;

        $this->siteroots = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->siteroots->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->siteroots->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSiteroot(Siteroot $siteroot)
    {
        if ($this->siteroots->contains($siteroot)) {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_UPDATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            $this->siteroots->set($siteroot->getId(), $siteroot);

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::UPDATE_SITEROOT, $event);

            $message = SiterootsMessage::create('Siteroot updated.', '', null, 'siteroot');
            $this->messagePoster->post($message);
        } else {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_CREATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            if (null === $siteroot->getId()) {
                $this->applyIdentifier($siteroot);
            }

            $this->siteroots->set($siteroot->getId(), $siteroot);

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::CREATE_SITEROOT, $event);

            $message = SiterootsMessage::create('Siteroot created.', '', null, 'siteroot');
            $this->messagePoster->post($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSiteroot(Siteroot $siteroot)
    {
        $event = new SiterootEvent($siteroot);
        if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_DELETE_SITEROOT, $event)->isPropagationStopped()) {
            return;
        }

        $this->siteroots->removeElement($siteroot);

        $event = new SiterootEvent($siteroot);
        $this->dispatcher->dispatch(SiterootEvents::DELETE_SITEROOT, $event);

        $message = SiterootsMessage::create('Siteroot deleted.', '', null, 'siteroot');
        $this->messagePoster->post($message);
    }

    /**
     * Apply UUID as identifier when entity doesn't have one yet.
     *
     * @param Siteroot $siteroot
     */
    private function applyIdentifier(Siteroot $siteroot)
    {
        $reflectionClass = new \ReflectionClass(get_class($siteroot));

        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($siteroot, Uuid::generate());
    }
}
