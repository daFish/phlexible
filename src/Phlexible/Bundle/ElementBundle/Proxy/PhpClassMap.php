<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use CG\Generator\PhpClass;

/**
 * Php class map
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhpClassMap
{
    private $classes = array();

    /**
     * @param string   $name
     * @param PhpClass $class
     *
     * @return $this
     */
    public function add($name, PhpClass $class)
    {
        $this->classes[$name] = $class;

        return $this;

    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->classes[$name]);
    }

    /**
     * @param string $name
     *
     * @return PhpClass
     */
    public function get($name)
    {
        return $this->classes[$name];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->classes;
    }
}
