<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
