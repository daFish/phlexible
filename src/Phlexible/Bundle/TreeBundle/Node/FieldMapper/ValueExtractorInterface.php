<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Value extractor interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface ValueExtractorInterface
{
    /**
     * @param mixed  $content
     * @param array  $mapping
     * @param string $language
     *
     * @return string|null
     */
    public function extract($content, array $mapping, $language);
}
