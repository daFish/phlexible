<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

/**
 * Path
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Path implements \Countable
{
    /**
     * @var array
     */
    private $parts = array();

    /**
     * @param array $parts
     */
    public function __construct(array $parts = array())
    {
        foreach ($parts as $part) {
            $this->append($part);
        }
    }

    /**
     * @param string $part
     *
     * @return $this
     */
    public function append($part)
    {
        $this->parts[] = $part;

        return $this;
    }

    /**
     * @param string $part
     *
     * @return string
     */
    public function prepend($part)
    {
        array_unshift($this->parts, $part);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode($this->parts);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->parts);
    }
}
