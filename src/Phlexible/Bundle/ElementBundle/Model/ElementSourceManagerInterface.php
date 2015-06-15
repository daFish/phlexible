<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Element source manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementSourceManagerInterface
{
    /**
     * @param string $elementtypeId
     *
     * @return ElementSource
     */
    public function findElementSource($elementtypeId);

    /**
     * @param string $type
     *
     * @return ElementSource[]
     */
    public function findByType($type);

    /**
     * @param string $elementtypeId
     *
     * @return Elementtype
     */
    public function findElementtype($elementtypeId);

    /**
     * @param string $type
     *
     * @return Elementtype[]
     */
    public function findElementtypesByType($type);

    /**
     * @param ElementSource $elementSource
     *
     * @return Elementtype
     */
    public function findElementtypeByElementSource(ElementSource $elementSource);

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementSource[]
     */
    public function findOutdatedElementSources(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementSource[]
     */
    public function findByElementtype(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementSource
     */
    public function findOneByElementtypeAndRevision(Elementtype $elementtype);

    /**
     * @param ElementSource $elementSource
     * @param bool          $flush
     */
    public function updateElementSource(ElementSource $elementSource, $flush = true);

    /**
     * @param ElementSource $elementSource
     */
    public function deleteElementSource(ElementSource $elementSource);
}
