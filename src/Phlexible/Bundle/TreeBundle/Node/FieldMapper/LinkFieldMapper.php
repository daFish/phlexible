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
 * Link field mapper.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class LinkFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, array('forward'));
    }

    /**
     * {@inheritdoc}
     */
    public function map(ValueExtractorInterface $valueExtractor, $content, array $mapping, $language)
    {
        $title = $valueExtractor->extract($content, $mapping['fields'][0], $language);

        if (!$title) {
            return null;
        }

        $value = $title;

        return is_array($value) ? json_encode($value) : $value;
    }
}
