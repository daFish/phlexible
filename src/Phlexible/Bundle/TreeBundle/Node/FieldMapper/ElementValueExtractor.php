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
 * Element value extractor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ElementValueExtractor implements ValueExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($content, array $mapping, $language)
    {
        $values = $content->__getValues();

        if (isset($values[$mapping['dsId']])) {
            return $values[$mapping['dsId']];
        }

        return null;
    }
}
