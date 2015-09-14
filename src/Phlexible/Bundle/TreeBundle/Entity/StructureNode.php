<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\TreeBundle\Model\StructureInterface;
use Phlexible\Component\Node\Domain\Node;

/**
 * Structure node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class StructureNode extends Node implements StructureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInNavigation()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedAuthentication()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityExpression()
    {
        return null;
    }
}
