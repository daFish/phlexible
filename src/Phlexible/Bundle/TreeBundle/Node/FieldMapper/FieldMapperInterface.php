<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Field mapper interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface FieldMapperInterface
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function accept($key);

    /**
     * @param ValueExtractorInterface $valueExtractor
     * @param mixed                   $content
     * @param array                   $mapping
     * @param string                  $language
     *
     * @return string|null
     */
    public function map(ValueExtractorInterface $valueExtractor, $content, array $mapping, $language);
}
