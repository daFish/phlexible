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
