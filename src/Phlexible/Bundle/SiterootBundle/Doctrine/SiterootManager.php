<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\SiterootEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Siteroot manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootManager implements SiterootManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $siterootRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getSiterootRepository()
    {
        if (null === $this->siterootRepository) {
            $this->siterootRepository = $this->entityManager->getRepository('PhlexibleSiterootBundle:Siteroot');
        }

        return $this->siterootRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getSiterootRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getSiterootRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSiteroot(Siteroot $siteroot)
    {
        if ($this->entityManager->contains($siteroot)) {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_UPDATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->flush();

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::UPDATE_SITEROOT, $event);
        } else {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_CREATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            if (null === $siteroot->getId()) {
                $this->applyIdentifier($siteroot);
            }

            $this->entityManager->persist($siteroot);
            $this->entityManager->flush();

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::CREATE_SITEROOT, $event);
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

        $this->entityManager->remove($siteroot);
        $this->entityManager->flush();

        $event = new SiterootEvent($siteroot);
        $this->dispatcher->dispatch(SiterootEvents::DELETE_SITEROOT, $event);
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
