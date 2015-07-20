<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;

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
     * @param array  $structure
     * @param string $language
     * @param array  $mapping
     *
     * @return string|null
     */
    public function map(array $structure, $language, array $mapping);
}
