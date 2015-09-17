<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Infobar;

/**
 * Infobar collection.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InfobarCollection
{
    /**
     * @var Infobar[]
     */
    private $infobars = array();

    /**
     * Constructor.
     *
     * @param Infobar[] $infobars
     */
    public function __construct(array $infobars = array())
    {
        foreach ($infobars as $infobar) {
            $this->add($infobar);
        }
    }

    /**
     * Add infobar.
     *
     * @param Infobar $infobar
     *
     * @return $this
     */
    public function add(Infobar $infobar)
    {
        $this->infobars[] = $infobar;

        return $this;
    }

    /**
     * Return array representation of infobars.
     *
     * @return Infobar[]
     */
    public function all()
    {
        return $this->infobars;
    }
}
