<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Compressor;

/**
 * Simple javascript compressor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SimpleJavascriptCompressor extends AbstractStringCompressor
{
    /**
     * {@inheritdoc}
     */
    public function compressString($buffer)
    {
        return $buffer;
    }
}
