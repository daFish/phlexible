<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

use Phlexible\Bundle\TreeBundle\Entity\NodeLink;

/**
 * Link extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LinkExtractorInterface
{
    /**
     * @param mixed $value
     *
     * @return NodeLink[]|null
     */
    public function extract($value);
}
