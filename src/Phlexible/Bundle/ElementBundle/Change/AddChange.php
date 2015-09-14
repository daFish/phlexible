<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Elementtype add change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddChange extends Change
{
    /**
     * @var bool
     */
    private $needImport = false;

    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     * @param bool        $needImport
     */
    public function __construct(Elementtype $elementtype, $needImport)
    {
        parent::__construct($elementtype);

        $this->needImport = $needImport;
    }

    /**
     * @return bool
     */
    public function getNeedImport()
    {
        return $this->needImport;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Elementtype added';
    }
}
