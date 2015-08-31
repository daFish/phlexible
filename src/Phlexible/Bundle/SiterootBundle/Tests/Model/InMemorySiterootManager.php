<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Event\SiteEvent;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Site\SiteEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * In memory siteroot manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InMemorySiterootManager implements SiteManagerInterface
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
     * @var Site[]|ArrayCollection
     */
    private $sites;

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

        $this->sites = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->sites->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->sites->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSite(Site $site)
    {
        if ($this->sites->contains($site)) {
            $event = new SiteEvent($site);
            if ($this->dispatcher->dispatch(SiteEvents::BEFORE_UPDATE_SITE, $event)->isPropagationStopped()) {
                return;
            }

            $this->sites->set($site->getId(), $site);

            $event = new SiteEvent($site);
            $this->dispatcher->dispatch(SiteEvents::UPDATE_SITE, $event);
        } else {
            $event = new SiteEvent($site);
            if ($this->dispatcher->dispatch(SiteEvents::BEFORE_CREATE_SITE, $event)->isPropagationStopped()) {
                return;
            }

            if (null === $site->getId()) {
                $this->applyIdentifier($site);
            }

            $this->sites->set($site->getId(), $site);

            $event = new SiteEvent($site);
            $this->dispatcher->dispatch(SiteEvents::CREATE_SITE, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSite(Site $site)
    {
        $event = new SiteEvent($site);
        if ($this->dispatcher->dispatch(SiteEvents::BEFORE_DELETE_SITE, $event)->isPropagationStopped()) {
            return;
        }

        $this->sites->removeElement($site);

        $event = new SiteEvent($site);
        $this->dispatcher->dispatch(SiteEvents::DELETE_SITE, $event);
    }

    /**
     * Apply UUID as identifier when entity doesn't have one yet.
     *
     * @param Site $site
     */
    private function applyIdentifier(Site $site)
    {
        $reflectionClass = new \ReflectionClass(get_class($site));

        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($site, Uuid::generate());
    }
}
