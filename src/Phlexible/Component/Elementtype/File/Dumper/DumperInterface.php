<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\File\Dumper;

use FluentDOM\Document;
use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param \Phlexible\Component\Elementtype\Domain\Elementtype $elementtype
     *
     * @return Document
     */
    public function dump(Elementtype $elementtype);
}
