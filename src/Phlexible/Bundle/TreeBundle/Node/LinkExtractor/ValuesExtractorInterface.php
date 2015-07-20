<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

/**
 * Values extractor interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface ValuesExtractorInterface
{
    /**
     * @param mixed  $content
     * @param string $language
     *
     * @return array
     */
    public function extract($content, $language);
}
