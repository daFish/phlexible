<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Structure node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class StructureNode extends Node
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
