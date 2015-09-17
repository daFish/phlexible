<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Field mapper interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
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
