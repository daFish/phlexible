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

/**
 * Redirect route
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @ORM\MappedSuperclass()
 */
class RedirectRoute extends Route
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $uri;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $routeName;

    /**
     * Whether this is a permanent redirect. Defaults to false.
     *
     * @var boolean
     */
    private $permanent = false;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     *
     * @return $this
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param mixed $permanent
     *
     * @return $this
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

}
