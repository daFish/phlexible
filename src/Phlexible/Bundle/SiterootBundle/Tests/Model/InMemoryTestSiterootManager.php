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
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Model\SiteManagerInterface;

/**
 * In memory siteroot manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InMemoryTestSiterootManager implements SiteManagerInterface
{
    /**
     * @var Site[]|ArrayCollection
     */
    private $sites;

    /**
     * Constructor.
     */
    public function __construct()
    {
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
            $this->sites->set($site->getId(), $site);
        } else {
            if (null === $site->getId()) {
                $this->applyIdentifier($site);
            }

            $this->sites->set($site->getId(), $site);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSite(Site $site)
    {
        $this->sites->removeElement($site);
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
