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
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * In memory siteroot manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InMemoryTestSiterootManager implements SiterootManagerInterface
{
    /**
     * @var Siteroot[]|ArrayCollection
     */
    private $siteroots;

    /**
     * Constructor.
     */
    public function __construct()
    {
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
            $this->siteroots->set($siteroot->getId(), $siteroot);
        } else {
            if (null === $siteroot->getId()) {
                $this->applyIdentifier($siteroot);
            }

            $this->siteroots->set($siteroot->getId(), $siteroot);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSiteroot(Siteroot $siteroot)
    {
        $this->siteroots->removeElement($siteroot);
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
