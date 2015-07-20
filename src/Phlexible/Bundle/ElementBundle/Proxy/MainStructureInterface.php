<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

/**
 * Main structure interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MainStructureInterface extends StructureInterface
{
    /**
     * @return string
     */
    public function __id();

    /**
     * @return string
     */
    public function __version();

    /**
     * @return string
     */
    public function __elementtypeId();

    /**
     * @return int
     */
    public function __elementtypeRevision();

    /**
     * @return string
     */
    public function __elementtypeName();

}
